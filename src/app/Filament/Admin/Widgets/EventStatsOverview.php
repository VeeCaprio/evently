<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\EventStatus;
use App\Filament\Admin\Resources\EventAttendanceResource;
use App\Filament\Admin\Resources\EventRegistrationResource;
use App\Filament\Admin\Resources\EventResource;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\EventRegistration;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EventStatsOverview extends StatsOverviewWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $publishedEvents = Event::query()->published()->count();
        $upcomingEvents = Event::query()->published()->upcoming()->count();
        $draftEvents = Event::query()->where('status', EventStatus::Draft->value)->count();

        $registrations = EventRegistration::query()->count();
        $registrationsThisWeek = EventRegistration::query()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $checkIns = EventAttendance::query()->count();
        $completedRegistrations = EventRegistration::query()
            ->whereHas('event', fn ($query) => $query->where('status', EventStatus::Completed->value))
            ->count();
        $completedCheckIns = EventAttendance::query()
            ->whereHas('registration.event', fn ($query) => $query->where('status', EventStatus::Completed->value))
            ->count();
        $attendanceRate = $completedRegistrations > 0
            ? (int) round(($completedCheckIns / $completedRegistrations) * 100)
            : 0;

        $upcomingQuota = Event::query()->published()->upcoming()->sum('quota');
        $upcomingRegistrationCount = EventRegistration::query()
            ->whereIn('event_id', Event::query()->published()->upcoming()->select('id'))
            ->count();
        $seatsLeft = max((int) $upcomingQuota - $upcomingRegistrationCount, 0);

        return [
            Stat::make('Published events', $publishedEvents)
                ->description("{$upcomingEvents} upcoming · {$draftEvents} drafts")
                ->descriptionIcon('heroicon-m-calendar-days')
                ->icon('heroicon-o-calendar-days')
                ->color('primary')
                ->url(EventResource::getUrl('index')),
            Stat::make('Registrations', number_format($registrations))
                ->description("+{$registrationsThisWeek} in the last 7 days")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->icon('heroicon-o-ticket')
                ->color('info')
                ->chart($this->dailyCounts(EventRegistration::class, 'created_at'))
                ->chartColor('info')
                ->url(EventRegistrationResource::getUrl('index')),
            Stat::make('Check-ins', number_format($checkIns))
                ->description("{$attendanceRate}% of completed-event guests")
                ->descriptionIcon('heroicon-m-check-badge')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->chart($this->dailyCounts(EventAttendance::class, 'checked_in_at'))
                ->chartColor('success')
                ->url(EventAttendanceResource::getUrl('index')),
            Stat::make('Seats remaining', number_format($seatsLeft))
                ->description("Across {$upcomingEvents} upcoming events")
                ->descriptionIcon('heroicon-m-users')
                ->icon('heroicon-o-users')
                ->color('warning')
                ->url(EventResource::getUrl('index')),
        ];
    }

    /**
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @return array<int, int>
     */
    private function dailyCounts(string $model, string $column): array
    {
        $counts = $model::query()
            ->where($column, '>=', now()->subDays(6)->startOfDay())
            ->selectRaw("DATE({$column}) as day, COUNT(*) as total")
            ->groupBy('day')
            ->pluck('total', 'day')
            ->map(fn ($total): int => (int) $total);

        return collect(range(6, 0))
            ->map(fn (int $days): int => $counts[now()->subDays($days)->toDateString()] ?? 0)
            ->all();
    }
}
