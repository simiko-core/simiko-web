<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;
    protected static ?string $navigationIcon = "heroicon-o-calendar-days";
    protected static ?string $navigationGroup = "Manajemen Konten";
    protected static ?string $label = "Event";
    protected static ?string $pluralLabel = "Daftar Event";

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make("Terkait UKM")
                ->description("Pilih UKM yang menyelenggarakan event ini")
                ->schema([
                    Forms\Components\Select::make("unit_kegiatan_id")
                        ->relationship("unitKegiatan", "name")
                        ->required()
                        ->searchable()
                        ->preload()
                        ->label("Unit Kegiatan"),
                ])->collapsible(),



            Forms\Components\Section::make("Informasi Event")
                ->description("Lengkapi informasi event yang akan ditampilkan di aplikasi")
                ->schema([
                    Forms\Components\TextInput::make("name")
                        ->label("Nama Event")
                        ->required()
                        ->maxLength(100),

                    Forms\Components\DatePicker::make("event_date")
                        ->label("Tanggal Pelaksanaan")
                        ->required()

                        ->minDate(now()),
                    Forms\Components\TextInput::make("location")
                        ->label("Lokasi")
                        ->placeholder("Contoh: Online / Gedung A, Lantai 3"),
                    Forms\Components\Select::make("event_type")
                        ->label("Jenis Event")
                        ->options([
                            "online" => "Online",
                            "offline" => "Offline",
                        ])
                        ->required(),
                    Forms\Components\Select::make('categories')
                        ->label('Kategori Event')
                        ->relationship('categories', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                        ])
                        ->required(),
                    Forms\Components\FileUpload::make("poster")
                        ->label("Poster")
                        ->image()
                        ->imagePreviewHeight(200)
                        ->required()
                        ->preserveFilenames()
                        ->directory("event-posters")
                        ->maxSize(2 * 1024)
                        ->columnSpanFull(),

                    Forms\Components\Toggle::make("is_paid")
                        ->label("Event Berbayar")
                        ->helperText("Aktifkan jika peserta perlu membayar biaya pendaftaran")
                        ->reactive()
                        ->default(false),

                    Forms\Components\TextInput::make("price")
                        ->label("Harga Tiket")
                        ->numeric()
                        ->placeholder("Contoh: 50000")
                        ->visible(fn(callable $get) => $get('is_paid')),

                    Forms\Components\Repeater::make('payment_methods')
                        ->label("Metode Pembayaran")
                        ->schema([
                            Forms\Components\TextInput::make('method')
                                ->label('Metode (misal: Dana, BRI)')
                                ->required(),

                            Forms\Components\TextInput::make('account_number')
                                ->label('Nomor Rekening / Nomor HP')
                                ->required(),

                            Forms\Components\TextInput::make('account_name')
                                ->label('Nama Pemilik Rekening')
                                ->required(),
                        ])
                        ->visible(fn(callable $get) => $get('is_paid'))
                        ->defaultItems(1)
                        ->collapsible()
                        ->columnSpanFull()
                        ->columns(3),
                    Forms\Components\RichEditor::make("description")
                        ->label("Deskripsi Event")
                        ->required()
                        ->columnSpanFull(),
                ])
                ->columns(2)->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("name")
                    ->label("Nama Event")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make("unitKegiatan.name")
                    ->label("Unit Kegiatan")
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make("event_date")
                    ->label("Tanggal")
                    ->date("d M Y")
                    ->sortable(),
                Tables\Columns\TextColumn::make("event_type")
                    ->label("Jenis")
                    ->badge()
                    ->color(
                        fn(string $state): string => $state === "online"
                            ? "info"
                            : "success"
                    ),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make("event_type")
                    ->options([
                        "online" => "Online",
                        "offline" => "Offline",
                    ])
                    ->label("Jenis Event"),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()])
            ->emptyStateHeading("Belum Ada Event")
            ->emptyStateIcon("heroicon-o-calendar")
            ->emptyStateDescription("Tambahkan event baru untuk UKM kamu di sini.");
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListEvents::route("/"),
            "create" => Pages\CreateEvent::route("/create"),
            "edit" => Pages\EditEvent::route("/{record}/edit"),
        ];
    }
}
