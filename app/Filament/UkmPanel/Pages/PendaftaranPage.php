<?php

namespace App\Filament\UkmPanel\Pages;

use App\Models\PendaftaranAnggota;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class PendaftaranPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.ukm-panel.pages.pendaftaran-page';
    protected static ?string $navigationLabel = 'Pendaftaran';
    protected static ?string $navigationGroup = 'Management Member';
    protected ?string $heading = 'Form Pendaftaran';
    protected ?string $subheading = 'Silakan isi form pendaftaran di bawah ini';
    public $unitKegiatan;
    public $isOpenPendaftaran;
    public $unitKegiatanId;

    public function mount(): void
    {
        $this->isOpenPendaftaran = Auth::user()->admin->unitKegiatan->open_registration;
        $this->unitKegiatan = Auth::user()->admin->unitKegiatan;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Toggle Pendaftaran')
                ->icon($this->isOpenPendaftaran ? 'heroicon-o-x-mark' : 'heroicon-o-check')
                ->color($this->isOpenPendaftaran ? 'danger' : 'success')
                ->label($this->isOpenPendaftaran ? 'Tutup Pendaftaran' : 'Buka Pendaftaran')
                ->action(function () {
                    $this->unitKegiatan->open_registration = !$this->unitKegiatan->open_registration;
                    $this->unitKegiatan->save();
                    $this->isOpenPendaftaran = $this->unitKegiatan->open_registration;
                    Notification::make()
                        ->title('Pendaftaran ' . ($this->isOpenPendaftaran ? 'dibuka' : 'ditutup'))
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
        ];
    }
}
