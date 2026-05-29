<?php

namespace App\Filament\Resources\CategoryPacks\Pages;

use App\Filament\Resources\CategoryPacks\CategoryPackResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategoryPack extends CreateRecord
{
    protected static string $resource = CategoryPackResource::class;
}
