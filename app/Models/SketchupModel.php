<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SketchupModel extends Model
{
    protected $table = 'sketchup_models';

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'file_key',
        'thumbnail_key',
        'file_size_bytes',
        'sketchup_version_min',
        'is_free_preview',
        'is_published',
        'sort_order',
    ];

    protected $hidden = ['file_key'];

    protected $casts = [
        'is_free_preview' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(ModelFavorite::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'sketchup_model_tag');
    }
}
