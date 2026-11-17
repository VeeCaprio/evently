<?php

namespace Database\Factories;

use App\Enums\EventStatus;
use App\Models\EventCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->catchPhrase();
        $startAt = fake()->dateTimeBetween('+3 days', '+3 months');
        $endAt = (clone $startAt)->modify('+' . fake()->numberBetween(2, 8) . ' hours');

        return [
            'event_category_id' => EventCategory::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::lower(Str::random(5)),
            'description' => fake()->paragraphs(3, true),
            'banner' => null,
            'organizer' => fake()->company(),
            'start_at' => $startAt,
            'end_at' => $endAt,
            'location' => fake()->city() . ' Convention Center',
            'quota' => fake()->numberBetween(50, 300),
            'ticket_price' => fake()->randomElement([0, 50000, 100000, 150000, 250000]),
            'status' => EventStatus::Published,
            'is_featured' => false,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => EventStatus::Draft]);
    }

    public function completed(): static
    {
        return $this->state(function () {
            $startAt = fake()->dateTimeBetween('-3 months', '-2 weeks');

            return [
                'status' => EventStatus::Completed,
                'start_at' => $startAt,
                'end_at' => (clone $startAt)->modify('+4 hours'),
            ];
        });
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }

    public function soldOut(): static
    {
        return $this->state(fn () => ['quota' => 0]);
    }
}
