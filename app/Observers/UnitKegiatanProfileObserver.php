<?php

namespace App\Observers;

use App\Models\UnitKegiatanProfile;
use Filament\Facades\Filament;

class UnitKegiatanProfileObserver
{
    public function creating(UnitKegiatanProfile $unitKegiatanProfile): void
    {
        $user = Filament::auth()?->user();
        if ($user && $user->hasRole('admin_ukm')) {
            $unitKegiatanProfile->unit_kegiatan_id = $user->admin->unit_kegiatan_id;
        }
    }
    /**
     * Handle the UnitKegiatanProfile "created" event.
     */
    public function created(UnitKegiatanProfile $unitKegiatanProfile): void
    {
        //
    }

    /**
     * Handle the UnitKegiatanProfile "updated" event.
     */
    public function updated(UnitKegiatanProfile $unitKegiatanProfile): void
    {
        //
    }

    /**
     * Handle the UnitKegiatanProfile "deleted" event.
     */
    public function deleted(UnitKegiatanProfile $unitKegiatanProfile): void
    {
        //
    }

    /**
     * Handle the UnitKegiatanProfile "restored" event.
     */
    public function restored(UnitKegiatanProfile $unitKegiatanProfile): void
    {
        //
    }

    /**
     * Handle the UnitKegiatanProfile "force deleted" event.
     */
    public function forceDeleted(UnitKegiatanProfile $unitKegiatanProfile): void
    {
        //
    }
}
