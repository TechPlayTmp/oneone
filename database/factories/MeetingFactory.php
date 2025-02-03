<?php

namespace Database\Factories;

use App\Enums\MeetingStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meeting>
 */
class MeetingFactory extends Factory
{

    public function definition(): array
    {
        $host = User::inRandomOrder()->first();
        $guest = User::where('id', '!=', $host->id)->inRandomOrder()->first();

        return [
            'scheduled_at' => $this->faker->dateTimeBetween('-1 week', '+1 week'),
            'status' => $this->faker->randomElement(MeetingStatusEnum::cases())->value,
            'host_id' => $host->id,
            'guest_id' => $guest->id,
            'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
