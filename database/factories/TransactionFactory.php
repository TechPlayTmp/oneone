<?php

namespace Database\Factories;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user_from = User::inRandomOrder()->first();
        $user_to = User::where('id', '!=', $user_from)->inRandomOrder()->first();
        $meeting = Meeting::inRandomOrder()->first();

        return [
            'from_id' => $user_from->id,
            'to_id' => $user_to->id,
            'amount' => $this->faker->randomFloat(2, 1, 10000),
            'description' => $this->faker->sentence(),
            'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
