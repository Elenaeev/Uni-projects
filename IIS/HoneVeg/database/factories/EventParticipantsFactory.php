<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\EventParticipants;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventParticipants>
 */
class EventParticipantsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = EventParticipants::class;


    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'user_id' => User::factory()->withRole()
        ];
    }
}
