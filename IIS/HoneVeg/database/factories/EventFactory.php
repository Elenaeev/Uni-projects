<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Event;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'seller_id' => User::factory()->seller(),
            'start_date' => now(),
            'end_date' => now(),
            'address' => fake()->address(),
            'name' => 'event',
            'description' => fake()->text()
        ];
    }
}
