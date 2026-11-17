<x-layouts.public title="Events" description="Browse upcoming and past events across Jakarta — technology, music, food, wellness, business, and more.">
    @php
        $baseFilters = array_filter([
            'q' => $search,
            'category' => $category,
        ], fn ($value) => filled($value));

        $timeframes = [
            'upcoming' => 'Upcoming',
            'past' => 'Past',
            'all' => 'All events',
        ];
    @endphp

    <section class="relative overflow-hidden border-b border-slate-900/5 bg-white">
        <div class="pointer-events-none absolute inset-0 hero-atmosphere opacity-70"></div>
        <div class="relative mx-auto max-w-7xl px-4 py-14 sm:px-6 sm:py-18 lg:px-8">
            <div class="max-w-3xl">
                <p class="eyebrow">The calendar</p>
                <h1 class="mt-2 text-balance text-4xl font-semibold tracking-[-0.045em] text-slate-950 sm:text-5xl">
                    Find your next great gathering.
                </h1>
                <p class="mt-4 max-w-xl text-base leading-7 text-slate-600">
                    Talks, workshops, live music, long-table dinners, and slow weekends. Filter by what you love — or simply see what is next.
                </p>
            </div>

            <form action="{{ route('events.index') }}" method="GET" class="mt-8 max-w-4xl">
                <input type="hidden" name="when" value="{{ $when }}">

                <div class="flex flex-col gap-2 rounded-[1.35rem] border border-slate-900/10 bg-white p-2 shadow-[0_20px_60px_-34px_rgb(49_46_129/0.35)] sm:flex-row sm:items-center">
                    <label for="event-search" class="sr-only">Search events</label>
                    <div class="flex flex-1 items-center gap-2 px-3">
                        <svg class="h-5 w-5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"/>
                            <path d="m20 20-3.2-3.2" stroke-linecap="round"/>
                        </svg>
                        <input
                            id="event-search"
                            type="search"
                            name="q"
                            value="{{ $search }}"
                            placeholder="Search by event, venue, or organizer"
                            class="w-full border-0 bg-transparent p-0 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0"
                        >
                    </div>

                    <label for="event-category" class="sr-only">Category</label>
                    <select
                        id="event-category"
                        name="category"
                        class="rounded-xl border-0 bg-slate-50 py-2.5 pl-3 pr-9 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-indigo-500 sm:max-w-[12rem]"
                    >
                        <option value="">All categories</option>
                        @foreach ($categories as $item)
                            <option value="{{ $item->slug }}" @selected($category === $item->slug)>{{ $item->name }}</option>
                        @endforeach
                    </select>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-950 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-950 focus-visible:ring-offset-2"
                    >
                        Search
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"/><path d="m20 20-3.2-3.2" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pt-10 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="inline-flex w-fit items-center gap-1 rounded-full border border-slate-900/5 bg-white p-1 shadow-sm">
                @foreach ($timeframes as $value => $label)
                    <a
                        href="{{ route('events.index', array_merge($baseFilters, ['when' => $value])) }}"
                        @class([
                            'rounded-full px-4 py-2 text-sm font-medium transition',
                            'bg-slate-950 text-white shadow-sm' => $when === $value,
                            'text-slate-600 hover:bg-slate-50 hover:text-slate-950' => $when !== $value,
                        ])
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <p class="text-sm text-slate-500">
                @if ($events->total() > 0)
                    <span class="font-semibold text-slate-900">{{ number_format($events->total()) }}</span>
                    {{ Str::plural('event', $events->total()) }}
                    @if ($search !== '')
                        matching “{{ $search }}”
                    @endif
                @else
                    No events found
                @endif
            </p>
        </div>

        @if ($categories->isNotEmpty())
            <div class="mt-6 flex flex-wrap gap-2">
                <a
                    href="{{ route('events.index', array_filter(['q' => $search, 'when' => $when])) }}"
                    @class([
                        'rounded-full border px-4 py-2 text-sm font-medium transition',
                        'border-indigo-600 bg-indigo-600 text-white' => $category === '',
                        'border-slate-900/10 bg-white text-slate-600 hover:border-indigo-200 hover:text-indigo-700' => $category !== '',
                    ])
                >
                    All
                </a>

                @foreach ($categories as $item)
                    <a
                        href="{{ route('events.index', array_filter(['q' => $search, 'category' => $item->slug, 'when' => $when])) }}"
                        @class([
                            'rounded-full border px-4 py-2 text-sm font-medium transition',
                            'border-indigo-600 bg-indigo-600 text-white' => $category === $item->slug,
                            'border-slate-900/10 bg-white text-slate-600 hover:border-indigo-200 hover:text-indigo-700' => $category !== $item->slug,
                        ])
                    >
                        {{ $item->name }}
                        <span @class(['ml-1 text-xs', 'text-indigo-100' => $category === $item->slug, 'text-slate-400' => $category !== $item->slug])>
                            {{ $item->events_count }}
                        </span>
                    </a>
                @endforeach
            </div>
        @endif

        @if ($events->isNotEmpty())
            <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($events as $event)
                    <div @class(['reveal', 'reveal-delay-1' => $loop->index % 3 === 1, 'reveal-delay-2' => $loop->index % 3 === 2])>
                        <x-event-card :event="$event" />
                    </div>
                @endforeach
            </div>

            @if ($events->hasPages())
                <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-slate-900/5 pt-6 sm:flex-row">
                    <p class="text-sm text-slate-500">
                        Showing <span class="font-medium text-slate-900">{{ $events->firstItem() }}</span>–<span class="font-medium text-slate-900">{{ $events->lastItem() }}</span>
                        of <span class="font-medium text-slate-900">{{ $events->total() }}</span>
                    </p>

                    <div class="flex items-center gap-2">
                        @if ($events->onFirstPage())
                            <span class="inline-flex h-10 items-center rounded-full border border-slate-900/5 bg-white px-4 text-sm font-medium text-slate-300">Previous</span>
                        @else
                            <a href="{{ $events->previousPageUrl() }}" class="inline-flex h-10 items-center rounded-full border border-slate-900/10 bg-white px-4 text-sm font-medium text-slate-700 transition hover:border-indigo-200 hover:text-indigo-700">Previous</a>
                        @endif

                        <span class="inline-flex h-10 items-center rounded-full bg-slate-950 px-4 text-sm font-semibold text-white">
                            {{ $events->currentPage() }} / {{ $events->lastPage() }}
                        </span>

                        @if ($events->hasMorePages())
                            <a href="{{ $events->nextPageUrl() }}" class="inline-flex h-10 items-center rounded-full border border-slate-900/10 bg-white px-4 text-sm font-medium text-slate-700 transition hover:border-indigo-200 hover:text-indigo-700">Next</a>
                        @else
                            <span class="inline-flex h-10 items-center rounded-full border border-slate-900/5 bg-white px-4 text-sm font-medium text-slate-300">Next</span>
                        @endif
                    </div>
                </div>
            @endif
        @else
            <div class="mt-10 rounded-[2rem] border border-dashed border-slate-300 bg-white/70 px-6 py-16 text-center">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                        <rect x="3.5" y="5" width="17" height="15.5" rx="3"/>
                        <path d="M8 3.5v3M16 3.5v3M3.5 9.5h17M9 14h6" stroke-linecap="round"/>
                    </svg>
                </span>
                <h2 class="mt-5 text-2xl font-semibold tracking-tight text-slate-950">Nothing on the calendar — yet.</h2>
                <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-slate-500">
                    Try a different search, browse another category, or look at past events for a taste of what Evently hosts.
                </p>
                <a href="{{ route('events.index') }}" class="mt-6 inline-flex items-center gap-2 rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Clear filters
                </a>
            </div>
        @endif
    </section>
</x-layouts.public>
