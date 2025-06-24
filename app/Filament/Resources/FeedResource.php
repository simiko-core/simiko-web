<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeedResource\Pages;
use App\Filament\Resources\FeedResource\RelationManagers;
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
    
    protected static ?string $modelLabel = 'Feed Item';
    
    protected static ?string $pluralModelLabel = 'Posts & Events';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Content Type & Organization')
                    ->description('Choose the type of content and select which organization it belongs to')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Content Type')
                            ->options([
                                'post' => 'Post - News, announcements, updates',
                                'event' => 'Event - Activities, workshops, competitions',
                            ])
                            ->required()
                            ->reactive()
                            ->helperText('Select whether this is a regular post or an event')
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('event_date', null)),

                        Forms\Components\Select::make('unit_kegiatan_id')
                            ->label('Organization (UKM)')
                            ->relationship('unitKegiatan', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Select which UKM this content belongs to'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Content Information')
                    ->description('Write the main content, title, and upload images')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter an engaging title')
                            ->helperText('Keep it concise and descriptive')
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('content')
                            ->label('Content')
                            ->required()
                            ->placeholder('Write your content here...')
                            ->helperText('Use the toolbar to format your text')
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
                            ->helperText('Upload JPG, PNG, or GIF. Maximum 2MB.')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Forms\Components\Section::make('Event Details')
                    ->description('Configure event-specific information like date, location, and pricing')
                    ->schema([
                        Forms\Components\DatePicker::make('event_date')
                            ->label('Event Date')
                            ->required()
                            ->minDate(now())
                            ->helperText('Select when this event will take place'),

                        Forms\Components\Select::make('event_type')
                            ->label('Event Type')
                            ->options([
                                'online' => 'Online - Virtual event',
                                'offline' => 'Offline - Physical location',
                            ])
                            ->required()
                            ->helperText('Choose event format'),

                        Forms\Components\TextInput::make('location')
                            ->label('Location')
                            ->placeholder('e.g., Zoom Meeting / Building A, Room 301')
                            ->helperText('Specify where the event takes place')
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_paid')
                            ->label('Paid Event')
                            ->helperText('Enable if this event requires payment')
                            ->reactive()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('price')
                            ->label('Price (IDR)')
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('50000')
                            ->helperText('Enter price in Indonesian Rupiah')
                            ->visible(fn (callable $get) => $get('is_paid'))
                            ->columnSpanFull(),

                        Forms\Components\Repeater::make('payment_methods')
                            ->label('Payment Methods')
                            ->schema([
                                Forms\Components\Select::make('method')
                                    ->label('Payment Method')
                                    ->options([
                                        'Bank Transfer' => 'Bank Transfer',
                                        'Dana' => 'Dana',
                                        'GoPay' => 'GoPay',
                                        'OVO' => 'OVO',
                                        'ShopeePay' => 'ShopeePay',
                                    ])
                                    ->required(),
                                Forms\Components\TextInput::make('account_number')
                                    ->label('Account Number/Phone')
                                    ->required()
                                    ->placeholder('1234567890'),
                                Forms\Components\TextInput::make('account_name')
                                    ->label('Account Owner Name')
                                    ->required()
                                    ->placeholder('John Doe'),
                            ])
                            ->visible(fn (callable $get) => $get('is_paid'))
                            ->helperText('Add payment options for participants')
                            ->columnSpanFull()
                            ->columns(3)
                            ->addActionLabel('Add Payment Method'),
                    ])
                    ->visible(fn (callable $get) => $get('type') === 'event')
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

                Tables\Columns\TextColumn::make('unitKegiatan.name')
                    ->label('Organization')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->color('info'),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'post' => 'info',
                        'event' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'post' => 'Post',
                        'event' => 'Event',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->limit(40)
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
                    ->color(fn (?string $state): string => match($state) {
                        'online' => 'info',
                        'offline' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match($state) {
                        'online' => 'Online',
                        'offline' => 'Offline',
                        default => '-',
                    })
                    ->placeholder('-'),

                Tables\Columns\IconColumn::make('is_paid')
                    ->label('Paid')
                    ->boolean()
                    ->trueIcon('heroicon-o-currency-dollar')
                    ->falseIcon('heroicon-o-gift')
                    ->trueColor('warning')
                    ->falseColor('success')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
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
                Tables\Filters\SelectFilter::make('unit_kegiatan_id')
                    ->label('Organization')
                    ->relationship('unitKegiatan', 'name')
                    ->searchable()
                    ->preload(),
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
            ->emptyStateHeading('No Content Found')
            ->emptyStateDescription('Create the first post or event to get started.')
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