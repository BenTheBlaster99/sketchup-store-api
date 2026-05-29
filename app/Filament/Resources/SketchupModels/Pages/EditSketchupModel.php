<?php

namespace App\Filament\Resources\SketchupModels\Pages;

use App\Filament\Resources\SketchupModels\SketchupModelResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSketchupModel extends EditRecord
{
    protected static string $resource = SketchupModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
