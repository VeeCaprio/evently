<?php

namespace App\Filament\Admin\Resources\EventRegistrationResource\Pages;

use App\Filament\Admin\Resources\EventRegistrationResource;
use App\Models\EventRegistration;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListEventRegistrations extends ListRecords
{
    protected static string $resource = EventRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('checkInByCode')
                ->label('Check in by code')
                ->icon('heroicon-m-qr-code')
                ->color('success')
                ->form([
                    Forms\Components\TextInput::make('ticket_code')
                        ->label('Ticket code')
                        ->required()
                        ->placeholder('EVL-XXXX-XXXX')
                        ->helperText('Find the code on the attendee ticket.'),
                ])
                ->modalHeading('Check in by ticket code')
                ->modalDescription('Manual check-in is the fastest fallback when a QR scanner is not available.')
                ->modalSubmitActionLabel('Check in')
                ->action(function (array $data): void {
                    $registration = EventRegistration::query()
                        ->with(['attendance', 'event'])
                        ->where('ticket_code', strtoupper(trim($data['ticket_code'])))
                        ->first();

                    if (! $registration) {
                        Notification::make()
                            ->title('Ticket not found')
                            ->body('Check the code and try again.')
                            ->danger()
                            ->send();

                        return;
                    }

                    if ($registration->attendance) {
                        Notification::make()
                            ->title('Already checked in')
                            ->body("{$registration->name} checked in on {$registration->attendance->checked_in_at->format('d M Y, H:i')}.")
                            ->warning()
                            ->send();

                        return;
                    }

                    $registration->checkIn(auth()->user());

                    Notification::make()
                        ->title('Attendee checked in')
                        ->body("{$registration->name} · {$registration->event->title}")
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make(),
        ];
    }
}
