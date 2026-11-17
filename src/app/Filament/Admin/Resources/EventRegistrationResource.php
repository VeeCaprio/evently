<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EventRegistrationResource\Pages;
use App\Models\Event;
use App\Models\EventRegistration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class EventRegistrationResource extends Resource
{
    protected static ?string $model = EventRegistration::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Attendees';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) EventRegistration::query()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'gray';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Attendee')
                ->description('Every attendee gets a unique ticket code after saving.')
                ->schema([
                    Forms\Components\Select::make('event_id')
                        ->label('Event')
                        ->relationship('event', 'title')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(120)
                        ->prefixIcon('heroicon-m-user'),
                    Forms\Components\TextInput::make('email')
                        ->required()
                        ->email()
                        ->maxLength(180)
                        ->prefixIcon('heroicon-m-envelope')
                        ->rules([
                            function (Get $get, ?EventRegistration $record): Unique {
                                $rule = Rule::unique('event_registrations', 'email')
                                    ->where(fn ($query) => $query->where('event_id', $get('event_id')));

                                return $record ? $rule->ignore($record) : $rule;
                            },
                        ])
                        ->helperText('One registration per email address, per event.'),
                    Forms\Components\TextInput::make('phone')
                        ->required()
                        ->tel()
                        ->maxLength(30)
                        ->prefixIcon('heroicon-m-phone'),
                    Forms\Components\TextInput::make('ticket_code')
                        ->label('Ticket code')
                        ->disabled()
                        ->dehydrated(false)
                        ->prefixIcon('heroicon-m-qr-code')
                        ->placeholder('Generated on save')
                        ->helperText('Generated automatically. Share this code with the attendee.'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Check-in')
                ->visible(fn (?EventRegistration $record): bool => $record !== null)
                ->schema([
                    Forms\Components\Placeholder::make('check_in_status')
                        ->label('Status')
                        ->content(fn (?EventRegistration $record): string => $record?->attendance
                            ? 'Checked in on ' . $record->attendance->checked_in_at->format('d M Y, H:i')
                            : 'Not checked in yet'),
                    Forms\Components\Placeholder::make('checked_in_by')
                        ->label('Checked in by')
                        ->content(fn (?EventRegistration $record): string => $record?->attendance?->checkedInBy?->name ?? '—'),
                ])
                ->columns(2),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Attendee')
                ->schema([
                    Infolists\Components\TextEntry::make('name'),
                    Infolists\Components\TextEntry::make('email')->copyable(),
                    Infolists\Components\TextEntry::make('phone'),
                    Infolists\Components\TextEntry::make('event.title')->label('Event'),
                    Infolists\Components\TextEntry::make('ticket_code')
                        ->fontFamily('mono')
                        ->copyable(),
                    Infolists\Components\TextEntry::make('created_at')
                        ->label('Registered at')
                        ->dateTime('d M Y, H:i'),
                ])
                ->columns(2),
            Infolists\Components\Section::make('Check-in')
                ->schema([
                    Infolists\Components\TextEntry::make('attendance.checked_in_at')
                        ->label('Checked in at')
                        ->dateTime('d M Y, H:i')
                        ->placeholder('Not checked in yet')
                        ->badge()
                        ->color(fn ($state): string => $state ? 'success' : 'gray'),
                    Infolists\Components\TextEntry::make('attendance.checkedInBy.name')
                        ->label('Checked in by')
                        ->placeholder('—'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['attendance', 'event']))
            ->columns([
                Tables\Columns\TextColumn::make('ticket_code')
                    ->label('Ticket')
                    ->fontFamily('mono')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn (EventRegistration $record): string => $record->email),
                Tables\Columns\TextColumn::make('event.title')
                    ->label('Event')
                    ->limit(38)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('attendance.checked_in_at')
                    ->label('Check-in')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Not checked in')
                    ->badge()
                    ->color(fn ($state): string => $state ? 'success' : 'gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event_id')
                    ->label('Event')
                    ->options(fn (): array => Event::query()
                        ->orderByDesc('start_at')
                        ->pluck('title', 'id')
                        ->all())
                    ->searchable(),
                Tables\Filters\SelectFilter::make('attendance_status')
                    ->label('Check-in status')
                    ->options([
                        'checked_in' => 'Checked in',
                        'not_checked_in' => 'Not checked in',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'checked_in' => $query->whereHas('attendance'),
                            'not_checked_in' => $query->whereDoesntHave('attendance'),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('checkIn')
                    ->label('Check in')
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Check in attendee')
                    ->modalDescription(fn (EventRegistration $record): string => "Mark {$record->name} as checked in for {$record->event->title}?")
                    ->action(function (EventRegistration $record): void {
                        $record->checkIn(auth()->user());

                        Notification::make()
                            ->title('Attendee checked in')
                            ->body("{$record->name} · {$record->ticket_code}")
                            ->success()
                            ->send();
                    })
                    ->visible(fn (EventRegistration $record): bool => $record->attendance === null),
                Tables\Actions\Action::make('undoCheckIn')
                    ->label('Undo check-in')
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Undo check-in')
                    ->modalDescription(fn (EventRegistration $record): string => "Remove the check-in record for {$record->name}?")
                    ->action(function (EventRegistration $record): void {
                        $record->attendance()->delete();

                        Notification::make()
                            ->title('Check-in removed')
                            ->body("{$record->name} can be checked in again.")
                            ->warning()
                            ->send();
                    })
                    ->visible(fn (EventRegistration $record): bool => $record->attendance !== null),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalDescription('This permanently deletes the registration and its check-in record.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('checkInSelected')
                        ->label('Check in selected')
                        ->icon('heroicon-m-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalDescription('Check in every selected attendee who is not checked in yet.')
                        ->action(function (Collection $records): void {
                            $checkedIn = 0;

                            $records->each(function (EventRegistration $record) use (&$checkedIn): void {
                                if ($record->attendance === null) {
                                    $record->checkIn(auth()->user());
                                    $checkedIn++;
                                }
                            });

                            Notification::make()
                                ->title("{$checkedIn} attendee(s) checked in")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No registrations yet')
            ->emptyStateDescription('Registrations will appear here as guests sign up for your events.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEventRegistrations::route('/'),
            'create' => Pages\CreateEventRegistration::route('/create'),
            'view' => Pages\ViewEventRegistration::route('/{record}'),
            'edit' => Pages\EditEventRegistration::route('/{record}/edit'),
        ];
    }
}
