<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'cover_image_key', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function models(): HasMany
    {
        return $this->hasMany(SketchupModel::class);
    }

    public function pack(): HasOne
    {
        return $this->hasOne(CategoryPack::class);
    }
}
