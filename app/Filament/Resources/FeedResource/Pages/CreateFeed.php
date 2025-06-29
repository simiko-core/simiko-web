<?php

namespace App\Filament\Resources\FeedResource\Pages;

use App\Filament\Resources\FeedResource;
use App\Models\PaymentConfiguration;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
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
                // Create the payment configuration
                $paymentConfig = PaymentConfiguration::create([
                    'unit_kegiatan_id' => $data['unit_kegiatan_id'],
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

        // Remove the new_payment_config data as it's not part of the Feed model
        unset($data['new_payment_config']);

        return $data;
    }
}
