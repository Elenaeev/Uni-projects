<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Order;
use App\Models\SellerOrders;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FarmerOrders>
 */
class SellerOrdersFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = SellerOrders::class;


    public function definition(): array
    {
        return [
            'seller_id' => User::factory()->seller(),
            'order_id' => Order::factory(),
            'status' => fake()->randomElement(['accepted', 'canceled', 'delivered'])
        ];
    
    }
}
