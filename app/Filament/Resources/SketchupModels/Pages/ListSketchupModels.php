<?php

namespace App\Filament\Resources\SketchupModels\Pages;

use App\Filament\Resources\SketchupModels\SketchupModelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSketchupModels extends ListRecords
{
    protected static string $resource = SketchupModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
