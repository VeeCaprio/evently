@props(['event'])

@php
    $soldOut = $event->isSoldOut();
    $remaining = $event->remainingQuota();
    $price = (float) $event->ticket_price;
@endphp

<a
    href="{{ route('events.show', ['slug' => $event->slug]) }}"
    class="group flex h-full flex-col overflow-hidden rounded-[1.75rem] border border-slate-900/5 bg-white shadow-[0_1px_2px_rgb(15_23_42/0.03),0_24px_60px_-40px_rgb(15_23_42/0.3)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_1px_2px_rgb(15_23_42/0.04),0_30px_70px_-38px_rgb(49_46_129/0.35)]"
>
    <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
        @if ($event->banner_url)
            <img
                src="{{ $event->banner_url }}"
                alt="{{ $event->title }}"
                class="event-card-media h-full w-full object-cover"
                loading="lazy"
            >
        @else
            <img
                src="{{ asset('images/evently-banner-placeholder.svg') }}"
                alt=""
                class="event-card-media h-full w-full object-cover"
                loading="lazy"
            >
        @endif

        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-950/45 via-transparent to-transparent"></div>

        <div class="absolute left-4 top-4 flex items-center gap-2">
            <div class="flex h-14 w-14 flex-col items-center justify-center rounded-2xl bg-white/95 text-slate-950 shadow-sm backdrop-blur">
                <span class="text-[0.6rem] font-semibold uppercase tracking-[0.14em] text-indigo-600">{{ $event->start_at->format('M') }}</span>
                <span class="text-xl font-semibold leading-none tracking-tight">{{ $event->start_at->format('d') }}</span>
            </div>

            @if ($soldOut)
                <span class="rounded-full bg-rose-600/95 px-3 py-1.5 text-[0.68rem] font-semibold uppercase tracking-[0.12em] text-white shadow-sm">
                    Sold out
                </span>
            @elseif ($event->isUpcoming() && $remaining <= 15)
                <span class="rounded-full bg-amber-400/95 px-3 py-1.5 text-[0.68rem] font-semibold uppercase tracking-[0.12em] text-amber-950 shadow-sm">
                    {{ $remaining }} seats left
                </span>
            @endif
        </div>

        @if ($event->category)
            <span class="absolute right-4 top-4 rounded-full bg-slate-950/55 px-3 py-1.5 text-[0.68rem] font-semibold text-white backdrop-blur">
                {{ $event->category->name }}
            </span>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-5 sm:p-6">
        <p class="flex items-center gap-2 text-xs font-medium text-slate-500">
            <time datetime="{{ $event->start_at->toIso8601String() }}">
                {{ $event->start_at->format('D, d M Y') }}
            </time>
            <span class="h-1 w-1 rounded-full bg-slate-300"></span>
            <span>{{ $event->start_at->format('H:i') }}</span>
        </p>

        <h3 class="mt-2.5 line-clamp-2 text-lg font-semibold tracking-[-0.02em] text-slate-950 transition group-hover:text-indigo-700">
            {{ $event->title }}
        </h3>

        <p class="mt-2 flex items-center gap-1.5 text-sm text-slate-500">
            <svg class="h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                <path d="M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11Z" stroke-linejoin="round"/>
                <circle cx="12" cy="10" r="2.4"/>
            </svg>
            <span class="truncate">{{ $event->location }}</span>
        </p>

        <div class="mt-auto flex items-center justify-between gap-3 border-t border-slate-900/5 pt-4">
            <span class="text-sm font-semibold text-slate-950">
                {{ $price > 0 ? 'Rp ' . number_format($price, 0, ',', '.') : 'Free entry' }}
            </span>

            <span class="flex items-center gap-1.5 text-sm font-medium text-indigo-600">
                View event
                <svg class="h-4 w-4 transition group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M5 12h14M13 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        </div>
    </div>
</a>
