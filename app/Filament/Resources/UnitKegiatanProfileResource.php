<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitKegiatanProfileResource\Pages;
use App\Filament\Resources\UnitKegiatanProfileResource\RelationManagers;
use App\Models\UnitKegiatanProfile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UnitKegiatanProfileResource extends Resource
{
    protected static ?string $model = UnitKegiatanProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Organization Management';

    protected static ?string $navigationLabel = 'Organization Profiles';

    protected static ?string $modelLabel = 'Organization Profile';

    protected static ?string $pluralModelLabel = 'Organization Profiles';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Profile Information')
                    ->description('Detailed profile information for the organization period')
                    ->schema([
                        Forms\Components\Select::make('unit_kegiatan_id')
                            ->label('Organization')
                            ->relationship('unitKegiatan', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Select which organization this profile belongs to'),

                        Forms\Components\TextInput::make('period')
                            ->label('Period Year')
                            ->numeric()
                            ->maxLength(4)
                            ->minLength(4)
                            ->required()
                            ->placeholder('2024')
                            ->helperText('Enter the year for this organization period'),

                        Forms\Components\RichEditor::make('description')
                            ->label('Description')
                            ->required()
                            ->placeholder('Describe the organization\'s purpose and activities...')
                            ->helperText('Detailed description of the organization')
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('vision')
                            ->label('Vision')
                            ->required()
                            ->placeholder('Enter the organization\'s vision statement...')
                            ->helperText('What the organization aspires to achieve')
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('mission')
                            ->label('Mission')
                            ->required()
                            ->placeholder('Enter the organization\'s mission statement...')
                            ->helperText('How the organization plans to achieve its vision')
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
                Tables\Columns\TextColumn::make('unitKegiatan.name')
                    ->label('Organization')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('unitKegiatan.alias')
                    ->label('Alias')
                    ->searchable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('period')
                    ->label('Period')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->wrap()
                    ->html(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('period', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('unit_kegiatan_id')
                    ->label('Organization')
                    ->relationship('unitKegiatan', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit Profile'),
                Tables\Actions\DeleteAction::make()
                    ->label('Delete Profile'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Selected'),
                ]),
            ])
            ->emptyStateHeading('No Organization Profiles')
            ->emptyStateDescription('Create detailed profiles for different organization periods.')
            ->emptyStateIcon('heroicon-o-document-text');
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
            'index' => Pages\ListUnitKegiatanProfiles::route('/'),
            'create' => Pages\CreateUnitKegiatanProfile::route('/create'),
            'edit' => Pages\EditUnitKegiatanProfile::route('/{record}/edit'),
        ];
    }
} 