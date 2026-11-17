<x-layouts.public
    :title="'Register · ' . $event->title"
    :description="'Reserve your seat for ' . $event->title . ' at ' . $event->location . '. No account needed.'"
    :noindex="true"
>
    <div class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
        <a href="{{ route('events.show', ['slug' => $event->slug]) }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-slate-900">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M19 12H5M11 6l-6 6 6 6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Back to event
        </a>
    </div>

    <section class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)] lg:items-start lg:gap-12">
            <div class="lg:sticky lg:top-24">
                <p class="eyebrow">Reserve your seat</p>
                <h1 class="mt-2 text-balance text-4xl font-semibold leading-[1.08] tracking-[-0.045em] text-slate-950 sm:text-5xl">
                    You are almost in.
                </h1>
                <p class="mt-4 max-w-md text-base leading-7 text-slate-600">
                    Tell us who is coming and we will generate a unique ticket code right away. No account and no payment needed.
                </p>

                <div class="mt-8 overflow-hidden rounded-[2rem] border border-slate-900/5 bg-white shadow-[0_1px_2px_rgb(15_23_42/0.03),0_24px_60px_-45px_rgb(15_23_42/0.3)]">
                    <div class="relative aspect-[16/8] overflow-hidden bg-slate-100">
                        @if ($event->banner_url)
                            <img src="{{ $event->banner_url }}" alt="{{ $event->title }}" class="h-full w-full object-cover">
                        @else
                            <img src="{{ asset('images/evently-banner-placeholder.svg') }}" alt="" class="h-full w-full object-cover">
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/40 to-transparent"></div>
                        @if ($event->category)
                            <span class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1.5 text-xs font-semibold text-slate-800 backdrop-blur">
                                {{ $event->category->name }}
                            </span>
                        @endif
                    </div>

                    <div class="p-6">
                        <h2 class="text-xl font-semibold tracking-[-0.025em] text-slate-950">{{ $event->title }}</h2>

                        <div class="mt-4 space-y-3 text-sm text-slate-600">
                            <p class="flex items-start gap-2.5">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                    <rect x="3.5" y="5" width="17" height="15.5" rx="3"/>
                                    <path d="M8 3.5v3M16 3.5v3M3.5 9.5h17" stroke-linecap="round"/>
                                </svg>
                                <span>
                                    <span class="font-medium text-slate-900">{{ $event->start_at->format('l, d F Y') }}</span><br>
                                    {{ $event->start_at->format('H:i') }} – {{ $event->end_at->format('H:i') }}
                                </span>
                            </p>
                            <p class="flex items-start gap-2.5">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                    <path d="M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11Z" stroke-linejoin="round"/>
                                    <circle cx="12" cy="10" r="2.4"/>
                                </svg>
                                <span>{{ $event->location }}</span>
                            </p>
                            @if ($event->organizer)
                                <p class="flex items-start gap-2.5">
                                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                        <path d="M4 20V8.5L12 4l8 4.5V20M9 20v-6h6v6" stroke-linejoin="round"/>
                                    </svg>
                                    <span>Hosted by {{ $event->organizer }}</span>
                                </p>
                            @endif
                        </div>

                        <div class="mt-5 flex items-center justify-between gap-3 border-t border-slate-900/5 pt-4">
                            <div>
                                <p class="text-[0.65rem] font-semibold uppercase tracking-[0.14em] text-slate-400">Ticket</p>
                                <p class="mt-0.5 text-lg font-semibold text-slate-950">
                                    {{ (float) $event->ticket_price > 0 ? 'Rp ' . number_format((float) $event->ticket_price, 0, ',', '.') : 'Free entry' }}
                                </p>
                            </div>
                            <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                                {{ $event->remainingQuota() }} seats left
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-[2rem] border border-slate-900/5 bg-white p-6 shadow-[0_24px_70px_-45px_rgb(15_23_42/0.35)] sm:p-9">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold tracking-[-0.03em] text-slate-950">Your details</h2>
                        <p class="mt-1.5 text-sm text-slate-500">One registration per email address, per event.</p>
                    </div>
                    <span class="hidden rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 sm:inline-flex">
                        Step 1 of 1
                    </span>
                </div>

                @if ($errors->any())
                    <div role="alert" class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-sm text-rose-900">
                        <div class="flex items-start gap-2.5">
                            <svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <circle cx="12" cy="12" r="9"/><path d="M12 8v4.5M12 16h.01" stroke-linecap="round"/>
                            </svg>
                            <div>
                                <p class="font-semibold">We could not complete your registration.</p>
                                <ul class="mt-1.5 list-inside list-disc space-y-1 text-xs leading-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('events.register.store', ['slug' => $event->slug]) }}" method="POST" class="mt-7 space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-800">Full name</label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Your full name"
                            @class([
                                'mt-2 block w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30',
                                'border-rose-300' => $errors->has('name'),
                                'border-slate-200' => ! $errors->has('name'),
                            ])
                        >
                        @error('name')
                            <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-800">Email address</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            inputmode="email"
                            placeholder="you@example.com"
                            @class([
                                'mt-2 block w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30',
                                'border-rose-300' => $errors->has('email'),
                                'border-slate-200' => ! $errors->has('email'),
                            ])
                        >
                        @error('email')
                            <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-800">Phone number</label>
                        <input
                            id="phone"
                            type="tel"
                            name="phone"
                            value="{{ old('phone') }}"
                            required
                            autocomplete="tel"
                            inputmode="tel"
                            placeholder="08xx xxxx xxxx"
                            @class([
                                'mt-2 block w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30',
                                'border-rose-300' => $errors->has('phone'),
                                'border-slate-200' => ! $errors->has('phone'),
                            ])
                        >
                        @error('phone')
                            <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-5 py-4 text-sm font-semibold text-white shadow-sm shadow-indigo-600/25 transition hover:-translate-y-0.5 hover:bg-indigo-500 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                    >
                        Confirm registration
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>

                    <p class="text-center text-xs leading-5 text-slate-500">
                        No payment is taken. Your ticket code appears on the next screen — save or print it for the door.
                    </p>
                </form>
            </div>
        </div>
    </section>
</x-layouts.public>
