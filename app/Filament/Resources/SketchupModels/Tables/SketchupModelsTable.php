<?php

namespace App\Filament\Resources\SketchupModels\Tables;

use App\Models\SketchupModel;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SketchupModelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->searchable(),
                TextColumn::make('creator.display_name')
                    ->label('Creator')
                    ->placeholder('Sarah (owner)')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('file_key')
                    ->searchable(),
                TextColumn::make('thumbnail_key')
                    ->searchable(),
                TextColumn::make('file_size_bytes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sketchup_version_min')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_free_preview')
                    ->boolean(),
                IconColumn::make('is_published')
                    ->boolean(),
                TextColumn::make('review_status')
                    ->label('Review')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending_review' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('likes_count')
                    ->label('Likes')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('tags.name')
                    ->label('Tags')
                    ->badge()
                    ->separator(','),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('review_status')
                    ->label('Review status')
                    ->options([
                        'approved' => 'Approved',
                        'pending_review' => 'Pending review',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->recordActions([
                Action::make('approve_model')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (SketchupModel $record): bool => $record->review_status === 'pending_review')
                    ->action(fn (SketchupModel $record): bool => $record->update([
                        'review_status' => 'approved',
                        'rejection_note' => null,
                        'is_published' => true,
                    ])),
                Action::make('reject_model')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('rejection_note')
                            ->label('Reason')
                            ->required()
                            ->rows(3),
                    ])
                    ->visible(fn (SketchupModel $record): bool => $record->review_status === 'pending_review')
                    ->action(fn (SketchupModel $record, array $data): bool => $record->update([
                        'review_status' => 'rejected',
                        'rejection_note' => $data['rejection_note'],
                        'is_published' => false,
                    ])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
