<x-layouts.public
    :title="$event->title"
    :description="Str::limit(strip_tags($event->description), 155)"
>
    @push('head')
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'Event',
                'name' => $event->title,
                'description' => Str::limit(strip_tags($event->description), 300),
                'startDate' => $event->start_at->toIso8601String(),
                'endDate' => $event->end_at->toIso8601String(),
                'eventStatus' => $event->status === \App\Enums\EventStatus::Cancelled
                    ? 'https://schema.org/EventCancelled'
                    : 'https://schema.org/EventScheduled',
                'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
                'image' => $event->banner_url ? url($event->banner_url) : url('images/evently-banner-placeholder.svg'),
                'isAccessibleForFree' => (float) $event->ticket_price === 0.0,
                'location' => [
                    '@type' => 'Place',
                    'name' => $event->location,
                    'address' => $event->location,
                ],
                'organizer' => [
                    '@type' => 'Organization',
                    'name' => $event->organizer ?: 'Evently',
                ],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
        </script>
    @endpush

    @php
        $registered = $event->registeredCount();
        $remaining = $event->remainingQuota();
        $percentFull = min(100, (int) round(($registered / max($event->quota, 1)) * 100));
        $price = (float) $event->ticket_price;
        $registrationOpen = $event->isRegistrationOpen();
        $ended = $event->hasEnded() || $event->status === \App\Enums\EventStatus::Completed;
        $ongoing = $event->isOngoing();
    @endphp

    <div class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-sm text-slate-500" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="transition hover:text-slate-900">Home</a>
            <svg class="h-3.5 w-3.5 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="m9 6 6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <a href="{{ route('events.index') }}" class="transition hover:text-slate-900">Events</a>
            <svg class="h-3.5 w-3.5 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="m9 6 6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="line-clamp-1 text-slate-700">{{ $event->title }}</span>
        </nav>
    </div>

    <section class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="max-w-3xl">
                <div class="flex flex-wrap items-center gap-2">
                    @if ($event->category)
                        <a
                            href="{{ route('events.index', ['category' => $event->category->slug]) }}"
                            class="rounded-full bg-indigo-50 px-3.5 py-1.5 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100"
                        >
                            {{ $event->category->name }}
                        </a>
                    @endif

                    @if ($event->is_featured)
                        <span class="rounded-full bg-amber-50 px-3.5 py-1.5 text-xs font-semibold text-amber-700">Featured</span>
                    @endif

                    @if ($ended)
                        <span class="rounded-full bg-slate-100 px-3.5 py-1.5 text-xs font-semibold text-slate-600">Past event</span>
                    @elseif ($event->isSoldOut())
                        <span class="rounded-full bg-rose-50 px-3.5 py-1.5 text-xs font-semibold text-rose-700">Sold out</span>
                    @elseif ($ongoing)
                        <span class="rounded-full bg-emerald-50 px-3.5 py-1.5 text-xs font-semibold text-emerald-700">Happening now</span>
                    @endif
                </div>

                <h1 class="mt-4 text-balance text-4xl font-semibold leading-[1.08] tracking-[-0.045em] text-slate-950 sm:text-5xl">
                    {{ $event->title }}
                </h1>

                <div class="mt-5 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-slate-600">
                    <span class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                            <rect x="3.5" y="5" width="17" height="15.5" rx="3"/>
                            <path d="M8 3.5v3M16 3.5v3M3.5 9.5h17" stroke-linecap="round"/>
                        </svg>
                        <time datetime="{{ $event->start_at->toIso8601String() }}">
                            {{ $event->start_at->format('l, d F Y · H:i') }}
                        </time>
                    </span>
                    <span class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                            <path d="M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11Z" stroke-linejoin="round"/>
                            <circle cx="12" cy="10" r="2.4"/>
                        </svg>
                        {{ $event->location }}
                    </span>
                    @if ($event->organizer)
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <path d="M4 20V8.5L12 4l8 4.5V20M9 20v-6h6v6" stroke-linejoin="round"/>
                            </svg>
                            {{ $event->organizer }}
                        </span>
                    @endif
                </div>
            </div>

            <button
                type="button"
                data-copy-link
                data-copy-label="Copy link"
                class="inline-flex w-fit items-center gap-2 rounded-full border border-slate-900/10 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 shadow-sm transition hover:border-indigo-200 hover:text-indigo-700"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                    <path d="M9.5 14.5 14.5 9.5M8 17H7a4 4 0 0 1 0-8h2M16 7h1a4 4 0 0 1 0 8h-2" stroke-linecap="round"/>
                </svg>
                <span>Copy link</span>
            </button>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_23rem] lg:items-start lg:gap-10">
            <div class="min-w-0">
                <div class="relative overflow-hidden rounded-[2rem] border border-slate-900/5 bg-slate-100 shadow-[0_30px_80px_-50px_rgb(15_23_42/0.45)]">
                    <div class="aspect-[16/9]">
                        @if ($event->banner_url)
                            <img src="{{ $event->banner_url }}" alt="{{ $event->title }}" class="h-full w-full object-cover">
                        @else
                            <img src="{{ asset('images/evently-banner-placeholder.svg') }}" alt="" class="h-full w-full object-cover">
                        @endif
                    </div>
                </div>

                <div class="mt-10 rounded-[2rem] border border-slate-900/5 bg-white p-6 shadow-[0_1px_2px_rgb(15_23_42/0.03)] sm:p-9">
                    <p class="eyebrow">About this event</p>
                    <div class="prose-event mt-4">
                        {!! nl2br(e($event->description)) !!}
                    </div>

                    <div class="mt-9 grid gap-4 border-t border-slate-900/5 pt-8 sm:grid-cols-3">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-indigo-600 shadow-sm">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="M4 8.5A2.5 2.5 0 0 1 6.5 6h11A2.5 2.5 0 0 1 20 8.5v1a3 3 0 0 0 0 5v1a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 4 15.5v-1a3 3 0 0 0 0-5v-1Z" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <h2 class="mt-3 text-sm font-semibold text-slate-950">One ticket tier</h2>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                {{ $price > 0 ? 'Rp ' . number_format($price, 0, ',', '.') : 'Free entry' }} — pricing is informational. Evently V1 does not process payments.
                            </p>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-indigo-600 shadow-sm">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="M7 3.5h7.5L19 8v12.5H7z" stroke-linejoin="round"/>
                                    <path d="M14 3.5V8h5M10 12h6M10 15.5h6" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <h2 class="mt-3 text-sm font-semibold text-slate-950">Instant ticket code</h2>
                            <p class="mt-1 text-xs leading-5 text-slate-500">Your code appears right after registering. Save or print the ticket page.</p>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-indigo-600 shadow-sm">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <h2 class="mt-3 text-sm font-semibold text-slate-950">Plan your arrival</h2>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Doors open at {{ $event->start_at->format('H:i') }}. Give yourself a little room to settle in.
                            </p>
                        </div>
                    </div>
                </div>

                @if ($related->isNotEmpty())
                    <div class="mt-12">
                        <div class="flex items-end justify-between gap-4">
                            <div>
                                <p class="eyebrow">Keep the momentum</p>
                                <h2 class="mt-2 text-2xl font-semibold tracking-[-0.03em] text-slate-950">You might also like</h2>
                            </div>
                            <a href="{{ route('events.index') }}" class="hidden text-sm font-semibold text-indigo-600 transition hover:text-indigo-700 sm:inline-flex">
                                All events
                            </a>
                        </div>

                        <div class="mt-6 grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach ($related as $relatedEvent)
                                <x-event-card :event="$relatedEvent" />
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <aside class="lg:sticky lg:top-24">
                <div class="overflow-hidden rounded-[2rem] border border-slate-900/5 bg-white shadow-[0_24px_70px_-40px_rgb(15_23_42/0.35)]">
                    <div class="p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Tickets</p>
                        <div class="mt-2 flex items-end justify-between gap-3">
                            <p class="text-3xl font-semibold tracking-[-0.04em] text-slate-950">
                                {{ $price > 0 ? 'Rp ' . number_format($price, 0, ',', '.') : 'Free' }}
                            </p>
                            @if ($price > 0)
                                <span class="pb-1 text-xs text-slate-400">per person</span>
                            @endif
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Informational price — no payment required.</p>

                        <div class="mt-6">
                            <div class="flex items-center justify-between text-xs font-medium">
                                <span class="text-slate-500">Seats reserved</span>
                                <span class="text-slate-900">{{ $registered }} / {{ $event->quota }}</span>
                            </div>
                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                                <div
                                    @class([
                                        'h-full rounded-full bg-gradient-to-r',
                                        'from-rose-500 to-rose-400' => $event->isSoldOut(),
                                        'from-amber-500 to-amber-400' => ! $event->isSoldOut() && $remaining <= 15,
                                        'from-indigo-500 to-violet-500' => ! $event->isSoldOut() && $remaining > 15,
                                    ])
                                    style="width: {{ $percentFull }}%"
                                ></div>
                            </div>
                            <p class="mt-2 text-xs text-slate-500">
                                @if ($event->isSoldOut())
                                    Every seat is taken.
                                @else
                                    {{ $remaining }} {{ Str::plural('seat', $remaining) }} still available.
                                @endif
                            </p>
                        </div>

                        <div class="mt-6">
                            @if ($registrationOpen)
                                <a
                                    href="{{ route('events.register', ['slug' => $event->slug]) }}"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-5 py-3.5 text-sm font-semibold text-white shadow-sm shadow-indigo-600/25 transition hover:-translate-y-0.5 hover:bg-indigo-500 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                                >
                                    Reserve a seat
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                                <p class="mt-3 text-center text-xs text-slate-500">No account needed · Ticket code issued instantly</p>
                            @else
                                <div class="rounded-2xl bg-slate-100 px-5 py-4 text-center">
                                    <p class="text-sm font-semibold text-slate-700">
                                        {{ $ended ? 'This event has ended' : ($event->isSoldOut() ? 'Sold out' : ($ongoing ? 'Happening now' : 'Registration closed')) }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $ended ? 'Browse the calendar for what is next.' : 'Explore similar events on the calendar.' }}
                                    </p>
                                </div>
                                <a href="{{ route('events.index') }}" class="mt-3 inline-flex w-full items-center justify-center rounded-2xl border border-slate-900/10 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-indigo-200 hover:text-indigo-700">
                                    Browse events
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-4 border-t border-slate-900/5 bg-slate-50/80 p-6">
                        <div class="flex gap-3">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <rect x="3.5" y="5" width="17" height="15.5" rx="3"/>
                                <path d="M8 3.5v3M16 3.5v3M3.5 9.5h17" stroke-linecap="round"/>
                            </svg>
                            <div>
                                <p class="text-xs font-semibold text-slate-900">{{ $event->start_at->format('D, d M Y') }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">
                                    {{ $event->start_at->format('H:i') }} – {{ $event->end_at->format('H:i') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <path d="M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11Z" stroke-linejoin="round"/>
                                <circle cx="12" cy="10" r="2.4"/>
                            </svg>
                            <p class="text-xs leading-5 text-slate-600">{{ $event->location }}</p>
                        </div>

                        @if ($event->organizer)
                            <div class="flex gap-3">
                                <svg class="mt-0.5 h-5 w-5 shrink-0 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                    <path d="M4 20V8.5L12 4l8 4.5V20M9 20v-6h6v6" stroke-linejoin="round"/>
                                </svg>
                                <p class="text-xs leading-5 text-slate-600">Hosted by <span class="font-medium text-slate-900">{{ $event->organizer }}</span></p>
                            </div>
                        @endif
                    </div>
                </div>
            </aside>
        </div>
    </section>
</x-layouts.public>
