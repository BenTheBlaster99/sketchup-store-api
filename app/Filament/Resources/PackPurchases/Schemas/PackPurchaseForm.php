<?php

namespace App\Filament\Resources\PackPurchases\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PackPurchaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                Select::make('status')
                    ->options(['pending' => 'Pending', 'active' => 'Active', 'rejected' => 'Rejected'])
                    ->default('pending')
                    ->required(),
                TextInput::make('price_paid_dzd')
                    ->required()
                    ->numeric(),
                TextInput::make('payment_ref'),
                TextInput::make('payer_name'),
                Textarea::make('admin_note')
                    ->columnSpanFull(),
            ]);
    }
}
