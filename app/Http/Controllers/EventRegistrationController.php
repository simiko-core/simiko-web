<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Feed;
use App\Models\PaymentTransaction;
use App\Models\AnonymousEventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class EventRegistrationController extends Controller
{
    /**
     * Show event registration page
     */
    public function show($token)
    {
        $event = Feed::where('registration_token', $token)
            ->where('is_paid', true)
            ->where('type', 'event')
            ->with(['unitKegiatan', 'paymentConfiguration'])
            ->firstOrFail();

        // Debug information (remove this in production)
        if (config('app.debug')) {
            \Illuminate\Support\Facades\Log::info('Event Registration Debug', [
                'event_id' => $event->id,
                'event_title' => $event->title,
                'event_date' => $event->event_date,
                'current_time' => now(),
                'is_past' => $event->event_date ? $event->event_date->isPast() : 'No date',
                'registration_deadline' => $this->getRegistrationDeadline($event),
                'is_registration_open' => $this->isRegistrationOpen($event),
                'max_participants' => $event->max_participants,
                'current_registrations' => $event->getTotalRegistrationsCount(),
            ]);
        }

        // Check if event registration is still open
        if (!$this->isRegistrationOpen($event)) {
            $reason = 'date_passed';
            return view('event-registration.closed', compact('event', 'reason'));
        }

        // Check if event has reached maximum capacity
        if ($event->max_participants) {
            $currentRegistrations = $event->getTotalRegistrationsCount();
            if ($currentRegistrations >= $event->max_participants) {
                $reason = 'capacity_full';
                return view('event-registration.closed', compact('event', 'reason'));
            }
        }

        // Calculate capacity information
        $capacityInfo = $this->getCapacityInfo($event);

        return view('event-registration.show', compact('event', 'capacityInfo'));
    }

    /**
     * Process registration
     */
    public function register(Request $request, $token)
    {
        $event = Feed::where('registration_token', $token)
            ->where('is_paid', true)
            ->where('type', 'event')
            ->with(['unitKegiatan', 'paymentConfiguration'])
            ->firstOrFail();

        // Check if event registration is still open
        if (!$this->isRegistrationOpen($event)) {
            return back()->with('error', 'Maaf, pendaftaran untuk event ini sudah ditutup karena tanggal event sudah berlalu.');
        }

        // Validate basic registration data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Validate custom fields from payment configuration
        $customFieldErrors = $this->validateCustomFields($request, $event->paymentConfiguration);
        if (!empty($customFieldErrors)) {
            return back()->withErrors($customFieldErrors)->withInput();
        }

        try {
            // Check if event has reached maximum capacity before processing registration
            if ($event->max_participants) {
                $currentRegistrations = $event->getTotalRegistrationsCount();
                if ($currentRegistrations >= $event->max_participants) {
                    return back()->with('error', 'Maaf, event ini sudah mencapai kapasitas maksimum peserta.');
                }
            }

            // Check if user already registered for this event by email
            $existingAnonymousRegistration = AnonymousEventRegistration::where('email', $request->email)
                ->where('feed_id', $event->id)
                ->first();

            if ($existingAnonymousRegistration) {
                $existingTransaction = PaymentTransaction::where('anonymous_registration_id', $existingAnonymousRegistration->id)
                    ->whereIn('status', ['pending', 'paid'])
                    ->first();

                if ($existingTransaction) {
                    return redirect()->route('event.payment', [
                        'token' => $token,
                        'transactionId' => $existingTransaction->transaction_id
                    ])->with('info', 'You are already registered for this event.');
                }
            }

            // Create anonymous registration record
            $anonymousRegistration = $this->createAnonymousRegistration($event, $request);

            // Create payment transaction
            $transaction = $this->createTransactionForAnonymous($anonymousRegistration, $event, $request);

            return redirect()->route('event.payment', [
                'token' => $token,
                'transactionId' => $transaction->transaction_id
            ])->with('success', 'Registration successful! Please proceed with payment.');
        } catch (\Exception $e) {
            return back()->with('error', 'Registration failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show payment page
     */
    public function showPayment($token, $transactionId)
    {
        $event = Feed::where('registration_token', $token)
            ->where('is_paid', true)
            ->where('type', 'event')
            ->with(['unitKegiatan', 'paymentConfiguration'])
            ->firstOrFail();

        $transaction = PaymentTransaction::where('transaction_id', $transactionId)
            ->where('feed_id', $event->id)
            ->with(['anonymousRegistration'])
            ->firstOrFail();

        // Calculate capacity information
        $capacityInfo = $this->getCapacityInfo($event);

        return view('event-registration.payment', compact('event', 'transaction', 'capacityInfo'));
    }

    /**
     * Upload proof of payment
     */
    public function uploadProof(Request $request, $token, $transactionId)
    {
        $validator = Validator::make($request->all(), [
            'proof_file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:4096', // 4MB max
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $event = Feed::where('registration_token', $token)
            ->where('is_paid', true)
            ->where('type', 'event')
            ->firstOrFail();

        $transaction = PaymentTransaction::where('transaction_id', $transactionId)
            ->where('feed_id', $event->id)
            ->firstOrFail();

        if ($transaction->status !== 'pending') {
            return back()->with('error', 'Transaction cannot be updated.');
        }

        try {
            // Upload proof file
            $proofPath = $request->file('proof_file')->store('payment_proofs', 'public');

            $transaction->update([
                'proof_of_payment' => $proofPath,
                'notes' => 'Proof uploaded via registration page'
            ]);

            return redirect()->route('event.status', [$token, $transactionId])
                ->with('success', 'Proof of payment uploaded successfully! Please wait for admin verification.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to upload proof of payment.');
        }
    }

    /**
     * Check registration status
     */
    public function status($token, $transactionId)
    {
        $event = Feed::where('registration_token', $token)
            ->where('is_paid', true)
            ->where('type', 'event')
            ->firstOrFail();

        $transaction = PaymentTransaction::where('transaction_id', $transactionId)
            ->where('feed_id', $event->id)
            ->with(['anonymousRegistration'])
            ->firstOrFail();

        // Calculate capacity information
        $capacityInfo = $this->getCapacityInfo($event);

        return view('event-registration.status', compact('event', 'transaction', 'capacityInfo'));
    }

    /**
     * Download PDF receipt for paid transaction
     */
    public function downloadReceipt($token, $transactionId)
    {
        $event = Feed::where('registration_token', $token)
            ->where('is_paid', true)
            ->where('type', 'event')
            ->firstOrFail();

        $transaction = PaymentTransaction::where('transaction_id', $transactionId)
            ->where('feed_id', $event->id)
            ->where('status', 'paid') // Only allow download for paid transactions
            ->with(['anonymousRegistration', 'feed.unitKegiatan', 'paymentConfiguration'])
            ->firstOrFail();

        // Generate PDF
        $pdf = Pdf::loadView('pdf.transaction-receipt', compact('transaction'));

        // Set PDF options
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'DejaVu Sans'
        ]);

        // Generate filename
        $filename = 'receipt-' . $transaction->transaction_id . '.pdf';

        // Return PDF download response
        return $pdf->download($filename);
    }

    /**
     * Calculate registration deadline for an event
     * By default, allows registration until the end of the event date
     * Can be customized to close registration earlier if needed
     */
    private function getRegistrationDeadline($event)
    {
        if (!$event->event_date) {
            return null; // No deadline if no event date
        }

        // Default: Allow registration until the end of the event date (23:59:59)
        $deadline = $event->event_date->copy()->endOfDay();

        // Optional: Close registration 2 hours before event starts
        // Uncomment the line below if you want to close registration earlier
        // $deadline = $event->event_date->copy()->subHours(2);

        return $deadline;
    }

    /**
     * Check if event registration is still open
     */
    private function isRegistrationOpen($event)
    {
        $deadline = $this->getRegistrationDeadline($event);

        if (!$deadline) {
            return true; // No deadline set, always open
        }

        return now()->isBefore($deadline);
    }

    private function validateCustomFields(Request $request, $paymentConfiguration)
    {
        $errors = [];
        $customFields = $paymentConfiguration->sanitizeCustomFields($paymentConfiguration->custom_fields ?? []);

        foreach ($customFields as $field) {
            $fieldName = $field['name'];
            $value = $request->input("custom_data.{$fieldName}");

            // Check required fields
            if (($field['required'] ?? false) && empty($value)) {
                $errors["custom_data.{$fieldName}"] = "The {$field['label']} field is required.";
            }

            // Validate file uploads
            if ($field['type'] === 'file' && $request->hasFile("custom_files.{$fieldName}")) {
                $file = $request->file("custom_files.{$fieldName}");
                $fileErrors = $paymentConfiguration->validateFileUpload($fieldName, $file);
                if (!empty($fileErrors)) {
                    $errors["custom_files.{$fieldName}"] = $fileErrors;
                }
            }
        }

        return $errors;
    }

    private function createAnonymousRegistration($event, Request $request)
    {
        // Collect custom data
        $customData = $request->input('custom_data', []);

        // Upload custom files if provided
        $customFiles = [];
        if ($request->hasFile('custom_files')) {
            foreach ($request->file('custom_files') as $fieldName => $file) {
                $path = $file->store("custom_files/{$fieldName}", 'public');
                $customFiles[$fieldName] = $path;
            }
        }

        return AnonymousEventRegistration::create([
            'feed_id' => $event->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'custom_data' => $customData,
            'custom_files' => $customFiles,
        ]);
    }

    private function createTransactionForAnonymous($anonymousRegistration, $event, $request)
    {
        // Generate unique transaction ID
        $transactionId = 'TXN-' . $event->unitKegiatan->alias . '-' . time() . '-' . rand(1000, 9999);

        return PaymentTransaction::create([
            'unit_kegiatan_id' => $event->unit_kegiatan_id,
            'anonymous_registration_id' => $anonymousRegistration->id,
            'payment_configuration_id' => $event->payment_configuration_id,
            'feed_id' => $event->id,
            'transaction_id' => $transactionId,
            'amount' => $event->paymentConfiguration->amount,
            'currency' => $event->paymentConfiguration->currency,
            'status' => 'pending',
            'custom_data' => $anonymousRegistration->custom_data,
            'custom_files' => $anonymousRegistration->custom_files,
            'expires_at' => $event->event_date ? $event->event_date->subDay() : now()->addDays(7),
        ]);
    }

    private function getCapacityInfo($event)
    {
        $maxParticipants = $event->max_participants;
        $currentRegistrations = $event->getTotalRegistrationsCount();

        if ($maxParticipants === null) {
            return [
                'max_participants' => null,
                'current_registrations' => $currentRegistrations,
                'available_slots' => null,
                'is_unlimited' => true,
                'is_full' => false,
                'percentage_filled' => 0,
            ];
        }

        $availableSlots = max(0, $maxParticipants - $currentRegistrations);
        $percentageFilled = $maxParticipants > 0 ? ($currentRegistrations / $maxParticipants) * 100 : 0;

        return [
            'max_participants' => $maxParticipants,
            'current_registrations' => $currentRegistrations,
            'available_slots' => $availableSlots,
            'is_unlimited' => false,
            'is_full' => $currentRegistrations >= $maxParticipants,
            'percentage_filled' => round($percentageFilled, 1),
        ];
    }
}
