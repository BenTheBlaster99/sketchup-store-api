<?php

namespace App\Filament\Resources\PackPurchases\Pages;

use App\Filament\Resources\PackPurchases\PackPurchaseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPackPurchase extends EditRecord
{
    protected static string $resource = PackPurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
