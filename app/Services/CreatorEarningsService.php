<?php

namespace App\Services;

use App\Models\Download;
use App\Models\SketchupModel;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CreatorEarningsService
{
    public function poolPercentage(): int
    {
        return (int) config('creator.pool_percentage', 40);
    }

    public function monthlySubscriptionRevenue(?Carbon $asOf = null): int
    {
        $asOf ??= now();

        return (int) Subscription::query()
            ->where('status', 'active')
            ->where('starts_at', '<=', $asOf)
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', $asOf))
            ->with('plan')
            ->get()
            ->sum(fn ($sub) => $sub->plan->price_dzd / $sub->plan->duration_months);
    }

    public function creatorModelIds(): Collection
    {
        return SketchupModel::query()
            ->whereNotNull('creator_id')
            ->pluck('id');
    }

    public function totalCreatorDownloads(Carbon $monthStart): int
    {
        return Download::query()
            ->whereIn('sketchup_model_id', $this->creatorModelIds())
            ->where('delivered_at', '>=', $monthStart)
            ->count();
    }

    public function creatorDownloads(int $creatorId, Carbon $monthStart): int
    {
        $modelIds = SketchupModel::query()
            ->where('creator_id', $creatorId)
            ->pluck('id');

        return Download::query()
            ->whereIn('sketchup_model_id', $modelIds)
            ->where('delivered_at', '>=', $monthStart)
            ->count();
    }

    public function forCreator(User $user): array
    {
        $poolPercent = $this->poolPercentage();
        $currentMonth = now()->startOfMonth();
        $monthlyRevenue = $this->monthlySubscriptionRevenue();
        $creatorPool = (int) round($monthlyRevenue * ($poolPercent / 100));
        $platformKeeps = $monthlyRevenue - $creatorPool;

        $modelIds = SketchupModel::query()
            ->where('creator_id', $user->id)
            ->pluck('id');

        $myDownloads = Download::query()
            ->whereIn('sketchup_model_id', $modelIds)
            ->where('delivered_at', '>=', $currentMonth)
            ->count();

        $totalCreatorDownloads = $this->totalCreatorDownloads($currentMonth);

        $mySharePercent = $totalCreatorDownloads > 0
            ? round(($myDownloads / $totalCreatorDownloads) * 100, 2)
            : 0;

        $myEarningsDzd = $totalCreatorDownloads > 0
            ? (int) round(($myDownloads / $totalCreatorDownloads) * $creatorPool)
            : 0;

        $perModel = Download::query()
            ->whereIn('sketchup_model_id', $modelIds)
            ->where('delivered_at', '>=', $currentMonth)
            ->selectRaw('sketchup_model_id, count(*) as download_count')
            ->groupBy('sketchup_model_id')
            ->orderByDesc('download_count')
            ->get()
            ->map(function ($row) {
                $model = SketchupModel::query()->find($row->sketchup_model_id);

                return [
                    'model_id' => $row->sketchup_model_id,
                    'model_name' => $model->name ?? 'Unknown',
                    'download_count' => $row->download_count,
                ];
            });

        return [
            'paypal_email' => $user->paypal_email,
            'platform_split' => 100 - $poolPercent,
            'creator_pool_split' => $poolPercent,
            'current_month' => [
                'label' => now()->format('F Y'),
                'total_platform_revenue' => $monthlyRevenue,
                'creator_pool_total' => $creatorPool,
                'platform_keeps' => $platformKeeps,
                'your_downloads' => $myDownloads,
                'total_creator_downloads' => $totalCreatorDownloads,
                'your_share_percent' => $mySharePercent,
                'your_estimated_earnings' => $myEarningsDzd,
            ],
            'top_models' => $perModel,
        ];
    }

    public function adminOverview(): array
    {
        $poolPercent = $this->poolPercentage();
        $currentMonth = now()->startOfMonth();
        $totalRevenue = $this->monthlySubscriptionRevenue();
        $creatorPool = (int) round($totalRevenue * ($poolPercent / 100));
        $platformKeeps = $totalRevenue - $creatorPool;
        $totalDownloads = $this->totalCreatorDownloads($currentMonth);

        $creatorBreakdown = User::query()
            ->where('is_creator', true)
            ->where('creator_status', 'approved')
            ->get()
            ->map(function (User $creator) use ($currentMonth, $totalDownloads, $creatorPool) {
                $myDownloads = $this->creatorDownloads($creator->id, $currentMonth);

                $sharePercent = $totalDownloads > 0
                    ? round(($myDownloads / $totalDownloads) * 100, 1)
                    : 0;

                $earningsDzd = $totalDownloads > 0
                    ? (int) round(($myDownloads / $totalDownloads) * $creatorPool)
                    : 0;

                return [
                    'name' => $creator->display_name ?? $creator->name,
                    'email' => $creator->email,
                    'paypal' => $creator->paypal_email ?? '—',
                    'downloads' => $myDownloads,
                    'share' => $sharePercent,
                    'payout_dzd' => $earningsDzd,
                ];
            })
            ->sortByDesc('downloads')
            ->values()
            ->all();

        return [
            'month' => now()->format('F Y'),
            'poolPercent' => $poolPercent,
            'totalRevenue' => $totalRevenue,
            'creatorPool' => $creatorPool,
            'platformKeeps' => $platformKeeps,
            'creatorBreakdown' => $creatorBreakdown,
        ];
    }
}
