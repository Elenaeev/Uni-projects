<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\ChangeCategoriesDesign;
use App\Models\ChangeCategoriesDesignLabels;
use App\Models\DesignLabels;
use App\Models\Label;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChangeCategoriesDesign>
 */
class ChangeCategoriesDesignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = ChangeCategoriesDesign::class;

    public function definition(): array
    {
        return [
            'moderator_id' => User::factory()->moderator(),
            'creator_id' => User::factory()->withRole(),
            'parent_category_id' => Category::factory(),
            'name' => 'category',
            'description' => fake()->text(),
            'creation_date' => now(),
            'close_date' => now(),
            'status' => fake()->randomElement(['created', 'approved', 'declined'])
        ];
    }

    public function configure() {
        

        return $this->afterCreating(function (ChangeCategoriesDesign $design) {
            DesignLabels::factory()->create([
                'design_id' => $design->design_id
            ]);
        });
    }

}
