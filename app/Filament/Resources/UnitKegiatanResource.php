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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Unit Kegiatan';
    protected static ?string $navigationLabel = 'Unit Kegiatan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Unit Kegiatan')
                                    ->placeholder('Masukkan nama unit kegiatan')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Nama lengkap unit kegiatan (contoh: UKM Fotografi)'),

                                Forms\Components\TextInput::make('alias')
                                    ->label('Alias Unit Kegiatan')
                                    ->placeholder('Masukkan alias unit kegiatan')
                                    ->required()
                                    ->maxLength(50)
                                    ->helperText('Alias singkat unit kegiatan (contoh: HMIF)'),

                                Forms\Components\FileUpload::make('logo')
                                    ->label('Upload Logo')
                                    ->image()
                                    ->disk('public')
                                    ->directory('logo_unit_kegiatan')
                                    ->preserveFilenames()
                                    ->maxSize(1024)
                                    ->required()
                                    ->helperText('Format: JPG, PNG. Maksimal 1MB.'),
                            ]),
                    ])
                    ->columns(1)
                    ->columnSpanFull()
                // ->collapsed(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Unit Kegiatan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('alias')
                    ->label('Alias Unit Kegiatan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->disk('public')
                    ->circular(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Terpilih'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
