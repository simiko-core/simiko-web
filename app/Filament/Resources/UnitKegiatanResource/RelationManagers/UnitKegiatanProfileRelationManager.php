<?php

namespace App\Filament\Resources\UnitKegiatanResource\RelationManagers;

use App\Models\UnitKegiatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UnitKegiatanProfileRelationManager extends RelationManager
{
    protected static string $relationship = 'UnitKegiatanProfile';

    protected static ?string $title = 'Profile Kegiatan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('period')
                    ->label('Periode')
                    ->numeric()
                    ->maxLength(4)
                    ->required()
                    ->helperText('Masukkan periode kegiatan (misalnya: 2023).')
                    ->columnSpan(2),

                Forms\Components\RichEditor::make('vision_mission')
                    ->label('Vision & Mission')
                    ->required()
                    ->helperText('Masukkan visi dan misi untuk unit kegiatan ini.')
                    ->columnSpanFull(),

                Forms\Components\RichEditor::make('description')
                    ->label('Description')
                    ->required()
                    ->helperText('Deskripsi unit kegiatan yang lebih detail.')
                    ->columnSpanFull(),

                Forms\Components\FileUpload::make('background_photo')
                    ->label('Background Photo')
                    ->image()
                    ->directory('unit_kegiatan_profiles/backgrounds')
                    ->visibility('public')
                    ->helperText('Upload a background image for the organization profile')
                    ->columnSpanFull(),
            ]);
    }
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('period')
            ->columns([
                Tables\Columns\TextColumn::make('period')
                    ->label('Periode')
                    ->numeric()
                    ->formatStateUsing(fn($state) => (int) $state)
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Buat Profile Baru')
                    ->icon('heroicon-o-plus')
            ])
            ->filters([])
            ->actions([
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
