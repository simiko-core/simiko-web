<?php

namespace App\Filament\Widgets;

use App\Models\Feed;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentActivityWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Feed::query()
                    ->with('unitKegiatan:id,alias')
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Content')
                    ->limit(40)
                    ->searchable(),
                TextColumn::make('unitKegiatan.alias')
                    ->label('UKM')
                    ->badge(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'post' => 'info',
                        'event' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable(),
            ])
            ->paginated(false);
    }
} 