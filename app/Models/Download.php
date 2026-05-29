<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Download extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'sketchup_model_id', 'ip_address', 'delivered_at'];

    protected $casts = ['delivered_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sketchupModel(): BelongsTo
    {
        return $this->belongsTo(SketchupModel::class);
    }
}
