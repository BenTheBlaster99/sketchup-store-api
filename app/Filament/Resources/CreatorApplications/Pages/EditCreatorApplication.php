<?php

namespace App\Filament\Resources\CreatorApplications\Pages;

use App\Filament\Resources\CreatorApplications\CreatorApplicationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCreatorApplication extends EditRecord
{
    protected static string $resource = CreatorApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
