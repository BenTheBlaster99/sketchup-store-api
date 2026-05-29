<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('plan_id')
                    ->relationship('plan', 'name')
                    ->required(),
                Select::make('status')
                    ->options([
            'pending' => 'Pending',
            'active' => 'Active',
            'expired' => 'Expired',
            'cancelled' => 'Cancelled',
            'beta' => 'Beta',
            'rejected' => 'Rejected',
        ])
                    ->default('pending')
                    ->required(),
                TextInput::make('payment_ref'),
                TextInput::make('payer_name'),
                DateTimePicker::make('starts_at'),
                DateTimePicker::make('ends_at'),
                Textarea::make('admin_note')
                    ->columnSpanFull(),
            ]);
    }
}
