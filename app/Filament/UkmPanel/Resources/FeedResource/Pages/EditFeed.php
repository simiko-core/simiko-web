<?php

namespace App\Filament\UkmPanel\Resources\FeedResource\Pages;

use App\Filament\UkmPanel\Resources\FeedResource;
use App\Models\PaymentConfiguration;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditFeed extends EditRecord
{
    protected static string $resource = FeedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // If this is a paid event and no payment configuration is selected, create a new one
        if ($data['type'] === 'event' && $data['is_paid'] && !isset($data['payment_configuration_id'])) {
            $newPaymentConfig = $data['new_payment_config'] ?? null;

            if ($newPaymentConfig && !empty($newPaymentConfig['name']) && !empty($newPaymentConfig['amount'])) {
                // Get the current UKM admin's organization
                $ukmId = Auth::user()->admin->unit_kegiatan_id;

                // Create the payment configuration
                $paymentConfig = PaymentConfiguration::create([
                    'unit_kegiatan_id' => $ukmId,
                    'name' => $newPaymentConfig['name'],
                    'description' => $newPaymentConfig['description'] ?? null,
                    'amount' => $newPaymentConfig['amount'],
                    'currency' => 'IDR',
                    'payment_methods' => $newPaymentConfig['payment_methods'] ?? [],
                    'custom_fields' => $newPaymentConfig['custom_fields'] ?? [],
                    'settings' => [],
                ]);

                // Set the payment configuration ID for the feed
                $data['payment_configuration_id'] = $paymentConfig->id;
            }
        }

        // Handle turning off Paid Event: disassociate and delete payment config if not used elsewhere
        if (
            isset($data['is_paid']) &&
            $data['is_paid'] == false &&
            $this->record &&
            $this->record->payment_configuration_id
        ) {
            $paymentConfig = $this->record->paymentConfiguration;
            if ($paymentConfig) {
                // Check if used by other feeds or has transactions
                $otherFeedsCount = $paymentConfig->feeds()->where('id', '!=', $this->record->id)->count();
                $transactionsCount = $paymentConfig->transactions()->count();
                if ($otherFeedsCount == 0 && $transactionsCount == 0) {
                    $paymentConfig->delete();
                }
            }
            $data['payment_configuration_id'] = null;
        }

        // Handle inline payment configuration editing
        if (isset($data['payment_configuration']) && $this->record && $this->record->paymentConfiguration) {
            $paymentConfigData = $data['payment_configuration'];

            // Update the existing payment configuration
            $this->record->paymentConfiguration->update([
                'name' => $paymentConfigData['name'] ?? $this->record->paymentConfiguration->name,
                'description' => $paymentConfigData['description'] ?? $this->record->paymentConfiguration->description,
                'amount' => $paymentConfigData['amount'] ?? $this->record->paymentConfiguration->amount,
                'currency' => 'IDR',
                'payment_methods' => $paymentConfigData['payment_methods'] ?? $this->record->paymentConfiguration->payment_methods,
                'custom_fields' => $paymentConfigData['custom_fields'] ?? $this->record->paymentConfiguration->custom_fields,
            ]);
        }

        // Prevent changing payment configuration for existing events
        $record = $this->record;
        if ($record && $record->payment_configuration_id && isset($data['payment_configuration_id'])) {
            if ($data['payment_configuration_id'] !== $record->payment_configuration_id) {
                // Keep the original payment configuration ID
                $data['payment_configuration_id'] = $record->payment_configuration_id;
            }
        }

        // Remove the payment configuration data as it's not part of the Feed model
        unset($data['payment_configuration']);
        unset($data['new_payment_config']);

        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load the current payment configuration data for inline editing
        if ($this->record && $this->record->paymentConfiguration) {
            $data['payment_configuration'] = [
                'name' => $this->record->paymentConfiguration->name,
                'description' => $this->record->paymentConfiguration->description,
                'amount' => $this->record->paymentConfiguration->amount,
                'payment_methods' => $this->record->paymentConfiguration->payment_methods,
                'custom_fields' => $this->record->paymentConfiguration->custom_fields,
            ];
        }

        return $data;
    }
}
