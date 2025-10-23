<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Label;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Label>
 */
class LabelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Label::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->word(),
            'type' => fake()->randomElement(['number', 'text'])
        ];
    }
}
