<?php

namespace App\Observers;

use App\Models\Post;
use Filament\Facades\Filament;

class PostObserver
{
    public function creating(Post $post): void
    {
        $user = Filament::auth()?->user();
        if ($user && $user->hasRole('admin_ukm')) {
            $post->unit_kegiatan_id = $user->admin->unit_kegiatan_id;
        }
    }

    /**
     * Handle the Post "created" event.
     */
    public function created(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "restored" event.
     */
    public function restored(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "force deleted" event.
     */
    public function forceDeleted(Post $post): void
    {
        //
    }
}
