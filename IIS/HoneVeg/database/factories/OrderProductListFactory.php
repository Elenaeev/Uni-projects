<?php

namespace Database\Factories;

use App\Models\order;
use App\Models\OrderProductList;
use App\Models\Product;
use App\Models\SellerOrders;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderProductList>
 */
class OrderProductListFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = OrderProductList::class;



    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'order_id' => Order::factory(),
            'product_amount' => 1
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (OrderProductList $orderProductList) {      
            $products = Product::where('product_id', $orderProductList->product_id)->get();  
                
            foreach ($products as $product) {
                SellerOrders::factory()->create([
                    'seller_id' => $product->seller_user_id,
                    'order_id' => $orderProductList->order_id,
                ]);
            }

        });
    }
}
