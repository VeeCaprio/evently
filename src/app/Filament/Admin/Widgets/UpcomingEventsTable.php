<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\EventResource;
use App\Models\Event;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingEventsTable extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Upcoming events')
            ->description('The next events on your calendar.')
            ->query(
                Event::query()
                    ->published()
                    ->upcoming()
                    ->with('category')
                    ->withCount('registrations')
                    ->orderBy('start_at')
                    ->limit(5),
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->weight('semibold')
                    ->description(fn (Event $record): string => $record->location),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('start_at')
                    ->label('Starts')
                    ->dateTime('d M Y, H:i'),
                Tables\Columns\TextColumn::make('registrations_count')
                    ->label('Registered')
                    ->badge()
                    ->formatStateUsing(fn (int $state, Event $record): string => "{$state} / {$record->quota}")
                    ->color(fn (Event $record): string => $record->isSoldOut() ? 'danger' : 'success'),
            ])
            ->actions([
                Tables\Actions\Action::make('manage')
                    ->label('Manage')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(fn (Event $record): string => EventResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
