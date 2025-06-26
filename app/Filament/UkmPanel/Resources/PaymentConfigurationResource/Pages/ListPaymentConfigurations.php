<?php

namespace App\Filament\UkmPanel\Resources\PaymentConfigurationResource\Pages;

use App\Filament\UkmPanel\Resources\PaymentConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentConfigurations extends ListRecords
{
    protected static string $resource = PaymentConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
} 