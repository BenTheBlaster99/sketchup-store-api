<?php

namespace App\Filament\Resources\CreatorApplications;

use App\Filament\Resources\CreatorApplications\Pages\EditCreatorApplication;
use App\Filament\Resources\CreatorApplications\Pages\ListCreatorApplications;
use App\Filament\Resources\CreatorApplications\Schemas\CreatorApplicationForm;
use App\Filament\Resources\CreatorApplications\Tables\CreatorApplicationsTable;
use App\Models\CreatorApplication;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CreatorApplicationResource extends Resource
{
    protected static ?string $model = CreatorApplication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserPlus;

    protected static string|UnitEnum|null $navigationGroup = 'Creators';

    protected static ?string $navigationLabel = 'Applications';

    protected static ?string $modelLabel = 'creator application';

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'pending')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function form(Schema $schema): Schema
    {
        return CreatorApplicationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CreatorApplicationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCreatorApplications::route('/'),
            'edit' => EditCreatorApplication::route('/{record}/edit'),
        ];
    }
}
