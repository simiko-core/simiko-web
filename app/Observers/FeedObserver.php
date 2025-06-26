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

    public function deleting(Feed $feed): void
    {
        // Only for event type with payment config
        if ($feed->type === 'event' && $feed->payment_configuration_id) {
            $paymentConfig = $feed->paymentConfiguration;
            if ($paymentConfig) {
                $otherFeedsCount = $paymentConfig->feeds()->where('id', '!=', $feed->id)->count();
                $transactionsCount = $paymentConfig->transactions()->count();
                if ($otherFeedsCount == 0 && $transactionsCount == 0) {
                    $paymentConfig->delete();
                }
            }
        }
    }
}
