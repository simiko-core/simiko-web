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

                TextColumn::make('vision_mission')
                    ->label('Vision & Mission')
                    ->sortable()
                    ->searchable()
                    ->limit(50)
                    ->wrap()
                    ->html(),

                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->sortable()
                    ->searchable(),

                \Filament\Tables\Columns\ImageColumn::make('background_photo')
                    ->label('Background Photo')
                    ->square()
                    ->toggleable(isToggledHiddenByDefault: true),
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

                        \Filament\Forms\Components\RichEditor::make('vision_mission')
                            ->label('Vision & Mission')
                            ->required()
                            ->helperText('Masukkan visi dan misi untuk unit kegiatan ini.')
                            ->columnSpanFull(),

                        \Filament\Forms\Components\RichEditor::make('description')
                            ->label('Deskripsi')
                            ->required()
                            ->helperText('Deskripsi unit kegiatan yang lebih detail.')
                            ->columnSpanFull(),

                        \Filament\Forms\Components\FileUpload::make('background_photo')
                            ->label('Background Photo')
                            ->image()
                            ->directory('unit_kegiatan_profiles/backgrounds')
                            ->visibility('public')
                            ->helperText('Upload a background image for the organization profile')
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

                        \Filament\Forms\Components\RichEditor::make('vision_mission')
                            ->label('Vision & Mission')
                            ->required()
                            ->helperText('Masukkan visi dan misi untuk unit kegiatan ini.')
                            ->columnSpanFull(),

                        \Filament\Forms\Components\RichEditor::make('description')
                            ->label('Deskripsi')
                            ->required()
                            ->helperText('Deskripsi unit kegiatan yang lebih detail.')
                            ->columnSpanFull(),

                        \Filament\Forms\Components\FileUpload::make('background_photo')
                            ->label('Background Photo')
                            ->image()
                            ->directory('unit_kegiatan_profiles/backgrounds')
                            ->visibility('public')
                            ->helperText('Upload a background image for the organization profile')
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
