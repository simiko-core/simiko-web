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

    protected static ?string $navigationIcon = "heroicon-o-rectangle-stack";

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make("Informasi Banner")
                ->description(
                    "Lengkapi informasi banner yang akan ditampilkan di aplikasi"
                )
                ->schema([
                    Forms\Components\Select::make("post_id")
                        ->relationship("post", "title", function (
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
                                        $livewire->record->post_id
                                    );
                                }
                            });
                        })
                        ->label("Post Terkait")
                        ->required()
                        ->searchable()
                        ->preload(),
                ])
                ->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("post.title")
                    ->label("Post Terkait")
                    ->searchable(),
                Tables\Columns\TextColumn::make("created_at")
                    ->label("Dibuat Pada")
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([Tables\Actions\DeleteAction::make()])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            "index" => Pages\ListBanners::route("/"),
            "create" => Pages\CreateBanner::route("/create"),
            "edit" => Pages\EditBanner::route("/{record}/edit"),
        ];
    }
}
