<?php

namespace App\Filament\Resources\Downloads;

use App\Filament\Resources\Downloads\Pages\ManageDownloads;
use App\Models\Download;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class DownloadResource extends Resource
{
    protected static ?string $model = Download::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownTray;

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Download log';

    protected static ?string $modelLabel = 'download';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sketchupModel.name')
                    ->label('Model')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('ip_address')
                    ->label('IP address')
                    ->toggleable(),
                TextColumn::make('delivered_at')
                    ->label('Delivered at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('delivered_at', 'desc')
            ->filters([])
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageDownloads::route('/'),
        ];
    }
}
