<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class EventRegistrationSeeder extends Seeder
{
    /**
     * Target registrations and check-ins per event slug.
     *
     * @var array<string, array{registrations: int, checked_in: int}>
     */
    private array $plan = [
        'tech-future-summit-2026' => ['registrations' => 186, 'checked_in' => 0],
        'startup-pitch-night' => ['registrations' => 98, 'checked_in' => 0],
        'jazz-under-the-stars' => ['registrations' => 214, 'checked_in' => 0],
        'modern-art-exhibition-new-shapes' => ['registrations' => 92, 'checked_in' => 0],
        'jakarta-city-marathon' => ['registrations' => 341, 'checked_in' => 0],
        'product-design-workshop' => ['registrations' => 40, 'checked_in' => 0],
        'weekend-wellness-retreat' => ['registrations' => 37, 'checked_in' => 0],
        'street-food-festival' => ['registrations' => 156, 'checked_in' => 0],
        'data-ai-conference' => ['registrations' => 0, 'checked_in' => 0],
        'indie-music-night' => ['registrations' => 180, 'checked_in' => 172],
    ];

    /**
     * @var array<string, array{name: string, email: string, phone: string, ticket_code: string}>
     */
    private array $demoRegistrations = [
        'tech-future-summit-2026' => [
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@evently.test',
            'phone' => '081234567890',
            'ticket_code' => 'EVL-DEMO-0001',
        ],
        'indie-music-night' => [
            'name' => 'Siti Rahma',
            'email' => 'siti.rahma@evently.test',
            'phone' => '081298765432',
            'ticket_code' => 'EVL-DEMO-0002',
        ],
    ];

    public function run(): void
    {
        $events = Event::query()
            ->whereIn('slug', array_keys($this->plan))
            ->get()
            ->keyBy('slug');

        $admin = User::query()->where('email', 'admin@evently.test')->first();

        foreach ($this->plan as $slug => $target) {
            $event = $events->get($slug);

            if (! $event) {
                continue;
            }

            $authoredAt = $event->start_at->isPast()
                ? $event->start_at->copy()->subDays(30)
                : Carbon::now()->subDays(30);

            $registrations = $this->seedDemoRegistration($event, $authoredAt);
            $registrations = $registrations->merge(
                $this->seedGeneratedRegistrations($event, $target['registrations'], $authoredAt),
            );

            $this->seedCheckIns($event, $registrations, $target['checked_in'], $admin);
        }
    }

    private function seedDemoRegistration(Event $event, Carbon $authoredAt): Collection
    {
        $demo = $this->demoRegistrations[$event->slug] ?? null;

        if (! $demo) {
            return collect();
        }

        $registration = EventRegistration::firstOrCreate(
            [
                'event_id' => $event->getKey(),
                'email' => $demo['email'],
            ],
            [
                'name' => $demo['name'],
                'phone' => $demo['phone'],
                'ticket_code' => $demo['ticket_code'],
                'created_at' => $authoredAt->copy()->addDay(),
                'updated_at' => $authoredAt->copy()->addDay(),
            ],
        );

        return collect([$registration]);
    }

    private function seedGeneratedRegistrations(Event $event, int $target, Carbon $authoredAt): Collection
    {
        $registrations = collect();
        $demoCount = isset($this->demoRegistrations[$event->slug]) ? 1 : 0;
        $needed = max($target - $demoCount, 0);

        for ($index = 0; $index < $needed; $index++) {
            [$firstName, $lastName] = $this->nameFor($index);

            $registration = EventRegistration::firstOrCreate(
                [
                    'event_id' => $event->getKey(),
                    'email' => strtolower($firstName . '.' . $lastName) . ($index + 1) . '@example.test',
                ],
                [
                    'name' => $firstName . ' ' . $lastName,
                    'phone' => '08' . str_pad((string) (1200000000 + $event->getKey() * 1000 + $index), 10, '0', STR_PAD_LEFT),
                    'created_at' => $authoredAt->copy()->addMinutes($index * 17),
                    'updated_at' => $authoredAt->copy()->addMinutes($index * 17),
                ],
            );

            $registrations->push($registration);
        }

        return $registrations;
    }

    private function seedCheckIns(Event $event, Collection $registrations, int $target, ?User $admin): void
    {
        if ($target === 0) {
            return;
        }

        $registrations
            ->take($target)
            ->each(function (EventRegistration $registration) use ($event, $admin): void {
                EventAttendance::firstOrCreate(
                    ['event_registration_id' => $registration->getKey()],
                    [
                        'checked_in_at' => $event->start_at->copy()->addMinutes(($registration->getKey() * 7) % 95),
                        'checked_in_by' => $admin?->getKey(),
                    ],
                );
            });
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function nameFor(int $index): array
    {
        $firstNames = [
            'Adi', 'Ayu', 'Bagas', 'Citra', 'Dewi', 'Dimas', 'Eka', 'Fajar',
            'Gita', 'Hana', 'Indra', 'Intan', 'Joko', 'Kirana', 'Laras', 'Made',
            'Maya', 'Naufal', 'Nadia', 'Oka', 'Putri', 'Raka', 'Rani', 'Sinta',
            'Tegar', 'Vina', 'Wahyu', 'Yoga', 'Zahra', 'Arif',
        ];

        $lastNames = [
            'Pratama', 'Wijaya', 'Santoso', 'Nugroho', 'Saputra', 'Hidayat',
            'Permata', 'Kusuma', 'Maulana', 'Sari', 'Firmansyah', 'Lestari',
            'Pradana', 'Anggraini', 'Setiawan', 'Wulandari', 'Hartono', 'Maharani',
            'Ramadhan', 'Utami',
        ];

        return [
            $firstNames[$index % count($firstNames)],
            $lastNames[intdiv($index, count($firstNames)) % count($lastNames)],
        ];
    }
}
