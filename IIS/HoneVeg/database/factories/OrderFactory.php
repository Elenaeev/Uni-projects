<?php

namespace Database\Factories;

use App\Models\User;
use Faker\Core\DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\OrderProductList;
use App\Models\Order;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'customer_user_id' => User::factory()->user(),
            'creation_date' => now(),
            'close_date' => now(),
            'delivery_date' => now(),
            'status' => fake()->randomElement(['cart', 'in process', 'canceled', 'delivered'])
        ];
    }

}