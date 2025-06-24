<?php

namespace App\Filament\UkmPanel\Pages;

use App\Models\PendaftaranAnggota;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class PendaftaranPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    
    protected static string $view = 'filament.ukm-panel.pages.pendaftaran-page';
    
    protected static ?string $navigationLabel = 'Member Registration';
    
    protected static ?string $navigationGroup = 'Member Management';
    
    protected static ?int $navigationSort = 1;
    
    protected ?string $heading = 'Member Registration Management';
    
    protected ?string $subheading = 'Manage new member applications and control registration status';
    
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
            Action::make('toggleRegistration')
                ->label($this->isOpenPendaftaran ? 'Close Registration' : 'Open Registration')
                ->icon($this->isOpenPendaftaran ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-open')
                ->color($this->isOpenPendaftaran ? 'danger' : 'success')
                ->action(function () {
                    $this->unitKegiatan->open_registration = !$this->unitKegiatan->open_registration;
                    $this->unitKegiatan->save();
                    $this->isOpenPendaftaran = $this->unitKegiatan->open_registration;
                    
                    Notification::make()
                        ->title($this->isOpenPendaftaran ? 'Registration Opened' : 'Registration Closed')
                        ->body($this->isOpenPendaftaran 
                            ? 'Students can now apply for membership' 
                            : 'New membership applications are now disabled'
                        )
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading($this->isOpenPendaftaran ? 'Close Registration?' : 'Open Registration?')
                ->modalDescription($this->isOpenPendaftaran 
                    ? 'This will prevent new students from applying for membership.' 
                    : 'This will allow students to submit membership applications.'
                )
        ];
    }
}
