<?php

namespace App\Filament\UkmPanel\Resources;

use App\Filament\UkmPanel\Resources\PaymentTransactionResource\Pages;
use App\Models\PaymentTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentTransactionResource extends Resource
{
    protected static ?string $model = PaymentTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';

    protected static ?string $navigationGroup = 'Payment Management';

    protected static ?string $navigationLabel = 'Payment Transactions';

    protected static ?string $modelLabel = 'Payment Transaction';

    protected static ?string $pluralModelLabel = 'Payment Transactions';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Information')
                    ->description('View and manage payment transaction details')
                    ->schema([
                        Forms\Components\TextInput::make('transaction_id')
                            ->label('Transaction ID')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('TXN-2024-001')
                            ->helperText('Unique identifier for this transaction'),

                        Forms\Components\Select::make('payment_configuration_id')
                            ->label('Payment Configuration')
                            ->relationship('paymentConfiguration', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Select which payment configuration this transaction belongs to'),

                        Forms\Components\Select::make('anonymous_registration_id')
                            ->label('Event Registration')
                            ->relationship('anonymousRegistration', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Anonymous registration for this payment'),

                        Forms\Components\Select::make('feed_id')
                            ->label('Related Event (Optional)')
                            ->relationship('feed', 'title')
                            ->searchable()
                            ->preload()
                            ->helperText('Link to a specific event if applicable'),

                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->required()
                            ->prefix('Rp')
                            ->placeholder('50000'),


                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Payment Details')
                    ->description('Payment method and status information')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'cancelled' => 'Cancelled',
                                'expired' => 'Expired',
                            ])
                            ->required()
                            ->default('pending'),

                        Forms\Components\TextInput::make('payment_method')
                            ->label('Payment Method')
                            ->placeholder('Bank Transfer, Dana, etc.')
                            ->helperText('Method used for payment'),

                        Forms\Components\KeyValue::make('payment_details')
                            ->label('Payment Details')
                            ->keyLabel('Field')
                            ->valueLabel('Value')
                            ->helperText('Additional payment method details'),

                        Forms\Components\FileUpload::make('proof_of_payment')
                            ->label('Proof of Payment')
                            ->disk('public')
                            ->directory('payment_proofs')
                            ->visibility('public')
                            ->imagePreviewHeight('100')
                            ->maxSize(4096)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                            ->helperText('Upload payment receipt or proof (JPG, PNG, PDF, max 4MB)'),

                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Paid At')
                            ->helperText('When the payment was completed'),

                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Expires At')
                            ->helperText('When this transaction expires (optional)'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Custom Data')
                    ->description('Custom field data submitted by the user')
                    ->schema([
                        Forms\Components\KeyValue::make('custom_data')
                            ->label('Custom Field Data')
                            ->keyLabel('Field')
                            ->valueLabel('Value')
                            ->helperText('Data from custom fields filled by the user'),

                        Forms\Components\FileUpload::make('custom_files')
                            ->label('Custom File Uploads')
                            ->disk('public')
                            ->directory('custom_files')
                            ->visibility('public')
                            ->multiple()
                            ->maxSize(10240)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/jpg'])
                            ->helperText('Only image files are allowed (JPG, JPEG, PNG, GIF, max 10MB per file)')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Notes')
                    ->description('Administrative notes and comments')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Admin Notes')
                            ->rows(4)
                            ->placeholder('Add any administrative notes here...')
                            ->helperText('Internal notes for administrative purposes'),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('Transaction ID')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->copyable()
                    ->copyMessage('Transaction ID copied!'),


                Tables\Columns\TextColumn::make('user_email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        return $record->getUserEmail();
                    })
                    ->copyable()
                    ->copyMessage('Email copied!')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('feed.title')
                    ->label('Event Name')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->wrap()
                    ->placeholder('No event associated'),


                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                        'gray' => 'cancelled',
                        'gray' => 'expired',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'cancelled' => 'Cancelled',
                        'expired' => 'Expired',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Not specified'),

                Tables\Columns\TextColumn::make('proof_of_payment')
                    ->label('Proof')
                    ->formatStateUsing(fn($state) => $state ? '<a href="' . asset('storage/' . $state) . '" target="_blank">View</a>' : '-')
                    ->html()
                    ->limit(20)
                    ->wrap(),


                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Paid At')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->placeholder('Not paid yet'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'cancelled' => 'Cancelled',
                        'expired' => 'Expired',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('feed_id')
                    ->label('Event Name')
                    ->relationship('feed', 'title')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\Filter::make('recent')
                    ->label('Recent Transactions')
                    ->query(fn(Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(7))),

                Tables\Filters\Filter::make('paid_today')
                    ->label('Paid Today')
                    ->query(fn(Builder $query): Builder => $query->whereDate('paid_at', today())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View Details'),
                Tables\Actions\EditAction::make()
                    ->label('Edit Transaction'),
                Tables\Actions\Action::make('mark_paid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Mark as Paid?')
                    ->modalDescription('This will mark the transaction as paid.')
                    ->visible(fn(PaymentTransaction $record): bool => $record->status === 'pending')
                    ->action(function (PaymentTransaction $record) {
                        $record->markAsPaid();
                    }),
                Tables\Actions\Action::make('mark_failed')
                    ->label('Mark as Failed')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Mark as Failed?')
                    ->modalDescription('This will mark the transaction as failed.')
                    ->visible(fn(PaymentTransaction $record): bool => $record->status === 'pending')
                    ->action(function (PaymentTransaction $record) {
                        $record->markAsFailed();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_paid_selected')
                        ->label('Mark Selected as Paid')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->where('status', 'pending')->each->markAsPaid();
                        }),
                    Tables\Actions\BulkAction::make('mark_failed_selected')
                        ->label('Mark Selected as Failed')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->where('status', 'pending')->each->markAsFailed();
                        }),
                ]),
            ])
            ->emptyStateHeading('No Payment Transactions')
            ->emptyStateDescription('Payment transactions will appear here when users make payments.')
            ->emptyStateIcon('heroicon-o-receipt-refund');
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
            'index' => Pages\ListPaymentTransactions::route('/'),
            'create' => Pages\CreatePaymentTransaction::route('/create'),
            'view' => Pages\ViewPaymentTransaction::route('/{record}'),
            'edit' => Pages\EditPaymentTransaction::route('/{record}/edit'),
        ];
    }
}
