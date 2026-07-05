<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Filament\Resources\Users\Pages\CreateUser;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(100),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->password()
                    ->label('Password')
                    ->required(fn ($livewire): bool => $livewire instanceof CreateUser)
                    ->minLength(8)
                    ->helperText('Leave blank when editing to keep the current password.')
                    ->dehydrated(fn (?string $state): bool => filled($state)),
                Toggle::make('is_admin')
                    ->label('Admin access')
                    ->helperText('Can log into this SketchLib dashboard.')
                    ->default(false),
                Toggle::make('is_student')
                    ->label('Student account')
                    ->default(false),
                Toggle::make('is_beta')
                    ->label('Beta tester')
                    ->default(false),
                Toggle::make('is_creator')
                    ->label('Creator account')
                    ->default(false),
                Select::make('creator_status')
                    ->options([
                        'none' => 'None',
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'suspended' => 'Suspended',
                    ])
                    ->default('none')
                    ->required(),
                TextInput::make('display_name')
                    ->label('Creator display name')
                    ->maxLength(100),
                Textarea::make('bio')
                    ->label('Creator bio')
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('paypal_email')
                    ->label('PayPal email')
                    ->email()
                    ->maxLength(255),
                TextInput::make('hardware_id')
                    ->label('Hardware ID')
                    ->helperText('Set automatically by the SketchUp plugin.')
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }
}
