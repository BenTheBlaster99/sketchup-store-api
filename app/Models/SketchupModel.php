<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
