<?php

namespace App\Filament\UkmPanel\Resources\FeedResource\Pages;

use App\Filament\UkmPanel\Resources\FeedResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeeds extends ListRecords
{
    protected static string $resource = FeedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
