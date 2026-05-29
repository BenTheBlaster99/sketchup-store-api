<?php

namespace App\Filament\Resources\SketchupModels\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SketchupModelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('file_key')
                    ->required(),
                TextInput::make('thumbnail_key'),
                TextInput::make('file_size_bytes')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('sketchup_version_min')
                    ->required()
                    ->numeric()
                    ->default(2020),
                Toggle::make('is_free_preview')
                    ->required(),
                Toggle::make('is_published')
                    ->required(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
