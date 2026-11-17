<?php

namespace App\Filament\Admin\Resources\EventAttendanceResource\Pages;

use App\Filament\Admin\Resources\EventAttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEventAttendance extends ViewRecord
{
    protected static string $resource = EventAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Undo check-in')
                ->icon('heroicon-m-arrow-uturn-left')
                ->requiresConfirmation()
                ->modalHeading('Undo check-in')
                ->modalDescription('Remove this check-in record? The attendee can be checked in again later.')
                ->successRedirectUrl(EventAttendanceResource::getUrl('index')),
        ];
    }
}
