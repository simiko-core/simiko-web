<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminResource\Pages;
use App\Filament\Resources\AdminResource\RelationManagers;
use App\Models\Admin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdminResource extends Resource
{
    protected static ?string $model = Admin::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $navigationLabel = 'UKM Administrators';

    protected static ?string $modelLabel = 'UKM Administrator';

    protected static ?string $pluralModelLabel = 'UKM Administrators';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('UKM Administrator Information')
                    ->description('Assign a user as administrator for a specific UKM. The user will gain administrative privileges for that unit.')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Select User')
                            ->relationship('user', 'name')
                            ->preload()
                            ->searchable()
                            ->required()
                            ->helperText('Choose a user who will become the administrator')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Full Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter user\'s full name'),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('user@example.com'),
                                Forms\Components\TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter secure password')
                            ]),
                        Forms\Components\Select::make('unit_kegiatan_id')
                            ->label('Unit Kegiatan (UKM)')
                            ->relationship('unitKegiatan', 'name')
                            ->preload()
                            ->searchable()
                            ->required()
                            ->helperText('Select which UKM this user will administrate'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Administrator Name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('unitKegiatan.name')
                    ->label('Manages UKM')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Assigned Date')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('unit_kegiatan_id')
                    ->label('Filter by UKM')
                    ->relationship('unitKegiatan', 'name')
                    ->preload()
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit Admin'),
                Tables\Actions\DeleteAction::make()
                    ->label('Remove Admin'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Remove Selected Admins'),
                ]),
            ])
            ->emptyStateHeading('No UKM Administrators')
            ->emptyStateDescription('Create the first UKM administrator to manage student organizations.')
            ->emptyStateIcon('heroicon-o-user-circle');
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
            'index' => Pages\ListAdmins::route('/'),
            'create' => Pages\CreateAdmin::route('/create'),
            'edit' => Pages\EditAdmin::route('/{record}/edit'),
        ];
    }
}
