<?php

namespace App\Livewire;

use App\Models\UnitKegiatanProfile;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Livewire\Component;
use Filament\Tables\Table;

class ListUnitKegiatanProfile extends Component implements HasForms, HasTable
{

    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(UnitKegiatanProfile::query())
            ->columns([
                TextColumn::make('period')
                    ->label('Periode')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('vision')
                    ->label('Visi')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('mission')
                    ->label('Misi')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                \Filament\Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->action(function (UnitKegiatanProfile $record) {
                        $record->delete();
                        Notification::make()
                            ->title('Profile Kegiatan Dihapus')
                            ->success()
                            ->send();
                    }),
                \Filament\Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->modalHeading('Edit Profile Kegiatan')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('period')
                            ->label('Periode')
                            ->numeric()
                            ->maxLength(4)
                            ->required()
                            ->helperText('Masukkan periode kegiatan (misalnya: 2023).'),

                        \Filament\Forms\Components\Grid::make(2)
                            ->schema([
                                \Filament\Forms\Components\RichEditor::make('vision')
                                    ->label('Visi')
                                    ->required()
                                    ->helperText('Masukkan visi untuk unit kegiatan ini.'),

                                \Filament\Forms\Components\RichEditor::make('mission')
                                    ->label('Misi')
                                    ->required()
                                    ->helperText('Masukkan misi untuk unit kegiatan ini.'),

                            ]),

                        \Filament\Forms\Components\RichEditor::make('description')
                            ->label('Deskripsi')
                            ->required()
                            ->helperText('Deskripsi unit kegiatan yang lebih detail.')
                            ->columnSpanFull(),
                    ])
                    ->action(function (array $data, UnitKegiatanProfile $record) {
                        $record->update($data);
                        Notification::make()
                            ->title('Profile Kegiatan Diperbarui')
                            ->success()
                            ->send();
                    }),
            ])
            ->headerActions([
                \Filament\Tables\Actions\CreateAction::make()
                    ->label('Tambah Profile Kegiatan')
                    ->modalHeading('Tambah Profile Kegiatan')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('period')
                            ->label('Periode')
                            ->numeric()
                            ->maxLength(4)
                            ->required()
                            ->helperText('Masukkan periode kegiatan (misalnya: 2023).'),

                        \Filament\Forms\Components\Grid::make(2)
                            ->schema([
                                \Filament\Forms\Components\RichEditor::make('vision')
                                    ->label('Visi')
                                    ->required()
                                    ->helperText('Masukkan visi untuk unit kegiatan ini.'),

                                \Filament\Forms\Components\RichEditor::make('mission')
                                    ->label('Misi')
                                    ->required()
                                    ->helperText('Masukkan misi untuk unit kegiatan ini.'),

                            ]),

                        \Filament\Forms\Components\RichEditor::make('description')
                            ->label('Deskripsi')
                            ->required()
                            ->helperText('Deskripsi unit kegiatan yang lebih detail.')
                            ->columnSpanFull(),
                    ])
                    ->action(function (array $data) {
                        UnitKegiatanProfile::create($data);
                        Notification::make()
                            ->title('Profile Kegiatan Ditambahkan')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public function render()
    {
        return view('livewire.list-unit-kegiatan-profile');
    }
}
