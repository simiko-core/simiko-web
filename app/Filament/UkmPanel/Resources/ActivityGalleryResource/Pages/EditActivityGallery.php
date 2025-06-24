<?php

namespace App\Filament\UkmPanel\Resources\ActivityGalleryResource\Pages;

use App\Filament\UkmPanel\Resources\ActivityGalleryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActivityGallery extends EditRecord
{
    protected static string $resource = ActivityGalleryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
