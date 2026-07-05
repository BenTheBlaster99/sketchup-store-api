<?php

namespace App\Filament\Resources\CreatorApplications\Tables;

use App\Models\CreatorApplication;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CreatorApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('portfolio_url')
                    ->label('Portfolio')
                    ->url(fn (CreatorApplication $record): ?string => $record->portfolio_url)
                    ->openUrlInNewTab()
                    ->placeholder('—'),
                TextColumn::make('paypal_email')
                    ->copyable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (CreatorApplication $record): bool => $record->status === 'pending')
                    ->action(function (CreatorApplication $record): void {
                        $record->update(['status' => 'approved']);
                        $record->user->update([
                            'is_creator' => true,
                            'creator_status' => 'approved',
                            'display_name' => $record->user->display_name ?: $record->user->name,
                            'paypal_email' => $record->paypal_email,
                            'bio' => $record->bio,
                        ]);
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('admin_note')
                            ->label('Reason (optional)')
                            ->rows(3),
                    ])
                    ->visible(fn (CreatorApplication $record): bool => $record->status === 'pending')
                    ->action(function (CreatorApplication $record, array $data): void {
                        $record->update([
                            'status' => 'rejected',
                            'admin_note' => $data['admin_note'] ?? null,
                        ]);
                        $record->user->update([
                            'is_creator' => false,
                            'creator_status' => 'none',
                        ]);
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
