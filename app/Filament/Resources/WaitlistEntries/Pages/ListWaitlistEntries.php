<?php

namespace App\Filament\Resources\WaitlistEntries\Pages;

use App\Filament\Resources\WaitlistEntries\WaitlistEntryResource;
use App\Http\Controllers\WaitlistController;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListWaitlistEntries extends ListRecords
{
    protected static string $resource = WaitlistEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('notifyAll')
                ->label('Send launch email to all')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Send launch email?')
                ->modalDescription('This queues a launch email for everyone who has not been notified yet.')
                ->action(function (): void {
                    $response = app(WaitlistController::class)->notifyAll();
                    $message = $response->getData(true)['message'] ?? 'Done.';

                    Notification::make()
                        ->title($message)
                        ->success()
                        ->send();
                }),
        ];
    }
}
