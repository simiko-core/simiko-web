<?php

namespace App\Observers;

use App\Models\Admin;

class AdminObserver
{
    /**
     * Handle the Admin "created" event.
     */
    public function created(Admin $admin): void
    {
        // Automatically assign admin_ukm role to the user when admin is created
        if ($admin->user && !$admin->user->hasRole('admin_ukm')) {
            $admin->user->assignRole('admin_ukm');
        }
    }

    /**
     * Handle the Admin "updated" event.
     */
    public function updated(Admin $admin): void
    {
        // Ensure the user still has admin_ukm role if admin record is updated
        if ($admin->user && !$admin->user->hasRole('admin_ukm')) {
            $admin->user->assignRole('admin_ukm');
        }
    }

    /**
     * Handle the Admin "deleted" event.
     */
    public function deleted(Admin $admin): void
    {
        // Remove admin_ukm role when admin is deleted
        if ($admin->user && $admin->user->hasRole('admin_ukm')) {
            $admin->user->removeRole('admin_ukm');
        }
    }
}
