<?php

namespace App\Filament\UkmPanel\Resources\PendaftaranAnggotaResource\Pages;

use App\Filament\UkmPanel\Resources\PendaftaranAnggotaResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListPendaftaranAnggotas extends ListRecords
{
    protected static string $resource = PendaftaranAnggotaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('toggle-registration')
                ->action(function () {}),
        ];
    }
}
