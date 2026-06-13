<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = ['name', 'slug'];

    public function sketchupModels(): BelongsToMany
    {
        return $this->belongsToMany(SketchupModel::class, 'sketchup_model_tag');
    }
}
