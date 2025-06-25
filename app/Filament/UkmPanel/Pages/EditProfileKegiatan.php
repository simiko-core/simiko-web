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
use Filament\Forms\Components\Toggle;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class EditProfileKegiatan extends Page implements HasForms
{
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    
    protected static string $view = 'filament.ukm-panel.pages.edit-profile-kegiatan';
    
    protected static ?string $navigationGroup = 'Organization Settings';
    
    protected static ?string $navigationLabel = 'Organization Profile';
    
    protected static ?int $navigationSort = 1;
    
    protected ?string $heading = 'Organization Profile Management';
    
    protected ?string $subheading = 'Update your organization\'s basic information and branding';
    
    public ?array $data = [];
    public $record;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->description('Update your organization\'s name and visual identity.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Organization Name')
                            ->placeholder('Enter organization name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Full official name of your organization'),

                        TextInput::make('alias')
                            ->label('Short Name / Alias')
                            ->placeholder('e.g., HMIF, HMTE, UKM Foto')
                            ->required()
                            ->maxLength(50)
                            ->helperText('Short abbreviation commonly used'),

                        Select::make('category')
                            ->label('Category')
                            ->options([
                                'Himpunan' => 'Himpunan (Academic Department)',
                                'UKM Olahraga' => 'UKM Olahraga (Sports)',
                                'UKM Seni' => 'UKM Seni (Arts & Culture)',
                                'UKM Keagamaan' => 'UKM Keagamaan (Religious)',
                                'UKM Keilmuan' => 'UKM Keilmuan (Academic)',
                                'UKM Kemasyarakatan' => 'UKM Kemasyarakatan (Community Service)',
                                'UKM Kewirausahaan' => 'UKM Kewirausahaan (Entrepreneurship)',
                                'UKM Teknologi' => 'UKM Teknologi (Technology)',
                                'UKM Media' => 'UKM Media (Media & Communication)',
                                'Lainnya' => 'Other',
                            ])
                            ->required()
                            ->searchable()
                            ->helperText('Select the type/category of your organization'),

                        FileUpload::make('logo')
                            ->label('Organization Logo')
                            ->image()
                            ->disk('public')
                            ->directory('logo_unit_kegiatan')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->maxSize(1024)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/svg+xml'])
                            ->helperText('Upload PNG, JPG, or SVG format. Maximum 1MB.')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Registration Settings')
                    ->description('Control whether new students can apply for membership.')
                    ->schema([
                        Toggle::make('open_registration')
                            ->label('Accept New Members')
                            ->helperText('Enable to allow students to register for membership')
                            ->onIcon('heroicon-m-check')
                            ->offIcon('heroicon-m-x-mark')
                            ->onColor('success')
                            ->offColor('danger'),
                    ])
                    ->columns(1)
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
            'alias' => $this->data['alias'],
            'category' => $this->data['category'],
            'logo' => $this->data['logo'],
            'open_registration' => $this->data['open_registration'] ?? false,
        ]);

        Notification::make()
            ->title('Profile Updated Successfully')
            ->body('Your organization profile has been updated.')
            ->success()
            ->send();
    }
}
