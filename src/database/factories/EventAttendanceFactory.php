<?php

namespace Database\Factories;

use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventAttendance>
 */
class EventAttendanceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_registration_id' => EventRegistration::factory(),
            'checked_in_at' => now(),
            'checked_in_by' => null,
        ];
    }

    public function checkedInBy(User $user): static
    {
        return $this->state(fn () => ['checked_in_by' => $user->getKey()]);
    }
}
