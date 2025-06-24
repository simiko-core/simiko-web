<?php

namespace App\Livewire;

use App\Models\PendaftaranAnggota;
use Filament\Forms\Concerns\InteractsWithForms;
use Livewire\Component;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;

class FormPendaftaran extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('No Membership Applications')
            ->emptyStateDescription('When students apply for membership, their applications will appear here for review.')
            ->emptyStateIcon('heroicon-o-user-group')
            ->query(PendaftaranAnggota::query()->where('user_id', '!=', 1))
            ->columns([
                ImageColumn::make('user.photo')
                    ->label('Photo')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png'))
                    ->size(40),

                TextColumn::make('user.name')
                    ->label('Full Name')
                    ->sortable()
                    ->searchable()
                    ->weight('semibold'),

                TextColumn::make('user.email')
                    ->label('Email Address')
                    ->sortable()
                    ->searchable()
                    ->color('gray'),

                TextColumn::make('user.phone')
                    ->label('Phone Number')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Not provided'),

                TextColumn::make('status')
                    ->badge()
                    ->label('Application Status')
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Pending Review',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Applied Date')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->label('Application Status')
                    ->options([
                        'pending' => 'Pending',
                        'accepted' => 'Accepted', 
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                \Filament\Tables\Actions\Action::make('accept')
                    ->label('Accept')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Accept Application?')
                    ->modalDescription('This will approve the student\'s membership application.')
                    ->visible(fn(PendaftaranAnggota $record): bool => $record->status === 'pending')
                    ->action(function (PendaftaranAnggota $record) {
                        $record->update(['status' => 'accepted']);
                        Notification::make()
                            ->title('Application Accepted')
                            ->body("Successfully accepted {$record->user->name}'s membership application.")
                            ->success()
                            ->send();
                    }),

                \Filament\Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Application?')
                    ->modalDescription('This will reject the student\'s membership application.')
                    ->visible(fn(PendaftaranAnggota $record): bool => $record->status === 'pending')
                    ->action(function (PendaftaranAnggota $record) {
                        $record->update(['status' => 'rejected']);
                        Notification::make()
                            ->title('Application Rejected')
                            ->body("Rejected {$record->user->name}'s membership application.")
                            ->warning()
                            ->send();
                    }),

                \Filament\Tables\Actions\Action::make('delete')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete Application?')
                    ->modalDescription('This action cannot be undone.')
                    ->visible(fn(PendaftaranAnggota $record): bool => in_array($record->status, ['pending', 'rejected']))
                    ->action(function (PendaftaranAnggota $record) {
                        $userName = $record->user->name;
                        $record->delete();
                        Notification::make()
                            ->title('Application Deleted')
                            ->body("Deleted {$userName}'s membership application.")
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\BulkAction::make('accept_selected')
                        ->label('Accept Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $count = $records->where('status', 'pending')->count();
                            $records->where('status', 'pending')->each->update(['status' => 'accepted']);
                            
                            Notification::make()
                                ->title('Applications Accepted')
                                ->body("Successfully accepted {$count} membership applications.")
                                ->success()
                                ->send();
                        }),

                    \Filament\Tables\Actions\BulkAction::make('reject_selected')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $count = $records->where('status', 'pending')->count();
                            $records->where('status', 'pending')->each->update(['status' => 'rejected']);
                            
                            Notification::make()
                                ->title('Applications Rejected')
                                ->body("Rejected {$count} membership applications.")
                                ->warning()
                                ->send();
                        }),
                ]),
            ]);
    }

    public function render()
    {
        return view('livewire.form-pendaftaran');
    }
}
