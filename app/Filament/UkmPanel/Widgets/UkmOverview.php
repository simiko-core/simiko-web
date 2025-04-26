<?php

namespace App\Filament\UkmPanel\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Admin;
use App\Models\User;

class UkmOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [];
    }
}
