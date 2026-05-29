<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'name',
    'email',
    'password',
    'is_admin',
    'is_student',
    'is_beta',
    'hardware_id',
])]
#[Hidden(['password', 'remember_token', 'hardware_id'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_student' => 'boolean',
            'is_beta' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin;
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->whereIn('status', ['active', 'beta'])
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->latestOfMany();
    }

    public function packPurchases(): HasMany
    {
        return $this->hasMany(PackPurchase::class);
    }

    public function userSessions(): HasMany
    {
        return $this->hasMany(UserSession::class);
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    public function ownsPack(int $categoryId): bool
    {
        return $this->packPurchases()
            ->where('category_id', $categoryId)
            ->where('status', 'active')
            ->exists();
    }

    public function canAccess(SketchupModel $model): bool
    {
        if ($model->is_free_preview) {
            return true;
        }

        if ($this->is_beta) {
            return true;
        }

        if ($this->hasActiveSubscription()) {
            return true;
        }

        if ($this->ownsPack($model->category_id)) {
            return true;
        }

        return false;
    }
}
