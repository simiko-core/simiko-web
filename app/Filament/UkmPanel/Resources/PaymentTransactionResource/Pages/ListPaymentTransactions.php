<?php

namespace App\Filament\UkmPanel\Resources\PaymentTransactionResource\Pages;

use App\Filament\UkmPanel\Resources\PaymentTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentTransactions extends ListRecords
{
    protected static string $resource = PaymentTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make()
            //     ->label('Create Transaction'),
        ];
    }
}
