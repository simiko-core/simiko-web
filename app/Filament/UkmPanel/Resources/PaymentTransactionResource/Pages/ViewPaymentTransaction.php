<?php

namespace App\Filament\UkmPanel\Resources\PaymentTransactionResource\Pages;

use App\Filament\UkmPanel\Resources\PaymentTransactionResource;
use App\Models\PaymentTransaction;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Barryvdh\DomPDF\Facade\Pdf;

class ViewPaymentTransaction extends ViewRecord
{
    protected static string $resource = PaymentTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make()
            //     ->label('Edit Transaction'),

            // add custom action to approve the transaction
            Actions\Action::make('approve')
                ->label('Approve Transaction')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Approve Payment Transaction')
                ->modalDescription(function (PaymentTransaction $record) {
                    return "Are you sure you want to approve this payment transaction for {$record->getUserName()}? This action will mark the transaction as paid and cannot be undone.";
                })
                ->modalSubmitActionLabel('Yes, Approve Transaction')
                ->modalCancelActionLabel('Cancel')
                ->visible(fn(PaymentTransaction $record): bool => $record->status === 'pending')
                ->action(function (PaymentTransaction $record) {
                    $record->markAsPaid();
                })
                ->after(function () {
                    // Refresh the page data to update the appearance
                    $this->refreshFormData([
                        'status',
                        'paid_at',
                    ]);
                })
                ->successNotificationTitle('Transaction Approved')
                ->successNotification(function (PaymentTransaction $record) {
                    return "Payment transaction for {$record->getUserName()} has been successfully approved.";
                }),

            Actions\Action::make('print_pdf')
                ->label('Print Receipt')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->visible(fn(PaymentTransaction $record): bool => $record->status === 'paid')
                ->action(function (PaymentTransaction $record) {
                    return $this->generatePDF($record);
                }),
        ];
    }

    protected function generatePDF(PaymentTransaction $transaction)
    {
        // Load transaction with all necessary relationships
        $transaction->load(['anonymousRegistration', 'feed.unitKegiatan', 'paymentConfiguration']);

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
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
