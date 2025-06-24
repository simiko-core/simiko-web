<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Filament\Resources\BannerResource\RelationManagers;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static ?string $navigationIcon = "heroicon-o-megaphone";

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?string $navigationLabel = 'Promotional Banners';

    protected static ?string $modelLabel = 'Banner';

    protected static ?string $pluralModelLabel = 'Promotional Banners';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make("Banner Configuration")
                ->description(
                    "Create promotional banners from existing posts and events to highlight important content"
                )
                ->schema([
                    Forms\Components\Select::make("feed_id")
                        ->label("Select Content to Promote")
                        ->relationship("feed", "title", function (
                            Builder $query,
                            $livewire
                        ) {
                            $query->where(function ($q) use ($livewire) {
                                $q->doesntHave("banner");
                                if (
                                    $livewire instanceof
                                        \Filament\Resources\Pages\EditRecord &&
                                    $livewire->record
                                ) {
                                    $q->orWhere(
                                        "id",
                                        $livewire->record->feed_id
                                    );
                                }
                            });
                        })
                        ->getOptionLabelFromRecordUsing(fn ($record) => 
                            "{$record->unitKegiatan->alias} - {$record->title} ({$record->type})"
                        )
                        ->required()
                        ->searchable()
                        ->preload()
                        ->helperText('Choose a post or event to feature as a banner. Only content without existing banners is shown.'),

                    Forms\Components\Toggle::make('active')
                        ->label('Banner Active')
                        ->helperText('Enable to display this banner to users')
                        ->default(true),
                ])
                ->columns(2)
                ->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make("feed.image")
                    ->label("Preview")
                    ->disk('public')
                    ->square()
                    ->size(80),

                Tables\Columns\TextColumn::make("feed.unitKegiatan.name")
                    ->label("Organization")
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->color('info'),

                Tables\Columns\TextColumn::make("feed.title")
                    ->label("Content Title")
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\TextColumn::make("feed.type")
                    ->label("Type")
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'post' => 'info',
                        'event' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'post' => 'Post',
                        'event' => 'Event',
                        default => $state,
                    }),

                Tables\Columns\IconColumn::make('active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make("created_at")
                    ->label("Created")
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('active')
                    ->label('Banner Status')
                    ->placeholder('All banners')
                    ->trueLabel('Active banners')
                    ->falseLabel('Inactive banners'),

                Tables\Filters\SelectFilter::make('feed.type')
                    ->label('Content Type')
                    ->options([
                        'post' => 'Posts',
                        'event' => 'Events',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit Banner'),
                Tables\Actions\DeleteAction::make()
                    ->label('Remove Banner'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Remove Selected Banners'),
                ]),
            ])
            ->emptyStateHeading('No Promotional Banners')
            ->emptyStateDescription('Create banners to highlight important posts and events on the main page.')
            ->emptyStateIcon('heroicon-o-megaphone');
    }

    public static function getRelations(): array
    {
        return [
                //
            ];
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListBanners::route("/"),
            "create" => Pages\CreateBanner::route("/create"),
            "edit" => Pages\EditBanner::route("/{record}/edit"),
        ];
    }
}
