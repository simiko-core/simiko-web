<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\PaymentConfiguration;
use App\Models\PaymentTransaction;
use App\Models\Feed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class paymentController extends Controller
{
    /**
     * Get payment configurations for a UKM
     */
    public function getConfigurations(Request $request)
    {
        try {
            $ukmId = $request->input('ukm_id');

            if (!$ukmId) {
                return ApiResponse::error('UKM ID is required', 400);
            }

            $configurations = PaymentConfiguration::where('unit_kegiatan_id', $ukmId)
                ->where('is_active', true)
                ->get();

            return ApiResponse::success($configurations, 'Payment configurations retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse::serverError('Failed to retrieve payment configurations');
        }
    }

    /**
     * Create a new payment transaction
     */
    public function createTransaction(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'payment_configuration_id' => 'required|exists:payment_configurations,id',
                'feed_id' => 'nullable|exists:feeds,id',
                'custom_data' => 'nullable|array',
                'custom_files' => 'nullable|array',
                'custom_files.*' => 'nullable|file|image|max:10240', // 10MB max for images
            ]);

            if ($validator->fails()) {
                return ApiResponse::validationError($validator->errors());
            }

            $user = $request->user();
            $configuration = PaymentConfiguration::findOrFail($request->payment_configuration_id);

            // Check if this is an event-linked transaction
            $feed = null;
            if ($request->feed_id) {
                $feed = Feed::findOrFail($request->feed_id);

                // Verify the feed is a paid event and has the correct payment configuration
                if (!$feed->is_paid || $feed->payment_configuration_id !== $configuration->id) {
                    return ApiResponse::error('Invalid event or payment configuration', 400);
                }
            }

            // Validate custom files if provided
            if ($request->hasFile('custom_files')) {
                $fileValidation = $this->validateCustomFiles($configuration, $request->file('custom_files'));
                if (!empty($fileValidation)) {
                    return ApiResponse::error('File validation failed', 400, $fileValidation);
                }
            }

            // Generate unique transaction ID
            $transactionId = 'TXN-' . $configuration->unitKegiatan->alias . '-' . time() . '-' . rand(1000, 9999);

            // Upload custom files if provided
            $customFiles = [];
            if ($request->hasFile('custom_files')) {
                $customFiles = $this->uploadCustomFiles($request->file('custom_files'));
            }

            $transaction = PaymentTransaction::create([
                'unit_kegiatan_id' => $configuration->unit_kegiatan_id,
                'user_id' => $user->id,
                'payment_configuration_id' => $configuration->id,
                'feed_id' => $feed ? $feed->id : null,
                'transaction_id' => $transactionId,
                'amount' => $configuration->amount,
                'currency' => $configuration->currency,
                'status' => 'pending',
                'custom_data' => $request->custom_data ?? [],
                'custom_files' => $customFiles,
                'expires_at' => now()->addDays(7), // 7 days expiry
            ]);

            return ApiResponse::success([
                'transaction' => [
                    'id' => $transaction->id,
                    'transaction_id' => $transaction->transaction_id,
                    'amount' => $transaction->amount,
                    'status' => $transaction->status,
                    'expires_at' => $transaction->expires_at,
                    'payment_configuration' => [
                        'name' => $configuration->name,
                        'description' => $configuration->description,
                        'payment_methods' => $configuration->payment_methods,
                    ],
                    'event' => $feed ? [
                        'id' => $feed->id,
                        'title' => $feed->title,
                        'event_date' => $feed->event_date,
                    ] : null,
                ]
            ], 'Transaction created successfully', 201);
        } catch (\Exception $e) {
            return ApiResponse::serverError('Failed to create transaction: ' . $e->getMessage());
        }
    }

    /**
     * Get user's payment transactions
     */
    public function getUserTransactions(Request $request)
    {
        try {
            $transactions = PaymentTransaction::where('user_id', auth()->id())
                ->with(['paymentConfiguration', 'unitKegiatan'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($transaction) {
                    return [
                        'id' => $transaction->id,
                        'transaction_id' => $transaction->transaction_id,
                        'amount' => $transaction->amount,
                        'status' => $transaction->status,
                        'payment_method' => $transaction->payment_method,
                        'notes' => $transaction->notes,
                        'paid_at' => $transaction->paid_at,
                        'expires_at' => $transaction->expires_at,
                        'created_at' => $transaction->created_at,
                        'payment_configuration' => [
                            'id' => $transaction->paymentConfiguration->id,
                            'name' => $transaction->paymentConfiguration->name,
                            'description' => $transaction->paymentConfiguration->description,
                            'amount' => $transaction->paymentConfiguration->amount,
                        ],
                        'unit_kegiatan' => [
                            'id' => $transaction->unitKegiatan->id,
                            'name' => $transaction->unitKegiatan->name,
                            'alias' => $transaction->unitKegiatan->alias,
                        ]
                    ];
                });

            return ApiResponse::success($transactions, 'User transactions retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse::serverError('Failed to retrieve user transactions');
        }
    }

    /**
     * Upload proof of payment
     */
    public function uploadProof(Request $request, $transactionId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'proof_file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:4096', // 4MB max
            ]);

            if ($validator->fails()) {
                return ApiResponse::validationError($validator->errors());
            }

            $transaction = PaymentTransaction::where('transaction_id', $transactionId)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            if ($transaction->status !== 'pending') {
                return ApiResponse::error('Transaction cannot be updated', 400);
            }

            // Upload proof file
            $proofPath = $request->file('proof_file')->store('payment_proofs', 'public');

            $transaction->update([
                'proof_of_payment' => $proofPath,
            ]);

            return ApiResponse::success($transaction, 'Proof of payment uploaded successfully');
        } catch (\Exception $e) {
            return ApiResponse::serverError('Failed to upload proof of payment');
        }
    }

    /**
     * Validate custom files based on configuration
     */
    private function validateCustomFiles($configuration, $files)
    {
        $errors = [];
        $fileFields = $configuration->getFileFields();

        foreach ($files as $fieldName => $file) {
            if (!isset($fileFields[$fieldName])) {
                $errors[$fieldName] = ['Invalid file field'];
                continue;
            }

            $fieldErrors = $configuration->validateFileUpload($fieldName, $file);
            if (!empty($fieldErrors)) {
                $errors[$fieldName] = $fieldErrors;
            }
        }

        return $errors;
    }

    /**
     * Upload custom files
     */
    private function uploadCustomFiles($files)
    {
        $uploadedFiles = [];

        foreach ($files as $fieldName => $file) {
            $path = $file->store("custom_files/{$fieldName}", 'public');
            $uploadedFiles[$fieldName] = $path;
        }

        return $uploadedFiles;
    }
}
