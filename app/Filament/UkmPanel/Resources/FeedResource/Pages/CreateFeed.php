<?php

namespace App\Filament\UkmPanel\Resources\FeedResource\Pages;

use App\Filament\UkmPanel\Resources\FeedResource;
use App\Models\PaymentConfiguration;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CreateFeed extends CreateRecord
{
    protected static string $resource = FeedResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        Log::info('Feed Create: new_payment_config', [
            'new_payment_config' => $data['new_payment_config'] ?? null,
            'all_data' => $data,
        ]);
        // If this is a paid event and no payment configuration is selected, create a new one
        if ($data['type'] === 'event' && $data['is_paid'] && !isset($data['payment_configuration_id'])) {
            $newPaymentConfig = $data['new_payment_config'] ?? null;

            if ($newPaymentConfig && !empty($newPaymentConfig['amount'])) {
                // Force the name value if missing
                $newPaymentConfig['name'] = $newPaymentConfig['name'] ?? $data['title'] ?? 'Event Payment';

                // Process custom_fields to ensure each has a 'name' field
                if (!empty($newPaymentConfig['custom_fields'])) {
                    foreach ($newPaymentConfig['custom_fields'] as &$customField) {
                        if (empty($customField['name']) && !empty($customField['label'])) {
                            // Generate field name from label
                            $fieldName = strtolower(str_replace(' ', '-', $customField['label']));
                            // Remove any special characters except hyphens and underscores
                            $fieldName = preg_replace('/[^a-z0-9\-_]/', '', $fieldName);
                            $customField['name'] = $fieldName;
                        }
                    }
                    unset($customField); // Break the reference
                }

                // Get the current UKM admin's organization
                $ukmId = Auth::user()->admin->unit_kegiatan_id;
                // Create the payment configuration
                $paymentConfig = PaymentConfiguration::create([
                    'unit_kegiatan_id' => $ukmId,
                    'name' => $newPaymentConfig['name'],
                    'amount' => $newPaymentConfig['amount'],
                    'currency' => 'IDR',
                    'payment_methods' => $newPaymentConfig['payment_methods'] ?? [],
                    'custom_fields' => $newPaymentConfig['custom_fields'] ?? [],
                ]);

                // Set the payment configuration ID for the feed
                $data['payment_configuration_id'] = $paymentConfig->id;
            }
        }

        // Remove the new_payment_config data as it's not part of the Feed model
        unset($data['new_payment_config']);

        return $data;
    }
}
