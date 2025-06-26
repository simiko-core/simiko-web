<?php

namespace App\Filament\UkmPanel\Resources;

use App\Filament\UkmPanel\Resources\PaymentConfigurationResource\Pages;
use App\Models\PaymentConfiguration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PaymentConfigurationResource extends Resource
{
    protected static ?string $model = PaymentConfiguration::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Payment Management';

    protected static ?string $navigationLabel = 'Payment Configurations';

    protected static ?string $modelLabel = 'Payment Configuration';

    protected static ?string $pluralModelLabel = 'Payment Configurations';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->description('Configure the payment details and basic information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Configuration Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Event Registration Fee, Workshop Payment, Membership Fee')
                            ->helperText('Give this payment configuration a clear, descriptive name'),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->maxLength(500)
                            ->rows(3)
                            ->placeholder('Describe what this payment is for and any important details...')
                            ->helperText('Optional description to help identify this payment configuration'),

                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->required()
                            ->prefix('Rp')
                            ->placeholder('50000')
                            ->helperText('Enter the payment amount in Indonesian Rupiah'),

                        Forms\Components\Select::make('currency')
                            ->label('Currency')
                            ->options([
                                'IDR' => 'Indonesian Rupiah (IDR)',
                                'USD' => 'US Dollar (USD)',
                            ])
                            ->default('IDR')
                            ->required()
                            ->helperText('Select the currency for this payment'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->helperText('Enable to make this payment configuration available')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Payment Methods')
                    ->description('Configure which payment methods are available for this configuration')
                    ->schema([
                        Forms\Components\Repeater::make('payment_methods')
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
                                    ->helperText('Bank account number or phone number for digital wallets'),

                                Forms\Components\TextInput::make('account_name')
                                    ->label('Account Owner Name')
                                    ->placeholder('Your Organization Name')
                                    ->helperText('Name of the account owner'),

                                Forms\Components\TextInput::make('bank_name')
                                    ->label('Bank Name')
                                    ->placeholder('Bank Central Asia (BCA)')
                                    ->helperText('Full bank name (for bank transfers)')
                                    ->visible(fn (callable $get) => Str::contains($get('method'), 'Bank Transfer')),

                                Forms\Components\Textarea::make('instructions')
                                    ->label('Payment Instructions')
                                    ->rows(2)
                                    ->placeholder('Special instructions for this payment method...')
                                    ->helperText('Optional: Add specific instructions for this payment method'),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add Payment Method')
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['method'] ?? null),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Custom Fields')
                    ->description('Add custom fields that participants need to fill when making payment')
                    ->schema([
                        Forms\Components\Repeater::make('custom_fields')
                            ->label('Custom Fields')
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label('Field Label')
                                    ->required()
                                    ->placeholder('e.g., Student ID, Phone Number, Faculty')
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
                                    ->helperText('Optional placeholder text for the field'),

                                Forms\Components\Toggle::make('required')
                                    ->label('Required Field')
                                    ->default(false)
                                    ->helperText('Make this field mandatory for payment'),

                                Forms\Components\Textarea::make('options')
                                    ->label('Options (for dropdown/radio)')
                                    ->rows(3)
                                    ->placeholder('Option 1, Option 2, Option 3')
                                    ->helperText('For dropdown/radio fields, enter options separated by commas')
                                    ->visible(fn (callable $get) => in_array($get('type'), ['select', 'radio'])),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add Custom Field')
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Additional Settings')
                    ->description('Configure additional settings for this payment configuration')
                    ->schema([
                        Forms\Components\DatePicker::make('settings.due_date')
                            ->label('Payment Due Date')
                            ->helperText('Set a deadline for payments (optional)'),

                        Forms\Components\TextInput::make('settings.max_participants')
                            ->label('Maximum Participants')
                            ->numeric()
                            ->placeholder('100')
                            ->helperText('Maximum number of participants allowed (optional)'),

                        Forms\Components\Textarea::make('settings.terms_conditions')
                            ->label('Terms & Conditions')
                            ->rows(4)
                            ->placeholder('Enter terms and conditions for this payment...')
                            ->helperText('Optional terms and conditions that participants must agree to'),

                        Forms\Components\Textarea::make('settings.notes')
                            ->label('Internal Notes')
                            ->rows(3)
                            ->placeholder('Internal notes for admin reference...')
                            ->helperText('Notes visible only to administrators'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Configuration Name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->limit(40)
                    ->wrap(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('IDR')
                    ->sortable()
                    ->color('success'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->wrap()
                    ->placeholder('No description'),

                Tables\Columns\TextColumn::make('payment_methods')
                    ->label('Payment Methods')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return 'None';
                        return collect($state)->pluck('method')->implode(', ');
                    })
                    ->limit(30)
                    ->wrap(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('total_transactions')
                    ->label('Transactions')
                    ->counts('transactions')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('Revenue')
                    ->money('IDR')
                    ->getStateUsing(fn (PaymentConfiguration $record) => $record->total_revenue)
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All configurations')
                    ->trueLabel('Active configurations')
                    ->falseLabel('Inactive configurations'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit Configuration'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Selected'),
                ]),
            ])
            ->emptyStateHeading('No Payment Configurations')
            ->emptyStateDescription('Create your first payment configuration to start accepting payments.')
            ->emptyStateIcon('heroicon-o-credit-card');
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
            'index' => Pages\ListPaymentConfigurations::route('/'),
            'create' => Pages\CreatePaymentConfiguration::route('/create'),
            'edit' => Pages\EditPaymentConfiguration::route('/{record}/edit'),
        ];
    }
} 