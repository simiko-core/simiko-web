<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitKegiatanResource\Pages;
use App\Filament\Resources\UnitKegiatanResource\RelationManagers;
use App\Models\UnitKegiatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UnitKegiatanResource extends Resource
{
    protected static ?string $model = UnitKegiatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    
    protected static ?string $navigationGroup = 'Organization Management';
    
    protected static ?string $navigationLabel = 'UKM Organizations';

    protected static ?string $modelLabel = 'UKM Organization';

    protected static ?string $pluralModelLabel = 'UKM Organizations';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Organization Information')
                    ->description('Basic information about the Unit Kegiatan Mahasiswa (Student Activity Unit)')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Organization Name')
                            ->placeholder('e.g., Himpunan Mahasiswa Informatika')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Full official name of the student organization')
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('alias')
                            ->label('Short Name / Alias')
                            ->placeholder('e.g., HMIF, HMTE, UKM Foto')
                            ->required()
                            ->maxLength(50)
                            ->helperText('Short abbreviation or commonly used alias')
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('open_registration')
                            ->label('Registration Open')
                            ->helperText('Allow new students to register for this UKM')
                            ->default(true)
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Visual Identity')
                    ->description('Upload the organization logo and visual branding')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->label('Organization Logo')
                            ->image()
                            ->disk('public')
                            ->directory('logo_unit_kegiatan')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->maxSize(1024)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/svg+xml'])
                            ->helperText('Upload PNG, JPG, or SVG format. Maximum 1MB.')
                            ->required(),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->disk('public')
                    ->circular()
                    ->size(50),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Organization Name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->wrap(),
                    
                Tables\Columns\TextColumn::make('alias')
                    ->label('Alias')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\IconColumn::make('open_registration')
                    ->label('Registration')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('admins.user.name')
                    ->label('Administrator')
                    ->placeholder('No admin assigned')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('open_registration')
                    ->label('Registration Status')
                    ->placeholder('All organizations')
                    ->trueLabel('Open for registration')
                    ->falseLabel('Closed for registration'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit Organization'),
                Tables\Actions\DeleteAction::make()
                    ->label('Delete Organization'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Selected Organizations'),
                ]),
            ])
            ->emptyStateHeading('No UKM Organizations')
            ->emptyStateDescription('Create the first student organization to get started.')
            ->emptyStateIcon('heroicon-o-building-office-2');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UnitKegiatanProfileRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnitKegiatans::route('/'),
            'create' => Pages\CreateUnitKegiatan::route('/create'),
            'edit' => Pages\EditUnitKegiatan::route('/{record}/edit'),
        ];
    }
}
