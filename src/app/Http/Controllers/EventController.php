<?php

namespace App\Http\Controllers;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:80'],
            'category' => ['nullable', 'string', 'max:80'],
            'when' => ['nullable', Rule::in(['upcoming', 'past', 'all'])],
        ]);

        $search = trim((string) ($filters['q'] ?? ''));
        $category = (string) ($filters['category'] ?? '');
        $when = (string) ($filters['when'] ?? 'upcoming');

        $events = Event::query()
            ->publiclyVisible()
            ->with('category')
            ->withCount('registrations')
            ->when($search !== '', fn (Builder $query) => $query->search($search))
            ->when($category !== '', fn (Builder $query) => $query->inCategory($category))
            ->when($when === 'upcoming', function (Builder $query): Builder {
                return $query->where('status', EventStatus::Published->value)
                    ->where('start_at', '>=', now())
                    ->orderBy('start_at');
            })
            ->when($when === 'past', function (Builder $query): Builder {
                return $query->where('end_at', '<', now())->orderByDesc('start_at');
            })
            ->when($when === 'all', fn (Builder $query) => $query->orderByDesc('start_at'))
            ->paginate(9)
            ->withQueryString();

        $categories = EventCategory::query()
            ->whereHas('events', fn (Builder $query) => $query->publiclyVisible())
            ->withCount([
                'events' => fn (Builder $query) => $query->publiclyVisible(),
            ])
            ->orderBy('name')
            ->get();

        return view('events.index', compact('events', 'categories', 'filters', 'search', 'category', 'when'));
    }

    public function show(string $slug): View
    {
        $event = Event::query()
            ->publiclyVisible()
            ->with('category')
            ->withCount('registrations')
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Event::query()
            ->published()
            ->upcoming()
            ->whereKeyNot($event->getKey())
            ->with('category')
            ->withCount('registrations')
            ->when(
                $event->event_category_id,
                fn (Builder $query) => $query->where('event_category_id', $event->event_category_id),
            )
            ->orderBy('start_at')
            ->limit(3)
            ->get();

        if ($related->count() < 3) {
            $related = $related
                ->merge(
                    Event::query()
                        ->published()
                        ->upcoming()
                        ->whereKeyNot($event->getKey())
                        ->whereNotIn('id', $related->modelKeys())
                        ->with('category')
                        ->withCount('registrations')
                        ->orderBy('start_at')
                        ->limit(3 - $related->count())
                        ->get(),
                )
                ->take(3);
        }

        return view('events.show', compact('event', 'related'));
    }
}
