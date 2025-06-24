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
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('event_date', null)),
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
                            ->helperText('Make it clear and interesting to attract readers'),

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

                        Forms\Components\TextInput::make('price')
                            ->label('Registration Fee (IDR)')
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('50000')
                            ->helperText('Enter the registration fee in Indonesian Rupiah')
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
                                    ->placeholder('1234567890 or 08123456789'),
                                Forms\Components\TextInput::make('account_name')
                                    ->label('Account Owner Name')
                                    ->required()
                                    ->placeholder('Your Organization Name'),
                            ])
                            ->visible(fn (callable $get) => $get('is_paid'))
                            ->helperText('Add one or more payment methods for participants')
                            ->columnSpanFull()
                            ->columns(3)
                            ->addActionLabel('Add Payment Method')
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
                    ->label('Fee')
                    ->boolean()
                    ->trueIcon('heroicon-o-currency-dollar')
                    ->falseIcon('heroicon-o-gift')
                    ->trueColor('warning')
                    ->falseColor('success')
                    ->placeholder('-'),

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
