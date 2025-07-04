<?php

namespace App\Filament\UkmPanel\Resources;

use App\Filament\UkmPanel\Resources\ActivityGalleryResource\Pages;
use App\Filament\UkmPanel\Resources\ActivityGalleryResource\RelationManagers;
use App\Models\ActivityGallery;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivityGalleryResource extends Resource
{
    protected static ?string $model = ActivityGallery::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Organization Profile';

    protected static ?string $navigationLabel = 'Activity Gallery';

    protected static ?string $modelLabel = 'Gallery Photo';

    protected static ?string $pluralModelLabel = 'Activity Gallery';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Photo Gallery')
                    ->description('Share photos from your organization\'s activities, events, and memorable moments')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Activity Photo')
                            ->image()
                            ->disk('public')
                            ->directory('activity_galleries')
                            ->visibility('public')
                            ->required()
                            ->preserveFilenames()
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif'])
                            ->helperText('Upload high-quality photos from your activities (Max 2MB)')
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('caption')
                            ->label('Photo Caption')
                            ->maxLength(100)
                            ->placeholder('e.g., Annual Tech Workshop 2024 - Group Photo')
                            ->helperText('Add a descriptive caption to give context to the photo. You can use formatting for better presentation.')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Photo')
                    ->disk('public')
                    ->square()
                    ->size(80),

                Tables\Columns\TextColumn::make('caption')
                    ->label('Caption')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(60)
                    ->placeholder('No caption')
                    ->html()
                    ->formatStateUsing(fn(?string $state): string => $state ? strip_tags($state) : ''),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit Photo'),
                Tables\Actions\DeleteAction::make()
                    ->label('Delete Photo'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Selected'),
                ]),
            ])
            ->emptyStateHeading('No Photos Yet')
            ->emptyStateDescription('Start building your photo gallery by uploading pictures from your activities!')
            ->emptyStateIcon('heroicon-o-photo');
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
            'index' => Pages\ListActivityGalleries::route('/'),
            'create' => Pages\CreateActivityGallery::route('/create'),
            'edit' => Pages\EditActivityGallery::route('/{record}/edit'),
        ];
    }
}
