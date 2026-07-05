<?php

namespace App\Filament\Resources\CreatorApplications\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CreatorApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'email')
                    ->disabled(),
                Textarea::make('bio')
                    ->rows(5)
                    ->columnSpanFull(),
                TextInput::make('portfolio_url')
                    ->url(),
                TextInput::make('paypal_email')
                    ->email(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
                Textarea::make('admin_note')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
