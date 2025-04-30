<?php

namespace App\Livewire;

use App\Models\UnitKegiatan;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ProfileKegiatan extends Component implements HasForms
{
    use InteractsWithForms;
    public ?array $data = [];
    public $record;


    public function mount(): void
    {
        $unitKegiatan = Auth::user()->admin->unitKegiatan;
        $this->form->fill($unitKegiatan->toArray());
    }

    public function update(): void
    {
        $unitKegiatan = Auth::user()->admin->unitKegiatan;
        $unitKegiatan->update($this->form->getState());

        Notification::make()
            ->title('Profil Kegiatan Diperbarui')
            ->body('Profil kegiatan Anda telah diperbarui.')
            ->success()
            ->send();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Dasar')
                    ->description('Lengkapi profil kegiatan unit Anda.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Kegiatan')
                            ->placeholder('Masukkan nama kegiatan')
                            ->required(),

                        FileUpload::make('logo')
                            ->label('Upload Logo')
                            ->image()
                            ->disk('public')
                            ->directory('logo_unit_kegiatan')
                            ->preserveFilenames()
                            ->maxSize(1024)
                            ->required()
                            ->helperText('Format: JPG, PNG. Maksimal 1MB.'),

                    ])
                    ->columns(2)
                    ->collapsible(),
            ])
            ->statePath('data');
    }


    public function render()
    {
        return view('livewire.profile-kegiatan');
    }
}
