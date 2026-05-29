<?php

namespace App\Services;

use App\Models\SketchupModel;
use App\Models\User;

class AccessService
{
    public function canDownload(User $user, SketchupModel $model): bool
    {
        return $user->canAccess($model);
    }

    public function getUserAccessType(User $user): string
    {
        if ($user->is_beta) {
            return 'beta';
        }

        if ($user->hasActiveSubscription()) {
            return 'subscription';
        }

        return 'pack_only';
    }

    public function getAccessibleCategoryIds(User $user): array
    {
        if ($user->is_beta || $user->hasActiveSubscription()) {
            return [];
        }

        return $user->packPurchases()
            ->where('status', 'active')
            ->pluck('category_id')
            ->toArray();
    }
}
