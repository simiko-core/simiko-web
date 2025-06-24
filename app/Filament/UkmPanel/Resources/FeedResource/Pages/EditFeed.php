<?php

namespace App\Filament\UkmPanel\Resources\FeedResource\Pages;

use App\Filament\UkmPanel\Resources\FeedResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeed extends EditRecord
{
    protected static string $resource = FeedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
