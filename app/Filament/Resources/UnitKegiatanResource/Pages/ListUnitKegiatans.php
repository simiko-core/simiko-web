<?php

namespace App\Filament\Resources\UnitKegiatanResource\Pages;

use App\Filament\Resources\UnitKegiatanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUnitKegiatans extends ListRecords
{
    protected static string $resource = UnitKegiatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
