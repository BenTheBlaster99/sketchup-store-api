<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModelFavorite extends Model
{
    protected $fillable = ['user_id', 'sketchup_model_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sketchupModel(): BelongsTo
    {
        return $this->belongsTo(SketchupModel::class);
    }
}
