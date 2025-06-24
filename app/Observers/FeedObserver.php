<?php

namespace App\Observers;

use App\Models\Feed;
use Filament\Facades\Filament;

class FeedObserver
{
    public function creating(Feed $feed): void
    {
        $user = Filament::auth()?->user();
        if ($user && $user->hasRole('admin_ukm')) {
            $feed->unit_kegiatan_id = $user->admin->unit_kegiatan_id;
        }
    }
}
