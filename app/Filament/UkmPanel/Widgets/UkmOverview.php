<?php

namespace App\Filament\UkmPanel\Widgets;

use App\Models\PendaftaranAnggota;
use App\Models\Feed;
use App\Models\UnitKegiatan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class UkmOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Get the current UKM admin's organization
        $ukmId = Auth::user()->admin->unit_kegiatan_id;
        $ukm = UnitKegiatan::find($ukmId);

        // Count accepted members
        $totalMembers = PendaftaranAnggota::where('unit_kegiatan_id', $ukmId)
            ->where('status', 'accepted')
            ->count();

        // Count pending applications
        $pendingApplications = PendaftaranAnggota::where('unit_kegiatan_id', $ukmId)
            ->where('status', 'pending')
            ->count();

        // Count total content (posts and events)
        $totalContent = Feed::where('unit_kegiatan_id', $ukmId)->count();

        // Registration status
        $registrationStatus = $ukm->open_registration ? 'Open' : 'Closed';
        $registrationColor = $ukm->open_registration ? 'success' : 'danger';

        return [
            Stat::make('Total Members', $totalMembers)
                ->description('Accepted Members')
                ->color('success'),

            Stat::make('Pending Applications', $pendingApplications)
                ->description('Awaiting Review')
                ->color('warning'),

            Stat::make('Total Content', $totalContent)
                ->description('Posts & Events')
                ->color('info'),

            Stat::make('Registration Status', $registrationStatus)
                ->description('New Member Applications')
                ->color($registrationColor),
        ];
    }
}
