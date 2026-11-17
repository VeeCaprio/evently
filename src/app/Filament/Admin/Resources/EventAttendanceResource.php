<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EventAttendanceResource\Pages;
use App\Models\Event;
use App\Models\EventAttendance;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EventAttendanceResource extends Resource
{
    protected static ?string $model = EventAttendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $navigationGroup = 'Attendees';

    protected static ?string $navigationLabel = 'Check-ins';

    protected static ?string $modelLabel = 'check-in';

    protected static ?string $pluralModelLabel = 'check-ins';

    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['registration.event', 'checkedInBy']))
            ->columns([
                Tables\Columns\TextColumn::make('registration.name')
                    ->label('Attendee')
                    ->searchable()
                    ->weight('semibold')
                    ->description(fn (EventAttendance $record): ?string => $record->registration?->email),
                Tables\Columns\TextColumn::make('registration.ticket_code')
                    ->label('Ticket')
                    ->fontFamily('mono')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('registration.event.title')
                    ->label('Event')
                    ->limit(38)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('checked_in_at')
                    ->label('Checked in at')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('checkedInBy.name')
                    ->label('Checked in by')
                    ->placeholder('System')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event_id')
                    ->label('Event')
                    ->options(fn (): array => Event::query()
                        ->orderByDesc('start_at')
                        ->pluck('title', 'id')
                        ->all())
                    ->searchable()
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'],
                        fn (Builder $query, $eventId) => $query->whereHas(
                            'registration',
                            fn (Builder $query) => $query->where('event_id', $eventId),
                        ),
                    )),
            ])
            ->actions([
                Tables\Actions\Action::make('viewRegistration')
                    ->label('View registration')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(fn (EventAttendance $record): string => EventRegistrationResource::getUrl('view', [
                        'record' => $record->event_registration_id,
                    ])),
                Tables\Actions\DeleteAction::make()
                    ->label('Undo check-in')
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->requiresConfirmation()
                    ->modalHeading('Undo check-in')
                    ->modalDescription(fn (EventAttendance $record): string => "Remove the check-in record for {$record->registration?->name}?")
                    ->successNotificationTitle('Check-in removed'),
            ])
            ->defaultSort('checked_in_at', 'desc')
            ->emptyStateHeading('No check-ins yet')
            ->emptyStateDescription('Checked-in guests will appear here with their event and ticket details.');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Check-in')
                ->schema([
                    Infolists\Components\TextEntry::make('registration.name')->label('Attendee'),
                    Infolists\Components\TextEntry::make('registration.email')->label('Email')->copyable(),
                    Infolists\Components\TextEntry::make('registration.event.title')->label('Event'),
                    Infolists\Components\TextEntry::make('registration.ticket_code')
                        ->label('Ticket')
                        ->fontFamily('mono')
                        ->copyable(),
                    Infolists\Components\TextEntry::make('checked_in_at')
                        ->label('Checked in at')
                        ->dateTime('d M Y, H:i')
                        ->badge()
                        ->color('success'),
                    Infolists\Components\TextEntry::make('checkedInBy.name')
                        ->label('Checked in by')
                        ->placeholder('System'),
                ])
                ->columns(2),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEventAttendances::route('/'),
            'view' => Pages\ViewEventAttendance::route('/{record}'),
        ];
    }
}
