<?php

namespace App\Http\Controllers;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RegistrationController extends Controller
{
    public function create(string $slug): View|RedirectResponse
    {
        $event = $this->findPublicEvent($slug);

        if (! $event->isRegistrationOpen()) {
            return $this->closedRedirect($event);
        }

        return view('events.register', compact('event'));
    }

    public function store(Request $request, string $slug): RedirectResponse
    {
        $event = $this->findPublicEvent($slug);

        if (! $event->isRegistrationOpen()) {
            return $this->closedRedirect($event);
        }

        $request->merge([
            'name' => trim((string) $request->input('name')),
            'email' => Str::lower(trim((string) $request->input('email'))),
            'phone' => trim((string) $request->input('phone')),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required',
                'string',
                'email',
                'max:180',
                Rule::unique('event_registrations', 'email')
                    ->where(fn ($query) => $query->where('event_id', $event->getKey())),
            ],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+()\s.-]{7,30}$/'],
        ], [
            'email.unique' => 'This email is already registered for this event.',
            'phone.regex' => 'Enter a valid phone number (at least 7 digits).',
        ]);

        $registration = DB::transaction(function () use ($event, $data): EventRegistration {
            $lockedEvent = Event::query()
                ->whereKey($event->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedEvent->isRegistrationOpen()) {
                throw ValidationException::withMessages([
                    'event' => $lockedEvent->isSoldOut()
                        ? 'This event just sold out. Please choose another event.'
                        : 'Registration for this event has closed.',
                ]);
            }

            if ($lockedEvent->registrations()->where('email', $data['email'])->exists()) {
                throw ValidationException::withMessages([
                    'email' => 'This email is already registered for this event.',
                ]);
            }

            return $lockedEvent->registrations()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ]);
        });

        return redirect()
            ->route('tickets.show', ['ticketCode' => $registration->ticket_code])
            ->with('registered', true);
    }

    private function findPublicEvent(string $slug): Event
    {
        return Event::query()
            ->publiclyVisible()
            ->with('category')
            ->withCount('registrations')
            ->where('slug', $slug)
            ->firstOrFail();
    }

    private function closedRedirect(Event $event): RedirectResponse
    {
        $message = match (true) {
            $event->status === EventStatus::Completed => 'This event has already taken place.',
            $event->hasEnded() => 'This event has ended and registration is closed.',
            $event->isSoldOut() => 'This event is sold out. Follow Evently for new dates and similar events.',
            default => 'Registration is not open for this event.',
        };

        return redirect()
            ->route('events.show', ['slug' => $event->slug])
            ->with('error', $message);
    }
}
