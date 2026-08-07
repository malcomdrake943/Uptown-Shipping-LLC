<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $pending     = Order::where('status', 'pending')->count();
        $purchased   = Order::where('status', 'purchased')->count();
        $shipped     = Order::where('status', 'shipped')->count();
        $delivered   = Order::where('status', 'delivered')->count();

        return [
            Stat::make('⏳ Pending', $pending)
                ->color('gray')
                ->description('Awaiting processing'),

            Stat::make('🛒 Purchased', $purchased)
                ->color('primary')
                ->description('Bought, awaiting shipment'),

            Stat::make('🚚 Shipped', $shipped)
                ->color('info')
                ->description('In transit'),

            Stat::make('✅ Delivered', $delivered)
                ->color('success')
                ->description('Completed orders'),
        ];
    }
}
