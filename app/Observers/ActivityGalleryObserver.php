<?php

namespace App\Observers;

use App\Models\ActivityGallery;
use Filament\Facades\Filament;

class ActivityGalleryObserver
{
    public function creating(ActivityGallery $gallery): void
    {
        $user = Filament::auth()?->user();
        if ($user && $user->hasRole('admin_ukm')) {
            $gallery->unit_kegiatan_id = $user->admin->unit_kegiatan_id;
        }
    }
} 