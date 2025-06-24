<?php

namespace App\Filament\Widgets;

use App\Models\UnitKegiatan;
use App\Models\Admin;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UkmOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total UKM', UnitKegiatan::count())
                ->description('Student Organizations')
                ->color('success'),

            Stat::make('UKM Admins', Admin::count())
                ->description('Organization Administrators')
                ->color('info'),

            Stat::make('Open Registration', UnitKegiatan::where('open_registration', true)->count())
                ->description('Accepting New Members')
                ->color('warning'),

            Stat::make('Closed Registration', UnitKegiatan::where('open_registration', false)->count())
                ->description('Not Accepting Members')
                ->color('gray'),
        ];
    }
}
