<?php

namespace App\Observers;
use Filament\Facades\Filament;
use App\Models\PaymentConfiguration;

class PaymentConfigurationsObserver
{
    public function creating(PaymentConfiguration $paymentConfig): void
    {
        $user = Filament::auth()?->user();
        $panel = Filament::getCurrentPanel();

        if (
            $user &&
            $user->hasRole('admin_ukm') &&
            $panel &&
            $panel->getId() === 'ukmPanel'
        ) {
            $paymentConfig->unit_kegiatan_id = $user->admin->unit_kegiatan_id;
        }
    }
}
