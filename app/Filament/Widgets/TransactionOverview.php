<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Transactions', Transaksi::query()->count()),
            Stat::make('Total Amount (Pending)', 'Rp. ' . number_format(Transaksi::query()->where('status_payment', 'pending')->sum('grand_total'), 0, ',', '.')),
            Stat::make('Total Amount (Dibayar)', 'Rp. ' . number_format(Transaksi::query()->where('status_payment', 'dibayar')->sum('grand_total'), 0, ',', '.')),
        ];
    }
}
