<?php

namespace App\Filament\UkmPanel\Resources;

use App\Filament\UkmPanel\Resources\AchievementResource\Pages;
use App\Filament\UkmPanel\Resources\AchievementResource\RelationManagers;
use App\Models\Achievement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AchievementResource extends Resource
{
    protected static ?string $model = Achievement::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationGroup = 'Organization Profile';

    protected static ?string $navigationLabel = 'Achievements';

    protected static ?string $modelLabel = 'Achievement';

    protected static ?string $pluralModelLabel = 'Achievements';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Achievement Information')
                    ->description('Showcase your organization\'s accomplishments, awards, and recognition')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Achievement Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., 1st Place National Programming Competition 2024')
                            ->helperText('Enter a clear and descriptive title for this achievement'),

                        Forms\Components\FileUpload::make('image')
                            ->label('Achievement Photo')
                            ->image()
                            ->disk('public')
                            ->directory('achievements')
                            ->visibility('public')
                            ->required()
                            ->preserveFilenames()
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif'])
                            ->helperText('Upload a photo of the certificate, trophy, or award ceremony (Max 2MB)'),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->maxLength(1000)
                            ->rows(4)
                            ->placeholder('Describe the achievement, competition details, and significance...')
                            ->helperText('Provide context about this achievement (max 1000 characters)')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
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
                    ->size(60),

                Tables\Columns\TextColumn::make('title')
                    ->label('Achievement Title')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->wrap()
                    ->limit(60),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->limit(80)
                    ->wrap()
                    ->placeholder('No description'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Added')
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
                    ->label('Edit Achievement'),
                Tables\Actions\DeleteAction::make()
                    ->label('Delete Achievement'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Selected'),
                ]),
            ])
            ->emptyStateHeading('No Achievements Yet')
            ->emptyStateDescription('Start adding your organization\'s achievements to showcase your success!')
            ->emptyStateIcon('heroicon-o-trophy');
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
            'index' => Pages\ListAchievements::route('/'),
            'create' => Pages\CreateAchievement::route('/create'),
            'edit' => Pages\EditAchievement::route('/{record}/edit'),
        ];
    }
}
