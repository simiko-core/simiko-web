<?php

namespace App\Filament\UkmPanel\Resources\PendaftaranAnggotaResource\Pages;

use App\Filament\UkmPanel\Resources\PendaftaranAnggotaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPendaftaranAnggota extends EditRecord
{
    protected static string $resource = PendaftaranAnggotaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
