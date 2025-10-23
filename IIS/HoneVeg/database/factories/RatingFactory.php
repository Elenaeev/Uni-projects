<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Rating;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rating>
 */
class RatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Rating::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->user(),
            'product_id' => Product::factory(),
            'rating' => fake()->randomFloat(null, 1, 5)
        ];
    }
}
