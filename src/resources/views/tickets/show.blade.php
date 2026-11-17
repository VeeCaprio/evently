<x-layouts.public
    :title="'Ticket · ' . $registration->event->title"
    description="Your Evently ticket and entry code."
    :noindex="true"
>
    @php
        $event = $registration->event;
        $checkedIn = $registration->attendance !== null;
        $justRegistered = session('registered', false);
    @endphp

    <section class="mx-auto max-w-5xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
        <div class="text-center">
            <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 ring-8 ring-emerald-50">
                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="m7.5 12.5 3 3 6-6.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>

            <p class="mt-5 text-[0.7rem] font-semibold uppercase tracking-[0.2em] text-emerald-600">
                {{ $justRegistered ? 'Registration confirmed' : 'Your ticket' }}
            </p>
            <h1 class="mt-2 text-balance text-4xl font-semibold tracking-[-0.045em] text-slate-950 sm:text-5xl">
                {{ $justRegistered ? 'You are in, ' . Str::before($registration->name, ' ') . '.' : 'Everything you need for the door.' }}
            </h1>
            <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-slate-600">
                Your seat at <span class="font-medium text-slate-900">{{ $event->title }}</span> is reserved. Keep this code handy — it is all you need to check in.
            </p>
        </div>

        <div class="mt-10 grid gap-6 lg:grid-cols-[1.25fr_0.75fr] lg:items-start">
            <div class="print-ticket overflow-hidden rounded-[2rem] border border-slate-900/5 bg-white shadow-[0_30px_80px_-45px_rgb(15_23_42/0.4)]">
                <div class="flex items-center justify-between gap-3 border-b border-slate-900/5 bg-slate-50/80 px-6 py-4">
                    <img src="{{ asset('images/evently-logo.svg') }}" alt="Evently" class="h-7 w-auto" width="156" height="36">
                    <span @class([
                        'rounded-full px-3 py-1.5 text-[0.68rem] font-semibold uppercase tracking-[0.12em]',
                        'bg-emerald-100 text-emerald-700' => $checkedIn,
                        'bg-indigo-100 text-indigo-700' => ! $checkedIn,
                    ])>
                        {{ $checkedIn ? 'Checked in' : 'Ready for entry' }}
                    </span>
                </div>

                <div class="p-6 sm:p-8">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">{{ $event->category?->name ?? 'Evently event' }}</p>
                    <h2 class="mt-2 text-2xl font-semibold tracking-[-0.03em] text-slate-950">{{ $event->title }}</h2>

                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div class="flex items-start gap-2.5 text-sm text-slate-600">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <rect x="3.5" y="5" width="17" height="15.5" rx="3"/>
                                <path d="M8 3.5v3M16 3.5v3M3.5 9.5h17" stroke-linecap="round"/>
                            </svg>
                            <span>
                                <span class="block font-medium text-slate-900">{{ $event->start_at->format('l, d F Y') }}</span>
                                {{ $event->start_at->format('H:i') }} – {{ $event->end_at->format('H:i') }}
                            </span>
                        </div>

                        <div class="flex items-start gap-2.5 text-sm text-slate-600">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <path d="M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11Z" stroke-linejoin="round"/>
                                <circle cx="12" cy="10" r="2.4"/>
                            </svg>
                            <span>{{ $event->location }}</span>
                        </div>
                    </div>

                    <div class="my-7 border-t border-dashed border-slate-300"></div>

                    <div class="rounded-2xl bg-gradient-to-br from-indigo-600 via-indigo-600 to-violet-600 px-6 py-6 text-center text-white shadow-lg shadow-indigo-600/20">
                        <p class="text-[0.65rem] font-semibold uppercase tracking-[0.22em] text-indigo-100">Ticket code</p>
                        <p class="ticket-code mt-2 text-2xl font-semibold sm:text-3xl">{{ $registration->ticket_code }}</p>
                        <p class="mt-2 text-xs text-indigo-100">Show this code at the door.</p>
                    </div>

                    <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="text-[0.65rem] font-semibold uppercase tracking-[0.14em] text-slate-400">Attendee</dt>
                            <dd class="mt-1 font-medium text-slate-900">{{ $registration->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-[0.65rem] font-semibold uppercase tracking-[0.14em] text-slate-400">Email</dt>
                            <dd class="mt-1 break-all text-slate-700">{{ $registration->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-[0.65rem] font-semibold uppercase tracking-[0.14em] text-slate-400">Phone</dt>
                            <dd class="mt-1 text-slate-700">{{ $registration->phone }}</dd>
                        </div>
                        <div>
                            <dt class="text-[0.65rem] font-semibold uppercase tracking-[0.14em] text-slate-400">Registered</dt>
                            <dd class="mt-1 text-slate-700">{{ $registration->created_at->format('d M Y · H:i') }}</dd>
                        </div>
                    </dl>

                    @if ($checkedIn)
                        <div class="mt-6 flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-3 text-xs font-medium text-emerald-800">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <circle cx="12" cy="12" r="9"/><path d="m8.5 12.5 2.4 2.4 4.6-5.4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Checked in on {{ $registration->attendance->checked_in_at->format('d M Y · H:i') }}
                        </div>
                    @endif
                </div>
            </div>

            <aside class="space-y-4">
                <div class="rounded-[1.75rem] border border-slate-900/5 bg-white p-6 shadow-[0_1px_2px_rgb(15_23_42/0.03)]">
                    <h2 class="text-sm font-semibold text-slate-950">Before you go</h2>
                    <ul class="mt-4 space-y-4 text-sm text-slate-600">
                        <li class="flex gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-[0.65rem] font-semibold text-indigo-700">1</span>
                            Save this page or print your ticket — the code is your entry.
                        </li>
                        <li class="flex gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-[0.65rem] font-semibold text-indigo-700">2</span>
                            Arrive a little early. Doors open at {{ $event->start_at->format('H:i') }}.
                        </li>
                        <li class="flex gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-[0.65rem] font-semibold text-indigo-700">3</span>
                            Show the code at the door and you are in.
                        </li>
                    </ul>
                </div>

                <div class="rounded-[1.75rem] border border-slate-900/5 bg-[#0b1026] p-6 text-white">
                    <p class="text-[0.65rem] font-semibold uppercase tracking-[0.18em] text-indigo-300">Need another look?</p>
                    <p class="mt-2 text-sm leading-6 text-slate-400">
                        The event page has the full description, venue details, and everything else you may need.
                    </p>
                    <a href="{{ route('events.show', ['slug' => $event->slug]) }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-white transition hover:text-indigo-200">
                        View event page
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            </aside>
        </div>

        <div class="no-print mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
            <button
                type="button"
                onclick="window.print()"
                class="inline-flex w-full items-center justify-center gap-2 rounded-full border border-slate-900/10 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-indigo-200 hover:text-indigo-700 sm:w-auto"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                    <path d="M7 9V4h10v5M7 17H5.5A1.5 1.5 0 0 1 4 15.5v-5A1.5 1.5 0 0 1 5.5 9h13a1.5 1.5 0 0 1 1.5 1.5v5a1.5 1.5 0 0 1-1.5 1.5H17" stroke-linejoin="round"/>
                    <rect x="7" y="14" width="10" height="6.5" rx="1"/>
                </svg>
                Print ticket
            </button>

            <a href="{{ route('events.index') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 sm:w-auto">
                Explore more events
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>
    </section>
</x-layouts.public>
