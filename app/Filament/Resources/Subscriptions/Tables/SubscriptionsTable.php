<?php

namespace App\Filament\Resources\Subscriptions\Tables;

use App\Models\Subscription;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('plan.name')
                    ->label('Plan')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'active', 'beta' => 'success',
                        'rejected', 'cancelled', 'expired' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('payment_ref')
                    ->label('Payment reference')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('payer_name')
                    ->label('Payer name')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('ends_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No pending subscriptions')
            ->emptyStateDescription('This list defaults to Pending. Clear the Status filter or choose Active to see approved subscriptions.')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'beta' => 'Beta',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve subscription payment?')
                    ->modalDescription('This will activate the subscription and set start/end dates from the plan.')
                    ->visible(fn (Subscription $record): bool => $record->status === 'pending')
                    ->action(function (Subscription $record): void {
                        $record->load('plan');
                        $record->activate();
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
                    ->visible(fn (Subscription $record): bool => $record->status === 'pending')
                    ->action(function (Subscription $record, array $data): void {
                        $record->update([
                            'status' => 'rejected',
                            'admin_note' => $data['admin_note'] ?? null,
                        ]);
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
