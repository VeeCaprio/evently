<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventRegistration;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featured = Event::query()
            ->published()
            ->upcoming()
            ->featured()
            ->with('category')
            ->withCount('registrations')
            ->orderBy('start_at')
            ->limit(3)
            ->get();

        $hero = $featured->first() ?? Event::query()
            ->published()
            ->upcoming()
            ->with('category')
            ->withCount('registrations')
            ->orderBy('start_at')
            ->first();

        $upcoming = Event::query()
            ->published()
            ->upcoming()
            ->with('category')
            ->withCount('registrations')
            ->when(
                $featured->isNotEmpty(),
                fn ($query) => $query->whereNotIn('id', $featured->modelKeys()),
            )
            ->orderBy('start_at')
            ->limit(4)
            ->get();

        $categories = EventCategory::query()
            ->whereHas('events', fn ($query) => $query->published()->upcoming())
            ->withCount([
                'events' => fn ($query) => $query->published()->upcoming(),
            ])
            ->orderBy('name')
            ->get();

        $stats = [
            'upcoming' => Event::query()->published()->upcoming()->count(),
            'registrations' => EventRegistration::query()
                ->whereHas('event', fn ($query) => $query->published())
                ->count(),
            'categories' => $categories->count(),
        ];

        return view('home', compact('featured', 'hero', 'upcoming', 'categories', 'stats'));
    }
}
