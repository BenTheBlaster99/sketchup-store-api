<?php

namespace App\Filament\Resources\Plans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('price_dzd')
                    ->required()
                    ->numeric(),
                TextInput::make('duration_months')
                    ->required()
                    ->numeric(),
                Toggle::make('is_student')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
