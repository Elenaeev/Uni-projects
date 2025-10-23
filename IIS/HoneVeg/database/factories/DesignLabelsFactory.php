<?php

namespace Database\Factories;

use App\Models\ChangeCategoriesDesign;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DesignLabels;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DesignLabels>
 */
class DesignLabelsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    
     protected $model = DesignLabels::class;
   

    public function definition(): array
    {
        return [
            'design_id' => ChangeCategoriesDesign::factory(),
            'name' => fake()->word(),
            'type' => fake()->randomElement(['number', 'text'])
        ];
    }
}
