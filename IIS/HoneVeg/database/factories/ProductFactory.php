<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Label;
use App\Models\ProductLabelValue;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Product::class;


    public function definition(): array
    {
        return [
            'seller_user_id' => User::factory()->seller(),
            'category_id' => Category::factory(),
            'price' => fake()->randomFloat(null, 10, 200),
            'description' => fake()->sentence(),
            'available_amount' => fake()->randomNumber(),
            'total_rating' => 0,
            'name' => "product"
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Product $product) {
            
            $category = Category::where('category_id', $product->category_id)->first();

            if($product->name == "product")
            {
                $matchingLabels = Label::where('category_id', $product->category_id)->get();      
                foreach ($matchingLabels as $label) {
                    ProductLabelValue::factory()->create([
                        'product_id' => $product->product_id,
                        'label_id' => $label->label_id,
                    ]);
                }
    
                while($category->category_id != 1) {
                    $category = Category::where('category_id', $category->parent_category_id)->first();
                    $matchingLabels = Label::where('category_id', $category->category_id)->get();      
                    foreach ($matchingLabels as $label) {
                        if ($label->name == "price type") {
                            ProductLabelValue::factory()->create([
                                'product_id' => $product->product_id,
                                'label_id' => $label->label_id,
                                'label_value' => fake()->randomElement(['1 kg', 'piece', '100 g'])
                            ]);
                        }else {
                            ProductLabelValue::factory()->create([
                                'product_id' => $product->product_id,
                                'label_id' => $label->label_id,
                            ]);
                        }
    
                    }
                }
            }
        });
    }
}
