<?php

namespace App\Filament\UkmPanel\Resources;

use App\Filament\UkmPanel\Resources\FeedResource\Pages;
use App\Filament\UkmPanel\Resources\FeedResource\RelationManagers;
use App\Models\Feed;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FeedResource extends Resource
{
    protected static ?string $model = Feed::class;

    protected static ?string $navigationIcon = 'heroicon-o-rss';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?string $navigationLabel = 'Posts & Events';

    protected static ?string $modelLabel = 'Content';

    protected static ?string $pluralModelLabel = 'Posts & Events';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Content Type')
                    ->description('Choose what type of content you want to create')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Content Type')
                            ->options([
                                'post' => 'Post - Share news, announcements, or updates',
                                'event' => 'Event - Create activities, workshops, or competitions',
                            ])
                            ->required()
                            ->reactive()
                            ->helperText('Select the type of content you want to publish')
                            ->afterStateUpdated(fn($state, Forms\Set $set) => $set('event_date', null)),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Forms\Components\Section::make('Content Details')
                    ->description('Write your content title, description, and upload images')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter an engaging title for your content')
                            ->helperText('Make it clear and interesting to attract readers')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                // Only update config name if it matches the old title or is empty
                                $oldNewName = $get('new_payment_config.name');
                                if (!$oldNewName || $oldNewName === $get('title')) {
                                    $set('new_payment_config.name', $state);
                                }
                                $oldEditName = $get('payment_configuration.name');
                                if (!$oldEditName || $oldEditName === $get('title')) {
                                    $set('payment_configuration.name', $state);
                                }
                            }),

                        Forms\Components\RichEditor::make('content')
                            ->label('Content')
                            ->required()
                            ->placeholder('Write your content here...')
                            ->helperText('Use the toolbar to format your text with bold, italic, lists, etc.')
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('image')
                            ->label('Featured Image')
                            ->image()
                            ->disk('public')
                            ->directory('feeds')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif'])
                            ->helperText('Upload a high-quality image (JPG, PNG, GIF). Maximum 2MB.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Event Information')
                    ->description('Configure event-specific details like date, location, and pricing')
                    ->schema([
                        Forms\Components\DatePicker::make('event_date')
                            ->label('Event Date')
                            ->required()
                            ->minDate(now())
                            ->helperText('When will this event take place?'),

                        Forms\Components\Select::make('event_type')
                            ->label('Event Format')
                            ->options([
                                'online' => 'Online - Virtual event (Zoom, Teams, etc.)',
                                'offline' => 'Offline - Physical location',
                            ])
                            ->required()
                            ->helperText('Choose whether this is an online or offline event'),

                        Forms\Components\TextInput::make('location')
                            ->label('Location')
                            ->placeholder('e.g., Zoom Meeting Room / Building A, Room 301')
                            ->helperText('Specify where participants should go or join')
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_paid')
                            ->label('Paid Event')
                            ->helperText('Turn on if this event requires payment from participants')
                            ->reactive()
                            ->columnSpanFull(),

                        Forms\Components\Section::make('Create New Payment Configuration')
                            ->description('Create a new payment configuration for this event')
                            ->schema([
                                Forms\Components\TextInput::make('new_payment_config.name')
                                    ->label('Configuration Name')
                                    ->default(fn(callable $get) => $get('title'))
                                    ->reactive()
                                    ->hidden()
                                    ->dehydrated(true)
                                    ->required(),

                                Forms\Components\TextInput::make('new_payment_config.amount')
                                    ->label('Amount')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->placeholder('50000')
                                    ->helperText('Enter the payment amount in Indonesian Rupiah'),
                                Forms\Components\Textarea::make('new_payment_config.description')
                                    ->label('Description')
                                    ->rows(2)
                                    ->placeholder('Describe what this payment is for...')
                                    ->helperText('Optional description'),



                                Forms\Components\Repeater::make('new_payment_config.payment_methods')
                                    ->label('Payment Methods')
                                    ->schema([
                                        Forms\Components\Select::make('method')
                                            ->label('Payment Method')
                                            ->options([
                                                'Bank Transfer' => 'Bank Transfer',
                                                'Bank Transfer BCA' => 'Bank Transfer BCA',
                                                'Bank Transfer Mandiri' => 'Bank Transfer Mandiri',
                                                'Bank Transfer BNI' => 'Bank Transfer BNI',
                                                'Bank Transfer BRI' => 'Bank Transfer BRI',
                                                'Dana' => 'Dana',
                                                'GoPay' => 'GoPay',
                                                'OVO' => 'OVO',
                                                'ShopeePay' => 'ShopeePay',
                                                'LinkAja' => 'LinkAja',
                                                'Cash' => 'Cash',
                                                'Other' => 'Other',
                                            ])
                                            ->required()
                                            ->searchable(),

                                        Forms\Components\TextInput::make('account_number')
                                            ->label('Account Number/Phone')
                                            ->placeholder('1234567890 or 08123456789')
                                            ->helperText('Bank account number or phone number'),

                                        Forms\Components\TextInput::make('account_name')
                                            ->label('Account Owner Name')
                                            ->placeholder('Your Organization Name')
                                            ->helperText('Name of the account owner'),

                                        Forms\Components\TextInput::make('bank_name')
                                            ->label('Bank Name')
                                            ->placeholder('Bank Central Asia (BCA)')
                                            ->helperText('Full bank name (for bank transfers)')
                                            ->visible(fn(callable $get) => str_contains($get('method'), 'Bank Transfer')),
                                    ])
                                    ->columns(2)
                                    ->addActionLabel('Add Payment Method')
                                    ->reorderable(false)
                                    ->collapsible()
                                    ->itemLabel(fn(array $state): ?string => $state['method'] ?? null),

                                Forms\Components\Repeater::make('new_payment_config.custom_fields')
                                    ->label('Custom Fields')
                                    ->schema([
                                        Forms\Components\TextInput::make('label')
                                            ->label('Field Label')
                                            ->required()
                                            ->placeholder('e.g., Student ID, Phone Number')
                                            ->helperText('The label that will be shown to users')
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    // Convert to lowercase and replace spaces with hyphens
                                                    $fieldName = strtolower(str_replace(' ', '-', $state));
                                                    // Remove any special characters except hyphens and underscores
                                                    $fieldName = preg_replace('/[^a-z0-9\-_]/', '', $fieldName);
                                                    $set('name', $fieldName);
                                                }
                                            }),

                                        Forms\Components\TextInput::make('name')
                                            ->label('Field Name')
                                            ->required()
                                            ->placeholder('student-id')
                                            ->helperText('Internal field name (automatically generated from label)')
                                            ->rules(['regex:/^[a-z0-9\-_]+$/'])
                                            ->validationMessages([
                                                'regex' => 'Field name can only contain lowercase letters, numbers, hyphens, and underscores.',
                                            ])
                                            ->hidden(),

                                        Forms\Components\Select::make('type')
                                            ->label('Field Type')
                                            ->options([
                                                'text' => 'Text Input',
                                                'email' => 'Email Input',
                                                'tel' => 'Phone Number',
                                                'number' => 'Number Input',
                                                'textarea' => 'Text Area',
                                                'select' => 'Dropdown Select',
                                                'checkbox' => 'Checkbox',
                                                'radio' => 'Radio Buttons',
                                                'file' => 'File Upload',
                                            ])
                                            ->required()
                                            ->default('text')
                                            ->reactive(),

                                        Forms\Components\TextInput::make('placeholder')
                                            ->label('Placeholder Text')
                                            ->placeholder('Enter your student ID...')
                                            ->helperText('Optional placeholder text'),

                                        Forms\Components\Toggle::make('required')
                                            ->label('Required Field')
                                            ->default(false)
                                            ->helperText('Make this field mandatory'),

                                        Forms\Components\Textarea::make('options')
                                            ->label('Options (for dropdown/radio)')
                                            ->rows(2)
                                            ->placeholder('Option 1, Option 2, Option 3')
                                            ->helperText('For dropdown/radio fields, enter options separated by commas')
                                            ->visible(fn(callable $get) => in_array($get('type'), ['select', 'radio'])),
                                    ])
                                    ->columns(2)
                                    ->addActionLabel('Add Custom Field')
                                    ->reorderable(false)
                                    ->collapsible()
                                    ->itemLabel(fn(array $state): ?string => $state['label'] ?? null),
                            ])
                            ->visible(fn(callable $get) => $get('is_paid') && !$get('payment_configuration_id'))
                            ->collapsible()
                            ->columnSpanFull(),

                        Forms\Components\Section::make('Current Payment Configuration')
                            ->description('Edit the current payment configuration for this event')
                            ->schema([
                                Forms\Components\TextInput::make('payment_configuration.name')
                                    ->label('Configuration Name')
                                    ->default(fn(callable $get) => $get('title'))
                                    ->reactive()
                                    ->hidden()
                                    ->dehydrated(true)
                                    ->required(),

                                Forms\Components\Textarea::make('payment_configuration.description')
                                    ->label('Description')
                                    ->rows(2)
                                    ->placeholder('Describe what this payment is for...')
                                    ->helperText('Optional description'),

                                Forms\Components\TextInput::make('payment_configuration.amount')
                                    ->label('Amount')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->placeholder('50000')
                                    ->helperText('Enter the payment amount in Indonesian Rupiah')
                                    ->required(),



                                Forms\Components\Toggle::make('payment_configuration.is_active')
                                    ->label('Active')
                                    ->helperText('Enable to make this payment configuration available')
                                    ->default(true),

                                Forms\Components\Repeater::make('payment_configuration.payment_methods')
                                    ->label('Payment Methods')
                                    ->schema([
                                        Forms\Components\Select::make('method')
                                            ->label('Payment Method')
                                            ->options([
                                                'Bank Transfer' => 'Bank Transfer',
                                                'Bank Transfer BCA' => 'Bank Transfer BCA',
                                                'Bank Transfer Mandiri' => 'Bank Transfer Mandiri',
                                                'Bank Transfer BNI' => 'Bank Transfer BNI',
                                                'Bank Transfer BRI' => 'Bank Transfer BRI',
                                                'Dana' => 'Dana',
                                                'GoPay' => 'GoPay',
                                                'OVO' => 'OVO',
                                                'ShopeePay' => 'ShopeePay',
                                                'LinkAja' => 'LinkAja',
                                                'QRIS' => 'QRIS',
                                                'Cash' => 'Cash',
                                                'Other' => 'Other',
                                            ])
                                            ->required()
                                            ->searchable(),

                                        Forms\Components\TextInput::make('account_number')
                                            ->label('Account Number/Phone')
                                            ->placeholder('1234567890 or 08123456789')
                                            ->helperText('Bank account number or phone number'),

                                        Forms\Components\TextInput::make('account_name')
                                            ->label('Account Owner Name')
                                            ->placeholder('Your Organization Name')
                                            ->helperText('Name of the account owner'),

                                        Forms\Components\TextInput::make('bank_name')
                                            ->label('Bank Name')
                                            ->placeholder('Bank Central Asia (BCA)')
                                            ->helperText('Full bank name (for bank transfers)')
                                            ->visible(fn(callable $get) => str_contains($get('method'), 'Bank Transfer')),
                                    ])
                                    ->columns(2)
                                    ->addActionLabel('Add Payment Method')
                                    ->reorderable(false)
                                    ->collapsible()
                                    ->itemLabel(fn(array $state): ?string => $state['method'] ?? null),

                                Forms\Components\Repeater::make('payment_configuration.custom_fields')
                                    ->label('Custom Fields')
                                    ->schema([
                                        Forms\Components\TextInput::make('label')
                                            ->label('Field Label')
                                            ->required()
                                            ->placeholder('e.g., Student ID, Phone Number')
                                            ->helperText('The label that will be shown to users')
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    // Convert to lowercase and replace spaces with hyphens
                                                    $fieldName = strtolower(str_replace(' ', '-', $state));
                                                    // Remove any special characters except hyphens and underscores
                                                    $fieldName = preg_replace('/[^a-z0-9\-_]/', '', $fieldName);
                                                    $set('name', $fieldName);
                                                }
                                            }),

                                        Forms\Components\TextInput::make('name')
                                            ->label('Field Name')
                                            ->required()
                                            ->placeholder('student-id')
                                            ->helperText('Internal field name (automatically generated from label)')
                                            ->rules(['regex:/^[a-z0-9\-_]+$/'])
                                            ->validationMessages([
                                                'regex' => 'Field name can only contain lowercase letters, numbers, hyphens, and underscores.',
                                            ])
                                            ->hidden(),

                                        Forms\Components\Select::make('type')
                                            ->label('Field Type')
                                            ->options([
                                                'text' => 'Text Input',
                                                'email' => 'Email Input',
                                                'tel' => 'Phone Number',
                                                'number' => 'Number Input',
                                                'textarea' => 'Text Area',
                                                'select' => 'Dropdown Select',
                                                'checkbox' => 'Checkbox',
                                                'radio' => 'Radio Buttons',
                                                'file' => 'File Upload',
                                            ])
                                            ->required()
                                            ->default('text')
                                            ->reactive(),

                                        Forms\Components\TextInput::make('placeholder')
                                            ->label('Placeholder Text')
                                            ->placeholder('Enter your student ID...')
                                            ->helperText('Optional placeholder text'),

                                        Forms\Components\Toggle::make('required')
                                            ->label('Required Field')
                                            ->default(false)
                                            ->helperText('Make this field mandatory'),

                                        Forms\Components\Textarea::make('options')
                                            ->label('Options (for dropdown/radio)')
                                            ->rows(2)
                                            ->placeholder('Option 1, Option 2, Option 3')
                                            ->helperText('For dropdown/radio fields, enter options separated by commas')
                                            ->visible(fn(callable $get) => in_array($get('type'), ['select', 'radio'])),
                                    ])
                                    ->columns(2)
                                    ->addActionLabel('Add Custom Field')
                                    ->reorderable(false)
                                    ->collapsible()
                                    ->itemLabel(fn(array $state): ?string => $state['label'] ?? null),
                            ])
                            ->visible(fn(callable $get, $record) => $get('is_paid') && $record && $record->payment_configuration_id)
                            ->collapsible()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn(callable $get) => $get('type') === 'event')
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->disk('public')
                    ->square()
                    ->size(60),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'post' => 'info',
                        'event' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'post' => 'Post',
                        'event' => 'Event',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\TextColumn::make('event_date')
                    ->label('Event Date')
                    ->date('M j, Y')
                    ->sortable()
                    ->placeholder('-')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('event_type')
                    ->label('Format')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'online' => 'info',
                        'offline' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'online' => 'Online',
                        'offline' => 'Offline',
                        default => '-',
                    })
                    ->placeholder('-'),

                Tables\Columns\IconColumn::make('is_paid')
                    ->label('Fee')
                    ->boolean()
                    ->trueIcon('heroicon-o-currency-dollar')
                    ->falseIcon('heroicon-o-gift')
                    ->trueColor('warning')
                    ->falseColor('success')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('paymentConfiguration.name')
                    ->label('Payment Config')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->wrap()
                    ->placeholder('No config'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Published')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Content Type')
                    ->options([
                        'post' => 'Posts',
                        'event' => 'Events',
                    ]),
                Tables\Filters\TernaryFilter::make('is_paid')
                    ->label('Event Type')
                    ->placeholder('All Events')
                    ->trueLabel('Paid Events')
                    ->falseLabel('Free Events'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit'),
                Tables\Actions\DeleteAction::make()
                    ->label('Delete'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Selected'),
                ]),
            ])
            ->emptyStateHeading('No Content Yet')
            ->emptyStateDescription('Start creating posts and events to engage with your members!')
            ->emptyStateIcon('heroicon-o-rss');
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
            'index' => Pages\ListFeeds::route('/'),
            'create' => Pages\CreateFeed::route('/create'),
            'edit' => Pages\EditFeed::route('/{record}/edit'),
        ];
    }
}
