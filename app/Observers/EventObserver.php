<?php

namespace App\Observers;

use App\Models\Event;
use Filament\Facades\Filament;

class EventObserver
{
    public function creating(Event $event): void
    {
        $user = Filament::auth()?->user();
        if ($user && $user->hasRole('admin_ukm')) {
            $event->unit_kegiatan_id = $user->admin->unit_kegiatan_id;
        }
    }

    /**
     * Handle the Event "created" event.
     */
    public function created(Event $event): void
    {
        //
    }

    /**
     * Handle the Event "updated" event.
     */
    public function updated(Event $event): void
    {
        //
    }

    /**
     * Handle the Event "deleted" event.
     */
    public function deleted(Event $event): void
    {
        //
    }

    /**
     * Handle the Event "restored" event.
     */
    public function restored(Event $event): void
    {
        //
    }

    /**
     * Handle the Event "force deleted" event.
     */
    public function forceDeleted(Event $event): void
    {
        //
    }
}
