<?php

namespace App\Observers;

use App\Models\Achievement;
use Filament\Facades\Filament;

class AchievementObserver
{
    public function creating(Achievement $achievement): void
    {
        $user = Filament::auth()?->user();
        if ($user && $user->hasRole('admin_ukm')) {
            $achievement->unit_kegiatan_id = $user->admin->unit_kegiatan_id;
        }
    }
} 