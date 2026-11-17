<?php

namespace Database\Seeders;

use App\Models\EventCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Technology', 'description' => 'Conferences, hackathons, and product launches shaping the future.'],
            ['name' => 'Business', 'description' => 'Networking sessions, pitch nights, and growth-focused gatherings.'],
            ['name' => 'Music', 'description' => 'Live gigs, intimate showcases, and music festivals.'],
            ['name' => 'Arts & Culture', 'description' => 'Exhibitions, screenings, and creative conversations.'],
            ['name' => 'Sports', 'description' => 'Races, tournaments, and community fitness events.'],
            ['name' => 'Education', 'description' => 'Workshops, classes, and hands-on learning experiences.'],
            ['name' => 'Health & Wellness', 'description' => 'Retreats and sessions designed to help you recharge.'],
            ['name' => 'Food & Drink', 'description' => 'Street food markets, tastings, and culinary festivals.'],
        ];

        foreach ($categories as $category) {
            EventCategory::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                ],
            );
        }
    }
}
