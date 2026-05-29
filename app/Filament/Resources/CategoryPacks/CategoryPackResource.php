<?php

namespace App\Filament\Resources\CategoryPacks;

use App\Filament\Resources\CategoryPacks\Pages\CreateCategoryPack;
use App\Filament\Resources\CategoryPacks\Pages\EditCategoryPack;
use App\Filament\Resources\CategoryPacks\Pages\ListCategoryPacks;
use App\Filament\Resources\CategoryPacks\Schemas\CategoryPackForm;
use App\Filament\Resources\CategoryPacks\Tables\CategoryPacksTable;
use App\Models\CategoryPack;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CategoryPackResource extends Resource
{
    protected static ?string $model = CategoryPack::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Category packs';

    protected static ?string $modelLabel = 'category pack';

    public static function form(Schema $schema): Schema
    {
        return CategoryPackForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoryPacksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategoryPacks::route('/'),
            'create' => CreateCategoryPack::route('/create'),
            'edit' => EditCategoryPack::route('/{record}/edit'),
        ];
    }
}
