<?php

namespace App\Filament\Resources\PackPurchases;

use App\Filament\Resources\PackPurchases\Pages\CreatePackPurchase;
use App\Filament\Resources\PackPurchases\Pages\EditPackPurchase;
use App\Filament\Resources\PackPurchases\Pages\ListPackPurchases;
use App\Filament\Resources\PackPurchases\Schemas\PackPurchaseForm;
use App\Filament\Resources\PackPurchases\Tables\PackPurchasesTable;
use App\Models\PackPurchase;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PackPurchaseResource extends Resource
{
    protected static ?string $model = PackPurchase::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static string|UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Pack purchases';

    protected static ?string $modelLabel = 'pack purchase';

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::query()->where('status', 'pending')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return PackPurchaseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PackPurchasesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPackPurchases::route('/'),
            'create' => CreatePackPurchase::route('/create'),
            'edit' => EditPackPurchase::route('/{record}/edit'),
        ];
    }
}
