<?php

namespace Database\Seeders;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $categories = EventCategory::query()->pluck('id', 'slug');

        foreach ($this->events() as $data) {
            $startAt = Carbon::now()
                ->addDays($data['start_offset'])
                ->setTime($data['start_hour'], $data['start_minute'] ?? 0);

            $endAt = (clone $startAt)->addHours($data['duration']);

            $event = Event::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'event_category_id' => $categories[$data['category']] ?? null,
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'banner' => 'events/' . $data['banner'],
                    'organizer' => $data['organizer'],
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                    'location' => $data['location'],
                    'quota' => $data['quota'],
                    'ticket_price' => $data['price'],
                    'status' => $data['status'],
                    'is_featured' => $data['featured'] ?? false,
                ],
            );

            $this->publishBanner($event->banner);
        }
    }

    private function publishBanner(?string $path): void
    {
        if (! $path) {
            return;
        }

        $source = database_path('seeders/assets/event-banners/' . basename($path));

        if (! is_file($source)) {
            return;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            $disk->put($path, file_get_contents($source));
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function events(): array
    {
        return [
            [
                'title' => 'Tech Future Summit 2026',
                'slug' => 'tech-future-summit-2026',
                'category' => 'technology',
                'description' => "Tech Future Summit is a two-day gathering for builders, founders, and product teams exploring what comes next. Expect practical keynotes, live demos, and honest conversations about shipping technology that people actually love.\n\nThis year's theme is Human by Design — how we keep products useful, accessible, and grounded while the pace of change keeps accelerating. Sessions cover applied AI, platform engineering, design systems, and responsible product growth.",
                'organizer' => 'Evently Labs',
                'start_offset' => 24,
                'start_hour' => 9,
                'duration' => 32,
                'location' => 'Jakarta Convention Center, Senayan',
                'quota' => 250,
                'price' => 150000,
                'status' => EventStatus::Published,
                'featured' => true,
                'banner' => 'tech-future-summit.jpg',
            ],
            [
                'title' => 'Startup Pitch Night',
                'slug' => 'startup-pitch-night',
                'category' => 'business',
                'description' => "Five early-stage teams. Five minutes each. One room full of operators, investors, and curious builders.\n\nStartup Pitch Night is a relaxed evening of sharp storytelling and useful feedback. Come to scout the next wave of local startups, meet potential collaborators, or simply borrow energy from people who are building in public.",
                'organizer' => 'Founders Circle Jakarta',
                'start_offset' => 12,
                'start_hour' => 18,
                'start_minute' => 30,
                'duration' => 4,
                'location' => 'GoWork Central Park, Jakarta Barat',
                'quota' => 120,
                'price' => 100000,
                'status' => EventStatus::Published,
                'featured' => true,
                'banner' => 'startup-pitch-night.jpg',
            ],
            [
                'title' => 'Jazz Under the Stars',
                'slug' => 'jazz-under-the-stars',
                'category' => 'music',
                'description' => "An open-air evening of contemporary jazz with a lineup of Indonesian musicians and special guests. Bring a light jacket, settle in, and let the horns do the rest.\n\nDoors open at 18:00. Food and drinks are available on site, and the venue is fully accessible.",
                'organizer' => 'Northlight Live',
                'start_offset' => 35,
                'start_hour' => 19,
                'duration' => 4,
                'location' => 'Plaza Senayan Amphitheatre, Jakarta',
                'quota' => 300,
                'price' => 200000,
                'status' => EventStatus::Published,
                'featured' => false,
                'banner' => 'jazz-under-the-stars.jpg',
            ],
            [
                'title' => 'Modern Art Exhibition: New Shapes',
                'slug' => 'modern-art-exhibition-new-shapes',
                'category' => 'arts-culture',
                'description' => "New Shapes brings together twelve emerging artists working across painting, sculpture, and digital media. The exhibition asks a simple question: what does form mean when so much of our life happens on screens?\n\nYour ticket includes a guided walkthrough at 14:00 and 17:00. The gallery is open until 20:00.",
                'organizer' => 'Museum MACAN Collective',
                'start_offset' => 15,
                'start_hour' => 10,
                'duration' => 10,
                'location' => 'Museum MACAN, Jakarta Barat',
                'quota' => 150,
                'price' => 75000,
                'status' => EventStatus::Published,
                'featured' => true,
                'banner' => 'modern-art-exhibition.jpg',
            ],
            [
                'title' => 'Jakarta City Marathon',
                'slug' => 'jakarta-city-marathon',
                'category' => 'sports',
                'description' => "A city-wide run through the heart of Jakarta with 5K, 10K, and half-marathon categories. Whether you are chasing a personal best or just here for the atmosphere, the route is flat, fast, and packed with support.\n\nRace pack collection details will be shared by email. Participants must be 15 or older on race day.",
                'organizer' => 'Jakarta Sport Community',
                'start_offset' => 60,
                'start_hour' => 5,
                'duration' => 6,
                'location' => 'Monumen Nasional, Jakarta Pusat',
                'quota' => 500,
                'price' => 250000,
                'status' => EventStatus::Published,
                'featured' => false,
                'banner' => 'jakarta-city-marathon.jpg',
            ],
            [
                'title' => 'Product Design Workshop',
                'slug' => 'product-design-workshop',
                'category' => 'education',
                'description' => "A hands-on, small-group workshop on turning fuzzy product ideas into testable prototypes. We will cover discovery interviews, journey mapping, and rapid interface sketching — with time to work on your own case.\n\nBring a laptop and one problem you genuinely care about. Seats are intentionally limited to keep the room collaborative.",
                'organizer' => 'Design Practice Asia',
                'start_offset' => 8,
                'start_hour' => 13,
                'duration' => 4,
                'location' => 'WeWork SCBD, Jakarta Selatan',
                'quota' => 40,
                'price' => 350000,
                'status' => EventStatus::Published,
                'featured' => false,
                'banner' => 'product-design-workshop.jpg',
            ],
            [
                'title' => 'Weekend Wellness Retreat',
                'slug' => 'weekend-wellness-retreat',
                'category' => 'health-wellness',
                'description' => "Step away from notifications for a full day of slow movement, breathing practice, and honest conversation. The retreat includes a guided morning walk, two workshop sessions, and a plant-based lunch.\n\nNo experience is required. Mats and props are provided; just bring comfortable clothes and an open mind.",
                'organizer' => 'Still Space',
                'start_offset' => 90,
                'start_hour' => 8,
                'duration' => 10,
                'location' => 'Plataran Puncak, Bogor',
                'quota' => 80,
                'price' => 500000,
                'status' => EventStatus::Published,
                'featured' => false,
                'banner' => 'wellness-retreat.jpg',
            ],
            [
                'title' => 'Street Food Festival',
                'slug' => 'street-food-festival',
                'category' => 'food-drink',
                'description' => "Forty local vendors, one open park, and zero entry fee. From sate and rendang to modern desserts and specialty coffee, this is a relaxed weekend built around good food and good company.\n\nFree entry with registration. Food and drinks are purchased directly from vendors, and a portion of proceeds supports community kitchens.",
                'organizer' => 'Senayan Park Collective',
                'start_offset' => 28,
                'start_hour' => 16,
                'duration' => 7,
                'location' => 'Senayan Park, Jakarta Pusat',
                'quota' => 400,
                'price' => 0,
                'status' => EventStatus::Published,
                'featured' => true,
                'banner' => 'street-food-festival.jpg',
            ],
            [
                'title' => 'Data & AI Conference',
                'slug' => 'data-ai-conference',
                'category' => 'technology',
                'description' => "A full-day program on the practical side of data and AI: evaluation, governance, retrieval, and the messy work of getting models into production. Speakers include practitioners from fintech, logistics, and public services.\n\nThe agenda is being finalized and will be published soon.",
                'organizer' => 'Evently Labs',
                'start_offset' => 75,
                'start_hour' => 9,
                'duration' => 8,
                'location' => 'The Ritz-Carlton Pacific Place, Jakarta',
                'quota' => 200,
                'price' => 175000,
                'status' => EventStatus::Draft,
                'featured' => false,
                'banner' => 'data-ai-conference.jpg',
            ],
            [
                'title' => 'Indie Music Night',
                'slug' => 'indie-music-night',
                'category' => 'music',
                'description' => "A stripped-back night with three independent bands and a room that knows every chorus. This edition has wrapped — thank you to everyone who came out and made it loud.\n\nStay tuned for the next season announcement.",
                'organizer' => 'Northlight Live',
                'start_offset' => -21,
                'start_hour' => 19,
                'duration' => 4,
                'location' => 'Bengkel Space SCBD, Jakarta Selatan',
                'quota' => 180,
                'price' => 100000,
                'status' => EventStatus::Completed,
                'featured' => false,
                'banner' => 'indie-music-night.jpg',
            ],
        ];
    }
}
