<?php

namespace App\Filament\Resources\CategoryPacks\Pages;

use App\Filament\Resources\CategoryPacks\CategoryPackResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCategoryPacks extends ListRecords
{
    protected static string $resource = CategoryPackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
