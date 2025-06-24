<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Admin;
use App\Models\Feed;
use App\Models\PendaftaranAnggota;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class SystemInfoWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Database status
        try {
            DB::connection()->getPdo();
            $dbStatus = 'Online';
            $dbColor = 'success';
        } catch (\Exception $e) {
            $dbStatus = 'Offline';
            $dbColor = 'danger';
        }

        // Calculate user counts
        $totalUsers = User::count() - Admin::count() - 1; // Exclude admins and super admin
        $totalContent = Feed::count();
        $pendingApplications = PendaftaranAnggota::where('status', 'pending')->count();

        return [
            Stat::make('Database', $dbStatus)
                ->description('System Status')
                ->color($dbColor),

            Stat::make('Active Users', $totalUsers)
                ->description('Registered Students')
                ->color('info'),

            Stat::make('Total Content', $totalContent)
                ->description('Posts & Events')
                ->color('warning'),

            Stat::make('Pending Applications', $pendingApplications)
                ->description('Awaiting Review')
                ->color('danger'),
        ];
    }
} 