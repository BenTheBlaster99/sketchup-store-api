<?php

namespace App\Filament\Pages;

use App\Services\CreatorEarningsService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class CreatorEarningsOverview extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Creators';

    protected static ?string $navigationLabel = 'Monthly payouts';

    protected static ?int $navigationSort = 20;

    protected string $view = 'filament.pages.creator-earnings-overview';

    public array $creatorBreakdown = [];

    public int $totalRevenue = 0;

    public int $creatorPool = 0;

    public int $platformKeeps = 0;

    public int $poolPercent = 40;

    public string $month = '';

    public function mount(CreatorEarningsService $earnings): void
    {
        $overview = $earnings->adminOverview();

        $this->month = $overview['month'];
        $this->poolPercent = $overview['poolPercent'];
        $this->totalRevenue = $overview['totalRevenue'];
        $this->creatorPool = $overview['creatorPool'];
        $this->platformKeeps = $overview['platformKeeps'];
        $this->creatorBreakdown = $overview['creatorBreakdown'];
    }
}
