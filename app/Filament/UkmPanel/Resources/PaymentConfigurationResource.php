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

    protected static ?string $navigationLabel = 'Payment Methods';

    protected static ?string $modelLabel = 'Payment Method';

    protected static ?string $pluralModelLabel = 'Payment Methods';

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

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


                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->required()
                            ->prefix('Rp')
                            ->placeholder('50000')
                            ->helperText('Enter the payment amount in Indonesian Rupiah'),
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
                                    ->visible(fn(callable $get) => Str::contains($get('method'), 'Bank Transfer')),

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
                            ->itemLabel(fn(array $state): ?string => $state['method'] ?? null),
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
                                    ->visible(fn(callable $get) => in_array($get('type'), ['select', 'radio'])),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add Custom Field')
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['label'] ?? null),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                PaymentConfiguration::query()->with(['feeds'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Event Name')
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

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->getStateUsing(fn(PaymentConfiguration $record): bool => $record->is_active)
                    ->tooltip(function (PaymentConfiguration $record): string {
                        $feed = $record->feeds()->first();
                        if (!$feed) {
                            return $record->is_active ? 'Available for registration' : 'Not available';
                        }

                        if ($record->is_active) {
                            if (!$feed->max_participants) {
                                return 'Available - Unlimited capacity';
                            }
                            $confirmed = $feed->getPaidRegistrationsCount();
                            $pendingWithProof = $feed->getPendingWithProofCount();
                            $totalConfirmed = $confirmed + $pendingWithProof;
                            $available = $feed->max_participants - $totalConfirmed;
                            return "Available - {$available} slots remaining\nConfirmed: {$totalConfirmed} (paid: {$confirmed}, pending with proof: {$pendingWithProof})";
                        } else {
                            return 'Event is full - Registration closed';
                        }
                    }),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->wrap()
                    ->placeholder('No description'),

                Tables\Columns\TextColumn::make('payment_methods')
                    ->label('Payment Methods')
                    ->formatStateUsing(function ($state) {
                        if (!$state || !is_array($state) || empty($state)) {
                            return 'None';
                        }

                        $methods = collect($state)
                            ->filter() // Remove null/empty items
                            ->map(function ($method) {
                                if (is_array($method) && isset($method['method'])) {
                                    return $method['method'];
                                }
                                return null;
                            })
                            ->filter() // Remove null values
                            ->unique()
                            ->values();

                        return $methods->isEmpty() ? 'None' : $methods->implode(', ');
                    })
                    ->limit(30)
                    ->wrap(),

                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('Revenue')
                    ->money('IDR')
                    ->getStateUsing(fn(PaymentConfiguration $record) => $record->total_revenue)
                    ->sortable(),

                Tables\Columns\TextColumn::make('current_registrations')
                    ->label('Current Registrations')
                    ->getStateUsing(function (PaymentConfiguration $record) {
                        $feed = $record->feeds()->first();
                        if (!$feed) return 0;

                        $confirmed = $feed->getPaidRegistrationsCount();
                        $pendingWithProof = $feed->getPendingWithProofCount();
                        $pendingWithoutProof = $feed->getPendingWithoutProofCount();

                        return [
                            'confirmed' => $confirmed,
                            'pending_with_proof' => $pendingWithProof,
                            'pending_without_proof' => $pendingWithoutProof,
                            'total_confirmed' => $confirmed + $pendingWithProof
                        ];
                    })
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            $confirmed = $state['confirmed'];
                            $pendingWithProof = $state['pending_with_proof'];
                            $pendingWithoutProof = $state['pending_without_proof'];
                            $totalConfirmed = $state['total_confirmed'];

                            $parts = [];
                            if ($confirmed > 0) $parts[] = "{$confirmed} paid";
                            if ($pendingWithProof > 0) $parts[] = "{$pendingWithProof} pending (proof uploaded)";
                            if ($pendingWithoutProof > 0) $parts[] = "{$pendingWithoutProof} pending (no proof)";

                            return $parts ? implode(', ', $parts) : '0';
                        }
                        return $state;
                    })
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->tooltip(function (PaymentConfiguration $record) {
                        $feed = $record->feeds()->first();
                        if (!$feed) return 'No associated event';

                        $confirmed = $feed->getPaidRegistrationsCount();
                        $pendingWithProof = $feed->getPendingWithProofCount();
                        $pendingWithoutProof = $feed->getPendingWithoutProofCount();
                        $totalConfirmed = $confirmed + $pendingWithProof;

                        $tooltip = "Confirmed registrations: {$totalConfirmed}\n";
                        $tooltip .= "• Paid: {$confirmed}\n";
                        $tooltip .= "• Pending with proof: {$pendingWithProof}\n";
                        $tooltip .= "• Pending without proof: {$pendingWithoutProof}";

                        return $tooltip;
                    }),

                Tables\Columns\TextColumn::make('max_participants')
                    ->label('Max Participants')
                    ->getStateUsing(function (PaymentConfiguration $record) {
                        $feed = $record->feeds()->first();
                        return $feed && $feed->max_participants ? number_format($feed->max_participants) : 'Unlimited';
                    })
                    ->badge()
                    ->color(fn($state) => $state === 'Unlimited' ? 'success' : 'primary')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('available_slots')
                    ->label('Available Slots')
                    ->getStateUsing(function (PaymentConfiguration $record) {
                        $feed = $record->feeds()->first();
                        if (!$feed || !$feed->max_participants) {
                            return 'Unlimited';
                        }
                        $confirmed = $feed->getPaidRegistrationsCount();
                        $pendingWithProof = $feed->getPendingWithProofCount();
                        $totalConfirmed = $confirmed + $pendingWithProof;
                        $available = max(0, $feed->max_participants - $totalConfirmed);
                        return number_format($available);
                    })
                    ->badge()
                    ->color(function ($state, PaymentConfiguration $record) {
                        if ($state === 'Unlimited') return 'success';

                        $feed = $record->feeds()->first();
                        if (!$feed || !$feed->max_participants) return 'success';

                        $available = (int) str_replace(',', '', $state);
                        $maxParticipants = $feed->max_participants;
                        $percentage = $available / $maxParticipants;

                        if ($percentage <= 0) return 'danger';
                        if ($percentage <= 0.2) return 'warning';
                        return 'success';
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('capacity_usage')
                    ->label('Capacity')
                    ->getStateUsing(function (PaymentConfiguration $record) {
                        $feed = $record->feeds()->first();
                        if (!$feed || !$feed->max_participants) {
                            return 'No limit';
                        }
                        $confirmed = $feed->getPaidRegistrationsCount();
                        $pendingWithProof = $feed->getPendingWithProofCount();
                        $totalConfirmed = $confirmed + $pendingWithProof;
                        $max = $feed->max_participants;
                        $percentage = $max > 0 ? round(($totalConfirmed / $max) * 100) : 0;
                        return "{$totalConfirmed}/{$max} ({$percentage}%)";
                    })
                    ->html()
                    ->formatStateUsing(function ($state, PaymentConfiguration $record) {
                        $feed = $record->feeds()->first();
                        if (!$feed || !$feed->max_participants || $state === 'No limit') {
                            return '<span class="text-green-600 font-medium">No limit</span>';
                        }

                        $confirmed = $feed->getPaidRegistrationsCount();
                        $pendingWithProof = $feed->getPendingWithProofCount();
                        $totalConfirmed = $confirmed + $pendingWithProof;
                        $max = $feed->max_participants;
                        $percentage = $max > 0 ? ($totalConfirmed / $max) * 100 : 0;

                        $color = 'bg-green-500';
                        $textColor = 'text-green-700';
                        if ($percentage >= 100) {
                            $color = 'bg-red-500';
                            $textColor = 'text-red-700';
                        } elseif ($percentage >= 80) {
                            $color = 'bg-yellow-500';
                            $textColor = 'text-yellow-700';
                        }

                        return '
                            <div class="w-full">
                                <div class="flex justify-between text-xs ' . $textColor . ' mb-1">
                                    <span>' . $totalConfirmed . '/' . $max . '</span>
                                    <span>' . round($percentage) . '%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="' . $color . ' h-2 rounded-full" style="width: ' . min(100, $percentage) . '%"></div>
                                </div>
                            </div>
                        ';
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('availability')
                    ->label('Availability Status')
                    ->placeholder('All configurations')
                    ->trueLabel('Available for registration')
                    ->falseLabel('Full or unavailable')
                    ->queries(
                        true: fn(Builder $query) => $query->whereHas('feeds', function ($feedQuery) {
                            $feedQuery->where(function ($q) {
                                $q->whereNull('max_participants')
                                    ->orWhereRaw('max_participants > (
                                        SELECT COUNT(*) FROM payment_transactions 
                                        WHERE feed_id = feeds.id 
                                        AND (
                                            status = "paid" 
                                            OR (status = "pending" AND proof_of_payment IS NOT NULL)
                                        )
                                    )');
                            });
                        }),
                        false: fn(Builder $query) => $query->whereHas('feeds', function ($feedQuery) {
                            $feedQuery->whereNotNull('max_participants')
                                ->whereRaw('max_participants <= (
                                    SELECT COUNT(*) FROM payment_transactions 
                                    WHERE feed_id = feeds.id 
                                    AND (
                                        status = "paid" 
                                        OR (status = "pending" AND proof_of_payment IS NOT NULL)
                                    )
                                )');
                        }),
                        blank: fn(Builder $query) => $query,
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
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
