<?php

namespace App\Filament\Resources\CategoryPacks\Pages;

use App\Filament\Resources\CategoryPacks\CategoryPackResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCategoryPack extends EditRecord
{
    protected static string $resource = CategoryPackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
