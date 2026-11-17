<?php

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\EventCategory;
use App\Models\EventRegistration;
use App\Models\User;

it('connects events to categories and registrations', function () {
    $category = EventCategory::factory()->create();
    $event = Event::factory()->create(['event_category_id' => $category->getKey()]);
    $registration = EventRegistration::factory()->create(['event_id' => $event->getKey()]);

    expect($event->category->is($category))->toBeTrue()
        ->and($category->events()->count())->toBe(1)
        ->and($event->registrations()->count())->toBe(1)
        ->and($registration->event->is($event))->toBeTrue();
});

it('casts event attributes and status', function () {
    $event = Event::factory()->create([
        'status' => EventStatus::Published,
        'ticket_price' => 125000,
        'is_featured' => true,
    ]);

    expect($event->status)->toBe(EventStatus::Published)
        ->and($event->start_at)->toBeInstanceOf(Illuminate\Support\Carbon::class)
        ->and($event->end_at)->toBeInstanceOf(Illuminate\Support\Carbon::class)
        ->and($event->is_featured)->toBeTrue()
        ->and((float) $event->ticket_price)->toBe(125000.0);
});

it('filters published, featured, upcoming, past, and searched events', function () {
    $category = EventCategory::factory()->create(['slug' => 'technology']);

    $published = Event::factory()->create([
        'event_category_id' => $category->getKey(),
        'status' => EventStatus::Published,
        'is_featured' => true,
        'title' => 'Laravel Horizon Summit',
        'start_at' => now()->addDays(10),
        'end_at' => now()->addDays(10)->addHours(3),
    ]);

    $draft = Event::factory()->draft()->create([
        'start_at' => now()->addDays(12),
        'end_at' => now()->addDays(12)->addHours(3),
    ]);

    $past = Event::factory()->completed()->create();

    expect(Event::published()->pluck('id')->all())->toContain($published->getKey())
        ->and(Event::published()->pluck('id')->all())->not->toContain($draft->getKey())
        ->and(Event::featured()->pluck('id')->all())->toContain($published->getKey())
        ->and(Event::upcoming()->pluck('id')->all())->toContain($published->getKey())
        ->and(Event::past()->pluck('id')->all())->toContain($past->getKey())
        ->and(Event::search('Horizon')->pluck('id')->all())->toContain($published->getKey())
        ->and(Event::inCategory('technology')->pluck('id')->all())->toContain($published->getKey());
});

it('calculates remaining quota from registrations', function () {
    $event = Event::factory()->create(['quota' => 3]);

    EventRegistration::factory()->count(2)->create(['event_id' => $event->getKey()]);

    $event->refresh();

    expect($event->registeredCount())->toBe(2)
        ->and($event->remainingQuota())->toBe(1)
        ->and($event->isSoldOut())->toBeFalse();

    EventRegistration::factory()->create(['event_id' => $event->getKey()]);

    expect($event->refresh()->remainingQuota())->toBe(0)
        ->and($event->isSoldOut())->toBeTrue()
        ->and($event->isRegistrationOpen())->toBeFalse();
});

it('generates readable unique ticket codes and hides the ticket token', function () {
    $first = EventRegistration::factory()->create();
    $second = EventRegistration::factory()->create();

    expect($first->ticket_code)
        ->toMatch('/^EVL-[A-HJ-NP-Z2-9]{4}-[A-HJ-NP-Z2-9]{4}$/')
        ->and($first->ticket_code)->not->toBe($second->ticket_code)
        ->and($first->ticket_token)->toHaveLength(64)
        ->and($first->toArray())->not->toHaveKey('ticket_token');
});

it('checks registrations in once and records who checked them in', function () {
    $admin = User::factory()->create();
    $registration = EventRegistration::factory()->create();

    expect($registration->isCheckedIn())->toBeFalse();

    $attendance = $registration->checkIn($admin);

    expect($attendance)->toBeInstanceOf(EventAttendance::class)
        ->and($attendance->checked_in_by)->toBe($admin->getKey())
        ->and($attendance->checked_in_at)->toBeInstanceOf(Illuminate\Support\Carbon::class)
        ->and($registration->fresh()->isCheckedIn())->toBeTrue()
        ->and($registration->checkIn($admin)->getKey())->toBe($attendance->getKey())
        ->and(EventAttendance::query()->count())->toBe(1);
});

it('exposes status labels and colors for the admin panel', function () {
    expect(EventStatus::Draft->getLabel())->toBe('Draft')
        ->and(EventStatus::Published->getLabel())->toBe('Published')
        ->and(EventStatus::Completed->getLabel())->toBe('Completed')
        ->and(EventStatus::Cancelled->getLabel())->toBe('Cancelled')
        ->and(EventStatus::Published->getColor())->toBe('success')
        ->and(EventStatus::Published->isPubliclyVisible())->toBeTrue()
        ->and(EventStatus::Draft->isPubliclyVisible())->toBeFalse();
});
