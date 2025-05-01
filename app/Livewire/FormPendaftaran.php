<?php

namespace App\Livewire;

use App\Models\PendaftaranAnggota;
use Filament\Forms\Concerns\InteractsWithForms;
use Livewire\Component;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;

class FormPendaftaran extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Pendaftaran Anggota')
            ->emptyStateDescription('Tidak ada pendaftaran anggota yang ditemukan.')
            ->query(PendaftaranAnggota::query()->where('user_id', '!=', 1))
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Lengkap')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.phone')
                    ->label('Nomor Telepon')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                    })
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([])
            // ->headerActions([
            //     \Filament\Tables\Actions\CreateAction::make()
            //         ->icon('heroicon-o-plus')
            //         ->label('Tambah Pendaftaran')
            //         ->form([
            //             \Filament\Forms\Components\TextInput::make('name')
            //                 ->label('Nama Lengkap')
            //                 ->required()
            //                 ->placeholder('Masukkan nama lengkap'),

            //             \Filament\Forms\Components\TextInput::make('email')
            //                 ->label('Email')
            //                 ->email()
            //                 ->required()
            //                 ->placeholder('Masukkan email'),

            //             \Filament\Forms\Components\TextInput::make('phone'
            //                 ->label('Nomor Telepon')
            //                 ->tel()
            //                 ->required()
            //                 ->placeholder('Masukkan nomor telepon'),
            //         ])
            // ])
            ->actions([
                \Filament\Tables\Actions\Action::make('terima')
                    ->icon('heroicon-o-check')
                    ->label('Terima')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn(PendaftaranAnggota $record): bool => $record->status === 'pending')
                    ->action(function (PendaftaranAnggota $record) {
                        $record->update(['status' => 'accepted']);
                        Notification::make()
                            ->title('Pendaftaran Diterima')
                            ->success()
                            ->send();
                    }),

                \Filament\Tables\Actions\Action::make('tolak')
                    ->icon('heroicon-o-check')
                    ->label('Tolak')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn(PendaftaranAnggota $record): bool => $record->status === 'pending')
                    ->action(function (PendaftaranAnggota $record) {
                        $record->update(['status' => 'rejected']);
                        Notification::make()
                            ->title('Pendaftaran Diterima')
                            ->success()
                            ->send();
                    }),

                \Filament\Tables\Actions\Action::make('hapus')
                    ->icon('heroicon-o-trash')
                    ->label('Hapus')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn(PendaftaranAnggota $record): bool => $record->status === 'pending' || $record->status === 'rejected')
                    ->action(function (PendaftaranAnggota $record) {
                        $record->delete();
                        Notification::make()
                            ->title('Pendaftaran Dihapus')
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
        return view('livewire.form-pendaftaran');
    }
}
