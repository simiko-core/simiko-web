<?php

namespace App\Filament\UkmPanel\Resources;

use App\Filament\UkmPanel\Resources\PendaftaranAnggotaResource\Pages;
use App\Filament\UkmPanel\Resources\PendaftaranAnggotaResource\RelationManagers;
use App\Models\PendaftaranAnggota;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PendaftaranAnggotaResource extends Resource
{
    protected static ?string $model = PendaftaranAnggota::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Member Management';

    protected static ?string $navigationLabel = 'Pendaftaran Anggota';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\Select::make('user_id')
                //     ->relationship('user', 'name')
                //     ->preload()
                //     ->required(),
                // Forms\Components\Select::make('unit_kegiatan_id')
                //     ->relationship('unitKegiatan', 'name')
                //     ->preload()
                //     ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListPendaftaranAnggotas::route('/'),
            'create' => Pages\CreatePendaftaranAnggota::route('/create'),
            'edit' => Pages\EditPendaftaranAnggota::route('/{record}/edit'),
        ];
    }
}
