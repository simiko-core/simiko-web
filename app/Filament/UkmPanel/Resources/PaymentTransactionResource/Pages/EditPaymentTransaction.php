<?php

namespace App\Filament\UkmPanel\Resources\PaymentTransactionResource\Pages;

use App\Filament\UkmPanel\Resources\PaymentTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentTransaction extends EditRecord
{
    protected static string $resource = PaymentTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Delete Transaction'),
        ];
    }
} 