<?php

namespace App\Filament\UkmPanel\Widgets;

use App\Models\PaymentConfiguration;
use App\Models\PaymentTransaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class PaymentOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Get the current UKM admin's organization
        $ukmId = Auth::user()->admin->unit_kegiatan_id;

        // Count active payment configurations
        $activeConfigurations = PaymentConfiguration::where('unit_kegiatan_id', $ukmId)
            ->where('is_active', true)
            ->count();

        // Count total transactions
        $totalTransactions = PaymentTransaction::where('unit_kegiatan_id', $ukmId)->count();

        // Calculate total revenue
        $totalRevenue = PaymentTransaction::where('unit_kegiatan_id', $ukmId)
            ->where('status', 'paid')
            ->sum('amount');

        // Count pending transactions
        $pendingTransactions = PaymentTransaction::where('unit_kegiatan_id', $ukmId)
            ->where('status', 'pending')
            ->count();

        // Count transactions with custom files
        $transactionsWithFiles = PaymentTransaction::where('unit_kegiatan_id', $ukmId)
            ->whereNotNull('custom_files')
            ->where('custom_files', '!=', '[]')
            ->count();

        return [
            Stat::make('Active Payment Configurations', $activeConfigurations)
                ->description('Available payment types')
                ->color('info')
                ->icon('heroicon-o-credit-card'),

            Stat::make('Total Transactions', $totalTransactions)
                ->description('All payment transactions')
                ->color('warning')
                ->icon('heroicon-o-receipt-refund'),

            Stat::make('Total Revenue', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description('From completed payments')
                ->color('success')
                ->icon('heroicon-o-currency-dollar'),

            Stat::make('Pending Transactions', $pendingTransactions)
                ->description('Awaiting payment confirmation')
                ->color('danger')
                ->icon('heroicon-o-clock'),

            Stat::make('Transactions with Files', $transactionsWithFiles)
                ->description('With custom file uploads')
                ->color('primary')
                ->icon('heroicon-o-document'),
        ];
    }
} 