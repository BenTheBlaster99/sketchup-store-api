<?php

namespace App\Filament\Resources\SketchupModels;

use App\Filament\Resources\SketchupModels\Pages\CreateSketchupModel;
use App\Filament\Resources\SketchupModels\Pages\EditSketchupModel;
use App\Filament\Resources\SketchupModels\Pages\ListSketchupModels;
use App\Filament\Resources\SketchupModels\Schemas\SketchupModelForm;
use App\Filament\Resources\SketchupModels\Tables\SketchupModelsTable;
use App\Models\SketchupModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SketchupModelResource extends Resource
{
    protected static ?string $model = SketchupModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = '3D Models';

    protected static ?string $modelLabel = '3D model';

    protected static ?string $pluralModelLabel = '3D models';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SketchupModelForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SketchupModelsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSketchupModels::route('/'),
            'create' => CreateSketchupModel::route('/create'),
            'edit' => EditSketchupModel::route('/{record}/edit'),
        ];
    }
}
