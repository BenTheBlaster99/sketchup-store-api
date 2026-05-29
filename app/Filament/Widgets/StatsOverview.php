<?php

namespace App\Filament\Widgets;

use App\Models\PackPurchase;
use App\Models\SketchupModel;
use App\Models\Subscription;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Pending subscriptions', Subscription::query()->where('status', 'pending')->count())
                ->description('BaridiMob / CCP payments to review')
                ->color('warning')
                ->icon('heroicon-o-clock'),

            Stat::make('Pending pack purchases', PackPurchase::query()->where('status', 'pending')->count())
                ->description('One-time category purchases to review')
                ->color('warning')
                ->icon('heroicon-o-shopping-bag'),

            Stat::make('Published models', SketchupModel::query()->where('is_published', true)->count())
                ->description('Live in the library')
                ->color('success')
                ->icon('heroicon-o-cube'),

            Stat::make('Total users', User::query()->count())
                ->description('Registered accounts')
                ->color('primary')
                ->icon('heroicon-o-users'),
        ];
    }
}
