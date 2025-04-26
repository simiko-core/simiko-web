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
                // ->label(fn() => setting('registration_open') ? 'Tutup Pendaftaran' : 'Buka Pendaftaran')
                // ->icon(fn() => setting('registration_open') ? 'heroicon-m-x-mark' : 'heroicon-m-check')
                // ->color(fn() => setting('registration_open') ? 'danger' : 'success')
                ->action(function () {
                    // setting()->set('registration_open', !setting('registration_open'));
                    // setting()->save();
                }),
        ];
    }
}
