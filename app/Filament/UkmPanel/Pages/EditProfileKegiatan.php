<?php

namespace App\Filament\UkmPanel\Pages;

use App\Filament\Resources\UnitKegiatanResource\RelationManagers\UnitKegiatanProfileRelationManager;
use App\Filament\Resources\UnitKegiatanResource\RelationManagers\UnitKegiatanProfileRelationManagerAdminPanel;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class EditProfileKegiatan extends Page implements HasForms
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.ukm-panel.pages.edit-profile-kegiatan';
    protected static ?string $navigationGroup = 'Manajemen Kegiatan';
    protected static ?string $navigationLabel = 'Profil Kegiatan';
    protected ?string $heading = 'Profil Kegiatan';
    protected ?string $subheading = 'Custom Page Subheading';
    public ?array $data = [];
    public $record;


    // public function mount(): void
    // {
    //     $this->record = Auth::user()->admin->unitKegiatan;

    //     $this->form->fill([
    //         'name' => $this->record->name,
    //         'logo' => $this->record->logo ?? null,
    //     ]);
    // }

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


    // save
    public function save()
    {
        $this->validate();

        $this->record->update([
            'name' => $this->data['name'],
            'logo' => $this->data['logo'],
        ]);

        Notification::make()
            ->title('Profil Kegiatan Diperbarui')
            ->success()
            ->send();
    }
}
