<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\ChangeCategoriesDesign;
use App\Models\Event;
use App\Models\EventParticipants;
use App\Models\EventProducts;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Roles;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderProductList;
use App\Models\Rating;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function createEvent($productsNum = 1, $participants = 0)
    {
        return $this->afterCreating(function (User $user) use ($productsNum, $participants) {
            $products = Product::factory($productsNum)->create([
                'seller_user_id' => $user->user_id,
            ]);

            $event = Event::factory()->create([
                'seller_id' => $user->user_id,
            ]);

            foreach($products as $product) {
                EventProducts::factory()->create([
                    'event_id' => $event->event_id,
                    'product_id' => $product->product_id
                ]);
            }
            
            EventParticipants::factory($participants)->create([
                'event_id' => $event->event_id
            ]);
        });
    }

    public function createCategoryDesign($num=1) {
        return $this->afterCreating(function (User $user) use ($num) {
            ChangeCategoriesDesign::factory($num)->create([
                'creator_id' => $user->user_id
            ]);
        });
    }

    public function rateProducts($num=1) {
        return $this->afterCreating(function (User $user) use ($num) {
            $products = Product::factory($num)->create([
                'seller_user_id' => $user->user_id
            ]);

            foreach($products as $product) {
                Rating::factory()->create([
                    'user_id' => $user->user_id,
                    'product_id' => $product->product_id
                ]);
            }
        });
    }

    public function withProducts($num=1) {
        return $this->afterCreating(function (User $user) use ($num) {
            $category = Category::factory()->create();
            Product::factory($num)->create([
                'seller_user_id' => $user->user_id,
                'category_id' => $category->category_id
            ]);
        });
    }

    public function withOrders($numOrders=1, $numProducts=1) {
        return $this->afterCreating(function (User $user) use ($numOrders, $numProducts) {

            $orders = Order::factory($numOrders)->create([
                'customer_user_id' => $user->user_id
            ]);

            foreach($orders as $order) {
                OrderProductList::factory($numProducts)->create([
                    'order_id' => $order->order_id
                ]);
            }
        });
    }

    public function withRole()
    {
        return $this->afterCreating(function (User $user) {
            Roles::factory()->create([
                'user_id' => $user->user_id,
                'role' => fake()->randomElement(['admin', 'moderator', 'user', 'seller', 'suspended'])
            ]);
        });
    }

    public function seller()
    {
        return $this->afterCreating(function (User $user) {
            Roles::factory()->create([
                'user_id' => $user->user_id,
                'role' => 'seller'
            ]);
        });
    }

    public function user()
    {
        return $this->afterCreating(function (User $user) {
            Roles::factory()->create([
                'user_id' => $user->user_id,
                'role' => 'user'
            ]);
        });
    }

    public function admin()
    {
        return $this->afterCreating(function (User $user) {
            Roles::factory()->create([
                'user_id' => $user->user_id,
                'role' => 'admin'
            ]);
        });
    }

    public function moderator()
    {
        return $this->afterCreating(function (User $user) {
            Roles::factory()->create([
                'user_id' => $user->user_id,
                'role' => 'moderator'
            ]);
        });
    }

    public function suspended()
    {
        return $this->afterCreating(function (User $user) {
            Roles::factory()->create([
                'user_id' => $user->user_id,
                'role' => 'suspended'
            ]);
        });
    }
}
