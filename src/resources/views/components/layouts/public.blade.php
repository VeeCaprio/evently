@props([
    'title' => null,
    'description' => 'Evently is a curated calendar of talks, workshops, music, food, and wellness events across Jakarta.',
    'noindex' => false,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ? $title . ' · Evently' : 'Evently · Events worth showing up for' }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="theme-color" content="#4f46e5">
    @if ($noindex)
        <meta name="robots" content="noindex, nofollow">
    @endif

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Evently">
    <meta property="og:title" content="{{ $title ?? 'Evently · Events worth showing up for' }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" href="{{ asset('images/evently-mark.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">

    <script>
        document.documentElement.classList.add('js');

        window.setTimeout(function () {
            if (!window.__eventlyReady) {
                document.querySelectorAll('.reveal').forEach(function (element) {
                    element.classList.add('is-visible');
                });
            }
        }, 2000);
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-[#f7f7fb] font-sans text-slate-900 antialiased selection:bg-indigo-200 selection:text-indigo-950">
    <a
        href="#main"
        class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-full focus:bg-slate-950 focus:px-5 focus:py-3 focus:text-sm focus:font-semibold focus:text-white"
    >
        Skip to content
    </a>

    <header class="sticky top-0 z-50 border-b border-slate-900/5 bg-white/80 backdrop-blur-xl">
        <nav class="mx-auto flex h-20 max-w-7xl items-center justify-between gap-6 px-4 sm:px-6 lg:px-8" aria-label="Main navigation">
            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5" aria-label="Evently home">
                <img src="{{ asset('images/evently-logo.svg') }}" alt="Evently" class="h-8 w-auto" width="156" height="36">
            </a>

            <div class="hidden items-center gap-1 lg:flex">
                <a
                    href="{{ route('home') }}"
                    @class([
                        'rounded-full px-4 py-2 text-sm font-medium transition',
                        'bg-slate-950 text-white' => request()->routeIs('home'),
                        'text-slate-600 hover:bg-slate-900/5 hover:text-slate-950' => ! request()->routeIs('home'),
                    ])
                >
                    Home
                </a>
                <a
                    href="{{ route('events.index') }}"
                    @class([
                        'rounded-full px-4 py-2 text-sm font-medium transition',
                        'bg-slate-950 text-white' => request()->routeIs('events.*', 'tickets.*'),
                        'text-slate-600 hover:bg-slate-900/5 hover:text-slate-950' => ! request()->routeIs('events.*', 'tickets.*'),
                    ])
                >
                    Events
                </a>
                <a href="{{ route('home') }}#categories" class="rounded-full px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-900/5 hover:text-slate-950">
                    Categories
                </a>
                <a href="{{ route('home') }}#about" class="rounded-full px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-900/5 hover:text-slate-950">
                    Why Evently
                </a>
            </div>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('events.index') }}"
                    class="hidden rounded-full bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-600/25 transition hover:-translate-y-0.5 hover:bg-indigo-500 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 sm:inline-flex"
                >
                    Find an event
                </a>

                <button
                    type="button"
                    id="mobile-menu-button"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-slate-900/10 bg-white text-slate-700 transition hover:bg-slate-50 lg:hidden"
                    aria-controls="mobile-menu"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
        </nav>

        <div id="mobile-menu" class="hidden border-t border-slate-900/5 bg-white px-4 pb-6 pt-4 lg:hidden">
            <div class="mx-auto flex max-w-7xl flex-col gap-1">
                <a href="{{ route('home') }}" class="rounded-2xl px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">Home</a>
                <a href="{{ route('events.index') }}" class="rounded-2xl px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">Events</a>
                <a href="{{ route('home') }}#categories" class="rounded-2xl px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">Categories</a>
                <a href="{{ route('home') }}#about" class="rounded-2xl px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50">Why Evently</a>
                <a href="{{ route('events.index') }}" class="mt-2 rounded-2xl bg-indigo-600 px-4 py-3 text-center text-sm font-semibold text-white">Find an event</a>
            </div>
        </div>
    </header>

    @if (session('status') || session('error'))
        <div class="mx-auto max-w-7xl px-4 pt-5 sm:px-6 lg:px-8">
            <div
                role="status"
                @class([
                    'flex items-start gap-3 rounded-2xl border px-4 py-3.5 text-sm shadow-sm',
                    'border-rose-200 bg-rose-50 text-rose-900' => session('error'),
                    'border-emerald-200 bg-emerald-50 text-emerald-900' => ! session('error'),
                ])
            >
                <svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    @if (session('error'))
                        <circle cx="12" cy="12" r="9"/><path d="M12 8v4.5M12 16h.01" stroke-linecap="round"/>
                    @else
                        <circle cx="12" cy="12" r="9"/><path d="m8.5 12.5 2.4 2.4 4.6-5.4" stroke-linecap="round" stroke-linejoin="round"/>
                    @endif
                </svg>
                <p>{{ session('error') ?? session('status') }}</p>
            </div>
        </div>
    @endif

    <main id="main">
        {{ $slot }}
    </main>

    <footer class="mt-24 border-t border-slate-900/5 bg-[#0b1026] text-slate-300">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-[1.4fr_1fr_1fr]">
                <div>
                    <img src="{{ asset('images/evently-logo-light.svg') }}" alt="Evently" class="h-8 w-auto" width="156" height="36">
                    <p class="mt-5 max-w-sm text-sm leading-6 text-slate-400">
                        A calmer way to find remarkable events — and a simpler way for organizers to welcome every guest.
                    </p>
                    <p class="mt-5 text-xs font-medium uppercase tracking-[0.2em] text-indigo-300">
                        Events worth showing up for
                    </p>
                </div>

                <div>
                    <h2 class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Explore</h2>
                    <div class="mt-4 flex flex-col gap-3 text-sm">
                        <a href="{{ route('events.index') }}" class="text-slate-300 transition hover:text-white">All events</a>
                        <a href="{{ route('events.index', ['when' => 'upcoming']) }}" class="text-slate-300 transition hover:text-white">Upcoming</a>
                        <a href="{{ route('events.index', ['when' => 'past']) }}" class="text-slate-300 transition hover:text-white">Past events</a>
                        <a href="{{ route('home') }}#categories" class="text-slate-300 transition hover:text-white">Categories</a>
                    </div>
                </div>

                <div>
                    <h2 class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Organizers</h2>
                    <div class="mt-4 flex flex-col gap-3 text-sm">
                        @if (Route::has('filament.admin.auth.login'))
                            <a href="{{ route('filament.admin.auth.login') }}" class="text-slate-300 transition hover:text-white">Organizer sign in</a>
                        @endif
                        <a href="{{ route('home') }}#about" class="text-slate-300 transition hover:text-white">Why Evently</a>
                        <span class="text-slate-500">Jakarta, Indonesia</span>
                    </div>
                </div>
            </div>

            <div class="mt-14 flex flex-col gap-3 border-t border-white/10 pt-6 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ date('Y') }} Evently. Crafted for memorable gatherings.</p>
                <p>Built with Laravel &amp; Filament.</p>
            </div>
        </div>
    </footer>
</body>
</html>
