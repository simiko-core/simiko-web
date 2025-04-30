<?php

namespace App\Filament\Resources\UnitKegiatanProfileResource\Pages;

use App\Filament\Resources\UnitKegiatanProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnitKegiatanProfile extends EditRecord
{
    protected static string $resource = UnitKegiatanProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
