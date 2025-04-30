<?php

namespace App\Filament\Resources\UnitKegiatanProfileResource\Pages;

use App\Filament\Resources\UnitKegiatanProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUnitKegiatanProfiles extends ListRecords
{
    protected static string $resource = UnitKegiatanProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
