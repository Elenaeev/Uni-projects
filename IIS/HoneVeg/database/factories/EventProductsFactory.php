<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\EventProducts;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventProducts>
 */
class EventProductsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = EventProducts::class;


    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'product_id' => Product::factory()
        ];
    }
}
