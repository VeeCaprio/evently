<?php

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventRegistration;

it('shows featured and upcoming published events on the home page', function () {
    $category = EventCategory::factory()->create(['name' => 'Technology']);

    $featured = Event::factory()->featured()->create([
        'event_category_id' => $category->getKey(),
        'title' => 'Future Stack Summit',
        'start_at' => now()->addDays(10),
        'end_at' => now()->addDays(10)->addHours(6),
    ]);

    $upcoming = Event::factory()->create([
        'title' => 'Slow Sunday Market',
        'start_at' => now()->addDays(20),
        'end_at' => now()->addDays(20)->addHours(5),
    ]);

    Event::factory()->draft()->create(['title' => 'Secret Draft Event']);
    Event::factory()->create([
        'title' => 'Long Gone Gathering',
        'status' => EventStatus::Completed,
        'start_at' => now()->subMonth(),
        'end_at' => now()->subMonth()->addHours(4),
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSeeText($featured->title)
        ->assertSeeText($upcoming->title)
        ->assertSeeText('Technology')
        ->assertDontSeeText('Secret Draft Event')
        ->assertDontSeeText('Long Gone Gathering');
});

it('lists public events and supports search and category filters', function () {
    $technology = EventCategory::factory()->create(['name' => 'Technology', 'slug' => 'technology']);
    $music = EventCategory::factory()->create(['name' => 'Music', 'slug' => 'music']);

    $techEvent = Event::factory()->create([
        'event_category_id' => $technology->getKey(),
        'title' => 'AI Builders Meetup',
        'location' => 'Sudirman Hub',
    ]);

    $musicEvent = Event::factory()->create([
        'event_category_id' => $music->getKey(),
        'title' => 'Rooftop Strings',
        'location' => 'Kemang Rooftop',
    ]);

    Event::factory()->draft()->create(['title' => 'Backstage Draft']);

    $this->get(route('events.index'))
        ->assertOk()
        ->assertSeeText($techEvent->title)
        ->assertSeeText($musicEvent->title)
        ->assertDontSeeText('Backstage Draft');

    $this->get(route('events.index', ['q' => 'AI Builders']))
        ->assertOk()
        ->assertSeeText($techEvent->title)
        ->assertDontSeeText($musicEvent->title);

    $this->get(route('events.index', ['category' => 'music']))
        ->assertOk()
        ->assertSeeText($musicEvent->title)
        ->assertDontSeeText($techEvent->title);

    $this->get(route('events.index', ['q' => 'nothing-matches-this']))
        ->assertOk()
        ->assertSeeText('Nothing on the calendar');
});

it('shows published events and hides non-public statuses', function () {
    $published = Event::factory()->create(['title' => 'Open Studio Night']);
    $completed = Event::factory()->completed()->create(['title' => 'Archive Session']);

    $draft = Event::factory()->draft()->create(['slug' => 'draft-event']);
    $cancelled = Event::factory()->create([
        'slug' => 'cancelled-event',
        'status' => EventStatus::Cancelled,
    ]);

    $this->get(route('events.show', ['slug' => $published->slug]))
        ->assertOk()
        ->assertSeeText($published->title);

    $this->get(route('events.show', ['slug' => $completed->slug]))
        ->assertOk()
        ->assertSeeText($completed->title)
        ->assertSeeText('Past event');

    $this->get(route('events.show', ['slug' => $draft->slug]))->assertNotFound();
    $this->get(route('events.show', ['slug' => $cancelled->slug]))->assertNotFound();
});

it('marks sold-out events and prevents registration', function () {
    $event = Event::factory()->soldOut()->create(['title' => 'Full House Forum']);

    $this->get(route('events.show', ['slug' => $event->slug]))
        ->assertOk()
        ->assertSeeText('Sold out');

    $this->get(route('events.register', ['slug' => $event->slug]))
        ->assertRedirect(route('events.show', ['slug' => $event->slug]))
        ->assertSessionHas('error');

    expect(EventRegistration::query()->count())->toBe(0);
});

it('registers a guest and shows their ticket', function () {
    $event = Event::factory()->create([
        'title' => 'Hands-on Laravel Lab',
        'quota' => 20,
    ]);

    $response = $this->post(route('events.register.store', ['slug' => $event->slug]), [
        'name' => '  Maya Putri  ',
        'email' => '  MAYA@example.com ',
        'phone' => '0812 3456 7890',
    ]);

    $registration = EventRegistration::query()->sole();

    expect($registration->name)->toBe('Maya Putri')
        ->and($registration->email)->toBe('maya@example.com')
        ->and($registration->phone)->toBe('0812 3456 7890')
        ->and($registration->event_id)->toBe($event->getKey())
        ->and($registration->ticket_code)->toStartWith('EVL-');

    $response->assertRedirect(route('tickets.show', ['ticketCode' => $registration->ticket_code]));

    $this->get(route('tickets.show', ['ticketCode' => strtolower($registration->ticket_code)]))
        ->assertOk()
        ->assertSeeText('Maya Putri')
        ->assertSeeText($registration->ticket_code)
        ->assertSeeText($event->title)
        ->assertSeeText('Ready for entry');
});

it('rejects duplicate registrations for the same event only', function () {
    $event = Event::factory()->create(['quota' => 50]);
    $otherEvent = Event::factory()->create(['quota' => 50]);

    EventRegistration::factory()->create([
        'event_id' => $event->getKey(),
        'email' => 'guest@example.com',
    ]);

    $this->post(route('events.register.store', ['slug' => $event->slug]), [
        'name' => 'Guest Again',
        'email' => 'guest@example.com',
        'phone' => '081234567890',
    ])->assertSessionHasErrors('email');

    expect(EventRegistration::query()->where('event_id', $event->getKey())->count())->toBe(1);

    $this->post(route('events.register.store', ['slug' => $otherEvent->slug]), [
        'name' => 'Guest Again',
        'email' => 'guest@example.com',
        'phone' => '081234567890',
    ])->assertRedirect();

    expect(EventRegistration::query()->where('email', 'guest@example.com')->count())->toBe(2);
});

it('validates registration details', function () {
    $event = Event::factory()->create();

    $this->post(route('events.register.store', ['slug' => $event->slug]), [
        'name' => '',
        'email' => 'not-an-email',
        'phone' => 'abc',
    ])->assertSessionHasErrors(['name', 'email', 'phone']);

    expect(EventRegistration::query()->count())->toBe(0);
});

it('does not register guests when the quota is already full', function () {
    $event = Event::factory()->create(['quota' => 2]);

    EventRegistration::factory()->count(2)->create(['event_id' => $event->getKey()]);

    $this->get(route('events.register', ['slug' => $event->slug]))
        ->assertRedirect(route('events.show', ['slug' => $event->slug]))
        ->assertSessionHas('error');

    $this->post(route('events.register.store', ['slug' => $event->slug]), [
        'name' => 'Too Late',
        'email' => 'late@example.com',
        'phone' => '081234567890',
    ])->assertRedirect(route('events.show', ['slug' => $event->slug]))
        ->assertSessionHas('error');

    expect(EventRegistration::query()->where('event_id', $event->getKey())->count())->toBe(2);
});

it('does not register guests for events that have ended', function () {
    $event = Event::factory()->completed()->create(['quota' => 50]);

    $this->get(route('events.register', ['slug' => $event->slug]))
        ->assertRedirect(route('events.show', ['slug' => $event->slug]))
        ->assertSessionHas('error');

    $this->post(route('events.register.store', ['slug' => $event->slug]), [
        'name' => 'Late Guest',
        'email' => 'late@example.com',
        'phone' => '081234567890',
    ])->assertRedirect(route('events.show', ['slug' => $event->slug]))
        ->assertSessionHas('error');

    expect(EventRegistration::query()->count())->toBe(0);
});

it('returns not found for an unknown ticket code', function () {
    $this->get(route('tickets.show', ['ticketCode' => 'EVL-NONE-0000']))->assertNotFound();
});
