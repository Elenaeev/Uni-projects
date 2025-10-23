<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        //USAGE:
        //php artisan db:seed
        //php artisan migrate:fresh --seed -> refresh db and seed data

        //change categoryfactory

        $mainCat = \App\Models\Category::factory()->create([
            'name' => 'base category'
        ]);

        //creates 10 users with random roles
        //\App\Models\User::factory(10)->withRole()->create();
        \App\Models\User::factory()->admin()->create();
        \App\Models\User::factory()->moderator()->create();

        //user, roles, products x5, category, label, labelproducts x5
        \App\Models\User::factory()->seller()->withProducts()->create();

        //user, roles, order, products x9, sellerorders, orderproducts, category, label, labelproducts
        \App\Models\User::factory()->user()->withOrders()->create();

        //user seller+participants, roles, products, event, eventproducts, category, label, labelproducts
        \App\Models\User::factory()->seller()->createEvent()->create();

        //user, roles, products x5, raitings, category, label, labelproducts
        \App\Models\User::factory()->user()->rateProducts()->create();

        //user, roles, categorydesign, designlabel
        \App\Models\User::factory()->user()->CreateCategoryDesign()->create();

        //category x6, labels
        \App\Models\Category::factory()->withChildren(3)->create();


    }
}
