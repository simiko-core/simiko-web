<?php

namespace App\Filament\Resources\UnitKegiatanResource\Pages;

use App\Filament\Resources\UnitKegiatanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnitKegiatan extends EditRecord
{
    protected static string $resource = UnitKegiatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
