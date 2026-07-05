<?php

namespace App\Filament\Resources\SketchupModels\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class SketchupModelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                Select::make('creator_id')
                    ->label('Creator')
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state ?? ''))),
                        TextInput::make('slug')->required(),
                        Select::make('group')
                            ->options([
                                'type' => 'Type',
                                'material' => 'Material',
                                'style' => 'Style',
                            ])
                            ->default('style')
                            ->required(),
                    ]),
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
                Select::make('review_status')
                    ->options([
                        'approved' => 'Approved',
                        'pending_review' => 'Pending review',
                        'rejected' => 'Rejected',
                    ])
                    ->default('approved')
                    ->required(),
                Textarea::make('rejection_note')
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
