<?php

namespace Database\Factories;

use App\Models\Label;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ProductLabelValue;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductLabelValue>
 */
class ProductLabelValueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = ProductLabelValue::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'label_id' => Label::factory(),
            'label_value' => fake()->word()
        ];
    }
}
