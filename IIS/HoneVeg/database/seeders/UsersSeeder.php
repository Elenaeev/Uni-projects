<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        //USAGE:
        //php artisan db:seed UsersSeeder
        //php artisan migrate:fresh --seed -> refresh db and seed data

        \App\Models\User::factory()->admin()->create(['email' => "admin@admin.com", 'name' => "Kristian Faiz Santiago"]);
        \App\Models\User::factory()->moderator()->create(['email' => "moder@moder.com", 'name' => "Dana Omphile Mullen"]);
        \App\Models\User::factory()->seller()->create(['email' => "prodavac@prodavac.com", 'name' => "Agathinos Baar"]);
        \App\Models\User::factory()->user()->create(['email' => "franta@franta.com", 'name' => "Kelvin Carter"]);

    }
}
