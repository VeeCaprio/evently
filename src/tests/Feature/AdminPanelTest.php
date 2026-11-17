<?php

use App\Filament\Admin\Resources\EventAttendanceResource;
use App\Filament\Admin\Resources\EventCategoryResource;
use App\Filament\Admin\Resources\EventRegistrationResource;
use App\Filament\Admin\Resources\EventRegistrationResource\Pages\ListEventRegistrations;
use App\Filament\Admin\Resources\EventResource;
use App\Filament\Admin\Widgets\EventStatsOverview;
use App\Filament\Admin\Widgets\LatestRegistrationsTable;
use App\Filament\Admin\Widgets\UpcomingEventsTable;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\EventCategory;
use App\Models\EventRegistration;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));

    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('super_admin');

    $this->category = EventCategory::factory()->create();
    $this->event = Event::factory()->create([
        'event_category_id' => $this->category->getKey(),
        'title' => 'Evently Admin Demo',
    ]);
    $this->registration = EventRegistration::factory()->create([
        'event_id' => $this->event->getKey(),
        'name' => 'Admin Demo Guest',
    ]);
});

it('shows the branded login page to guests', function () {
    $this->get('/admin/login')
        ->assertOk()
        ->assertSee('Evently')
        ->assertSee('Events worth showing up for.');
});

it('renders the overview dashboard for super admins', function () {
    $this->actingAs($this->admin)
        ->get('/admin')
        ->assertOk()
        ->assertSee('Overview')
        ->assertSee('Welcome back, ' . $this->admin->name);
});

it('renders every event management page', function () {
    $this->actingAs($this->admin);

    $this->get(EventResource::getUrl('index'))->assertOk();
    $this->get(EventResource::getUrl('create'))->assertOk();
    $this->get(EventResource::getUrl('edit', ['record' => $this->event]))->assertOk();
    $this->get(EventCategoryResource::getUrl('index'))->assertOk();
    $this->get(EventCategoryResource::getUrl('create'))->assertOk();
    $this->get(EventCategoryResource::getUrl('edit', ['record' => $this->category]))->assertOk();
});

it('renders registration and check-in pages', function () {
    $attendance = EventAttendance::factory()->create([
        'event_registration_id' => $this->registration->getKey(),
        'checked_in_by' => $this->admin->getKey(),
    ]);

    $this->actingAs($this->admin);

    $this->get(EventRegistrationResource::getUrl('index'))->assertOk();
    $this->get(EventRegistrationResource::getUrl('create'))->assertOk();
    $this->get(EventRegistrationResource::getUrl('view', ['record' => $this->registration]))->assertOk();
    $this->get(EventRegistrationResource::getUrl('edit', ['record' => $this->registration]))->assertOk();
    $this->get(EventAttendanceResource::getUrl('index'))->assertOk();
    $this->get(EventAttendanceResource::getUrl('view', ['record' => $attendance]))->assertOk();
});

it('renders dashboard widgets with live data', function () {
    Livewire::actingAs($this->admin)
        ->test(EventStatsOverview::class)
        ->assertSuccessful()
        ->assertSee('Published events')
        ->assertSee('Seats remaining');

    Livewire::actingAs($this->admin)
        ->test(UpcomingEventsTable::class)
        ->assertSuccessful()
        ->assertSee($this->event->title);

    Livewire::actingAs($this->admin)
        ->test(LatestRegistrationsTable::class)
        ->assertSuccessful()
        ->assertSee($this->registration->name);
});

it('checks a guest in by ticket code from the registrations page', function () {
    Livewire::actingAs($this->admin)
        ->test(ListEventRegistrations::class)
        ->callAction('checkInByCode', data: [
            'ticket_code' => strtolower($this->registration->ticket_code),
        ])
        ->assertHasNoActionErrors();

    expect($this->registration->fresh()->attendance)->not->toBeNull()
        ->and($this->registration->fresh()->attendance->checked_in_by)->toBe($this->admin->getKey());
});

it('checks a guest in with the row action', function () {
    Livewire::actingAs($this->admin)
        ->test(ListEventRegistrations::class)
        ->callTableAction('checkIn', $this->registration)
        ->assertHasNoTableActionErrors();

    expect($this->registration->fresh()->isCheckedIn())->toBeTrue();
});

it('blocks panel admins without resource permissions', function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

    $limitedAdmin = User::factory()->create();
    $limitedAdmin->assignRole('admin');

    $this->actingAs($limitedAdmin)
        ->get(EventResource::getUrl('index'))
        ->assertForbidden();
});
