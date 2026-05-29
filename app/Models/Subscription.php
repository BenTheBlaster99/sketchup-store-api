<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'payment_ref',
        'payer_name',
        'starts_at',
        'ends_at',
        'admin_note',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonths($this->plan->duration_months),
        ]);
    }
}
