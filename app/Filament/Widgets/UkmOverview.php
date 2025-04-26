<?php

namespace App\Filament\Widgets;

use App\Models\Admin;
use App\Models\UnitKegiatan;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UkmOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            // total ukm
            Stat::make('Total UKM', UnitKegiatan::count())
                ->label('Total UKM')
                ->icon('heroicon-o-users'),

            // total user witout admin role
            Stat::make('Total User', User::count() - Admin::count() - 1)
                ->label('Total User')
                ->icon('heroicon-o-user-group'),
        ];
    }
}
