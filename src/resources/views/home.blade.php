<x-layouts.public description="Discover curated talks, workshops, music, food, and wellness events across Jakarta. Reserve your spot in under a minute — no account needed.">
    <section class="hero-atmosphere relative overflow-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-[38rem] hero-grain opacity-60"></div>
        <div class="pointer-events-none absolute -left-40 top-40 h-80 w-80 rounded-full bg-violet-300/30 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-24 top-10 h-96 w-96 rounded-full bg-indigo-300/40 blur-3xl"></div>

        <div class="relative mx-auto max-w-7xl px-4 pb-20 pt-14 sm:px-6 sm:pt-20 lg:px-8 lg:pb-28">
            <div class="grid items-center gap-14 xl:grid-cols-[1.02fr_0.98fr] xl:gap-16">
                <div class="reveal">
                    <span class="inline-flex items-center gap-2 rounded-full border border-indigo-200/80 bg-white/70 px-4 py-2 text-xs font-semibold text-indigo-700 shadow-sm backdrop-blur">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-indigo-400 opacity-70"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-indigo-600"></span>
                        </span>
                        Curated events across Jakarta
                    </span>

                    <h1 class="mt-6 max-w-2xl text-balance text-5xl font-semibold leading-[1.02] tracking-[-0.055em] text-slate-950 sm:text-6xl xl:text-[4.35rem]">
                        Find your next
                        <span class="bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-500 bg-clip-text text-transparent">great gathering.</span>
                    </h1>

                    <p class="mt-6 max-w-xl text-lg leading-8 text-slate-600">
                        Talks, workshops, live music, and good food — chosen for the curious. Save your spot in under a minute, no account required.
                    </p>

                    <form action="{{ route('events.index') }}" method="GET" class="mt-8 max-w-2xl">
                        <div class="flex flex-col gap-2 rounded-[1.35rem] border border-slate-900/10 bg-white p-2 shadow-[0_20px_60px_-30px_rgb(49_46_129/0.35)] sm:flex-row sm:items-center">
                            <label for="home-search" class="sr-only">Search events</label>
                            <div class="flex flex-1 items-center gap-2 px-3">
                                <svg class="h-5 w-5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <circle cx="11" cy="11" r="7"/>
                                    <path d="m20 20-3.2-3.2" stroke-linecap="round"/>
                                </svg>
                                <input
                                    id="home-search"
                                    type="search"
                                    name="q"
                                    placeholder="Search events or venues"
                                    class="w-full border-0 bg-transparent p-0 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-0"
                                >
                            </div>

                            <label for="home-category" class="sr-only">Category</label>
                            <select
                                id="home-category"
                                name="category"
                                class="rounded-xl border-0 bg-slate-50 py-2.5 pl-3 pr-9 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-indigo-500 sm:max-w-[11rem]"
                            >
                                <option value="">All categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->slug }}">{{ $category->name }}</option>
                                @endforeach
                            </select>

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-950 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-950 focus-visible:ring-offset-2"
                            >
                                Explore
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </form>

                    <dl class="mt-10 grid grid-cols-3 gap-4 sm:flex sm:flex-wrap sm:items-center sm:gap-x-9 sm:gap-y-5">
                        <div class="min-w-0">
                            <dt class="text-[0.6rem] font-medium uppercase tracking-[0.14em] text-slate-500 sm:text-xs sm:tracking-[0.16em]">Upcoming</dt>
                            <dd class="mt-1 text-base font-semibold tracking-tight text-slate-950 sm:text-2xl">{{ number_format($stats['upcoming']) }} events</dd>
                        </div>
                        <div class="hidden h-10 w-px bg-slate-900/10 sm:block"></div>
                        <div class="min-w-0">
                            <dt class="text-[0.6rem] font-medium uppercase tracking-[0.14em] text-slate-500 sm:text-xs sm:tracking-[0.16em]">Reserved</dt>
                            <dd class="mt-1 text-base font-semibold tracking-tight text-slate-950 sm:text-2xl">{{ number_format($stats['registrations']) }} seats</dd>
                        </div>
                        <div class="hidden h-10 w-px bg-slate-900/10 sm:block"></div>
                        <div class="min-w-0">
                            <dt class="text-[0.6rem] font-medium uppercase tracking-[0.14em] text-slate-500 sm:text-xs sm:tracking-[0.16em]">Categories</dt>
                            <dd class="mt-1 text-base font-semibold tracking-tight text-slate-950 sm:text-2xl">{{ number_format($stats['categories']) }}</dd>
                        </div>
                    </dl>
                </div>

                @if ($hero)
                    <div class="reveal reveal-delay-1 relative mx-auto w-full max-w-xl xl:max-w-none">
                        <div class="absolute -inset-4 rounded-[2.5rem] bg-gradient-to-br from-indigo-500/20 via-violet-500/10 to-transparent blur-2xl"></div>

                        <a
                            href="{{ route('events.show', ['slug' => $hero->slug]) }}"
                            class="group relative block overflow-hidden rounded-[2.25rem] border border-white/60 bg-slate-900 shadow-[0_40px_100px_-40px_rgb(15_23_42/0.55)]"
                        >
                            <div class="relative aspect-[5/6] sm:aspect-[6/5] xl:aspect-[5/6]">
                                @if ($hero->banner_url)
                                    <img
                                        src="{{ $hero->banner_url }}"
                                        alt="{{ $hero->title }}"
                                        class="h-full w-full object-cover transition duration-700 group-hover:scale-[1.03]"
                                    >
                                @else
                                    <img src="{{ asset('images/evently-banner-placeholder.svg') }}" alt="" class="h-full w-full object-cover">
                                @endif

                                <div class="absolute inset-0 bg-gradient-to-t from-[#070b1c] via-[#070b1c]/35 to-transparent"></div>

                                <div class="absolute inset-x-0 bottom-0 p-6 sm:p-8">
                                    <div class="flex items-center gap-2">
                                        <span class="rounded-full bg-white/15 px-3 py-1.5 text-[0.68rem] font-semibold uppercase tracking-[0.14em] text-white backdrop-blur">
                                            Featured event
                                        </span>
                                        @if ($hero->category)
                                            <span class="rounded-full bg-indigo-500/80 px-3 py-1.5 text-[0.68rem] font-semibold text-white backdrop-blur">
                                                {{ $hero->category->name }}
                                            </span>
                                        @endif
                                    </div>

                                    <h2 class="mt-4 text-balance text-3xl font-semibold leading-tight tracking-[-0.035em] text-white sm:text-4xl">
                                        {{ $hero->title }}
                                    </h2>

                                    <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-slate-200">
                                        <span class="flex items-center gap-2">
                                            <svg class="h-4 w-4 text-indigo-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                                <rect x="3.5" y="5" width="17" height="15.5" rx="3"/>
                                                <path d="M8 3.5v3M16 3.5v3M3.5 9.5h17" stroke-linecap="round"/>
                                            </svg>
                                            {{ $hero->start_at->format('D, d M · H:i') }}
                                        </span>
                                        <span class="flex items-center gap-2">
                                            <svg class="h-4 w-4 text-indigo-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                                <path d="M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11Z" stroke-linejoin="round"/>
                                                <circle cx="12" cy="10" r="2.4"/>
                                            </svg>
                                            <span class="line-clamp-1">{{ $hero->location }}</span>
                                        </span>
                                    </div>

                                    <div class="mt-6 flex items-center justify-between gap-4 border-t border-white/15 pt-5">
                                        <div>
                                            <p class="text-[0.65rem] font-semibold uppercase tracking-[0.16em] text-slate-400">Tickets</p>
                                            <p class="mt-0.5 text-lg font-semibold text-white">
                                                {{ (float) $hero->ticket_price > 0 ? 'Rp ' . number_format((float) $hero->ticket_price, 0, ',', '.') : 'Free entry' }}
                                            </p>
                                        </div>
                                        <span class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition group-hover:bg-indigo-50">
                                            View event
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <div class="absolute -left-4 top-8 z-10 hidden w-56 rounded-2xl border border-slate-900/5 bg-white/95 p-4 shadow-xl shadow-slate-900/10 backdrop-blur sm:block">
                            <div class="flex items-center justify-between text-xs font-medium text-slate-500">
                                <span>Seats reserved</span>
                                <span class="font-semibold text-slate-900">{{ $hero->registeredCount() }}/{{ $hero->quota }}</span>
                            </div>
                            <div class="mt-2.5 h-1.5 overflow-hidden rounded-full bg-slate-100">
                                <div
                                    class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-violet-500"
                                    style="width: {{ min(100, (int) round(($hero->registeredCount() / max($hero->quota, 1)) * 100)) }}%"
                                ></div>
                            </div>
                            <p class="mt-2 text-[0.7rem] text-slate-500">
                                {{ $hero->isSoldOut() ? 'This one is sold out.' : $hero->remainingQuota() . ' seats still available.' }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    @if ($featured->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 pt-10 sm:px-6 lg:px-8">
            <div class="reveal flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="eyebrow">Handpicked for you</p>
                    <h2 class="mt-2 text-3xl font-semibold tracking-[-0.04em] text-slate-950 sm:text-4xl">Featured events</h2>
                </div>
                <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 transition hover:text-indigo-700">
                    Browse the full calendar
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>

            <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($featured as $event)
                    <div @class(['reveal', 'reveal-delay-1' => $loop->index === 1, 'reveal-delay-2' => $loop->index === 2])>
                        <x-event-card :event="$event" />
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    @if ($categories->isNotEmpty())
        <section id="categories" class="mx-auto max-w-7xl scroll-mt-28 px-4 pt-20 sm:px-6 lg:px-8">
            <div class="reveal flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="eyebrow">Something for every mood</p>
                    <h2 class="mt-2 text-3xl font-semibold tracking-[-0.04em] text-slate-950 sm:text-4xl">Browse by category</h2>
                </div>
                <p class="max-w-md text-sm leading-6 text-slate-500">
                    From deep-dive conference days to slow weekends — start with the room you want to be in.
                </p>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($categories as $category)
                    <a
                        href="{{ route('events.index', ['category' => $category->slug]) }}"
                        class="reveal group flex items-center justify-between gap-4 rounded-2xl border border-slate-900/5 bg-white p-5 shadow-[0_1px_2px_rgb(15_23_42/0.03)] transition duration-300 hover:-translate-y-0.5 hover:border-indigo-200 hover:bg-indigo-50/40"
                    >
                        <div class="flex items-center gap-3.5">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-100 to-violet-100 text-sm font-semibold text-indigo-700">
                                {{ strtoupper(substr($category->name, 0, 2)) }}
                            </span>
                            <div>
                                <h3 class="text-sm font-semibold text-slate-950">{{ $category->name }}</h3>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $category->events_count }} upcoming</p>
                            </div>
                        </div>
                        <svg class="h-4 w-4 text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if ($upcoming->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 pt-20 sm:px-6 lg:px-8">
            <div class="reveal flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="eyebrow">Save the date</p>
                    <h2 class="mt-2 text-3xl font-semibold tracking-[-0.04em] text-slate-950 sm:text-4xl">Coming up next</h2>
                </div>
                <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 transition hover:text-indigo-700">
                    See all events
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>

            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($upcoming as $event)
                    <div @class(['reveal', 'reveal-delay-1' => $loop->index === 1, 'reveal-delay-2' => $loop->index === 2, 'reveal-delay-3' => $loop->index === 3])>
                        <x-event-card :event="$event" />
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section id="about" class="mx-auto max-w-7xl scroll-mt-28 px-4 pt-24 sm:px-6 lg:px-8">
        <div class="reveal relative overflow-hidden rounded-[2.5rem] bg-[#0b1026] px-6 py-12 text-white sm:px-10 lg:px-14 lg:py-16">
            <div class="pointer-events-none absolute -right-32 -top-32 h-96 w-96 rounded-full bg-indigo-500/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-40 left-1/3 h-96 w-96 rounded-full bg-violet-500/20 blur-3xl"></div>

            <div class="relative grid gap-12 lg:grid-cols-[0.9fr_1.1fr] lg:gap-16">
                <div>
                    <p class="text-[0.7rem] font-semibold uppercase tracking-[0.2em] text-indigo-300">Why Evently</p>
                    <h2 class="mt-3 text-balance text-3xl font-semibold tracking-[-0.045em] sm:text-4xl">
                        Less admin. More moments that matter.
                    </h2>
                    <p class="mt-5 max-w-md text-sm leading-7 text-slate-400 sm:text-base">
                        Evently pairs a considered public calendar with a calm organizer workspace. Guests get a beautiful page and a ticket in seconds; organizers get registrations, seat limits, and check-ins in one place.
                    </p>

                    @if (Route::has('filament.admin.auth.login'))
                        <a
                            href="{{ route('filament.admin.auth.login') }}"
                            class="mt-8 inline-flex items-center gap-2 rounded-full bg-white px-6 py-3.5 text-sm font-semibold text-slate-950 transition hover:-translate-y-0.5 hover:bg-indigo-50"
                        >
                            Open organizer workspace
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    @endif
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-5 backdrop-blur">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-500/20 text-indigo-200">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <circle cx="11" cy="11" r="7"/><path d="m20 20-3.2-3.2" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <h3 class="mt-4 text-sm font-semibold text-white">Curated discovery</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-400">A focused calendar — searchable, filterable, and free of clutter.</p>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-5 backdrop-blur">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-500/20 text-violet-200">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <path d="M4 8.5A2.5 2.5 0 0 1 6.5 6h11A2.5 2.5 0 0 1 20 8.5v1a3 3 0 0 0 0 5v1a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 4 15.5v-1a3 3 0 0 0 0-5v-1Z" stroke-linejoin="round"/>
                                <path d="M13.5 6v12" stroke-dasharray="2 3" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <h3 class="mt-4 text-sm font-semibold text-white">One-minute registration</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-400">No account, no checkout maze. A unique ticket code appears instantly.</p>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-5 backdrop-blur">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/20 text-emerald-200">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <circle cx="12" cy="12" r="9"/><path d="m8.5 12.5 2.4 2.4 4.6-5.4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <h3 class="mt-4 text-sm font-semibold text-white">Reliable check-in</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-400">Manual code check-in built for the door — fast when the room is filling up.</p>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-5 backdrop-blur">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500/20 text-amber-200">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <path d="M4 19.5V10m5.3 9.5V5.5M14.7 19.5v-7m5.3 7V8" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <h3 class="mt-4 text-sm font-semibold text-white">Live seat visibility</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-400">Quotas, sold-out states, and registration counts stay honest in real time.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
