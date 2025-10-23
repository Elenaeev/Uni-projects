<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Roles;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Roles>
 */
class RolesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Roles::class;


    public function definition(): array
    {
        return [
            'role'=>fake()->randomElement(['admin', 'moderator', 'user', 'seller', 'suspended']),
            'user_id'=>User::factory()
        ];
    }
}
