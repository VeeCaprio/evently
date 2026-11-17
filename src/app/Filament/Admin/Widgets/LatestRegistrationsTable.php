<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\EventRegistrationResource;
use App\Models\EventRegistration;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestRegistrationsTable extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Latest registrations')
            ->description('The newest guests across all events.')
            ->query(
                EventRegistration::query()
                    ->with(['attendance', 'event'])
                    ->latest()
                    ->limit(6),
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->weight('semibold')
                    ->description(fn (EventRegistration $record): string => $record->email),
                Tables\Columns\TextColumn::make('event.title')
                    ->label('Event')
                    ->limit(38),
                Tables\Columns\TextColumn::make('ticket_code')
                    ->label('Ticket')
                    ->fontFamily('mono')
                    ->copyable(),
                Tables\Columns\IconColumn::make('checked_in')
                    ->label('Checked in')
                    ->getStateUsing(fn (EventRegistration $record): bool => $record->attendance !== null)
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registered')
                    ->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(fn (EventRegistration $record): string => EventRegistrationResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
