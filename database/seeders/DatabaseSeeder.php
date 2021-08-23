<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            LanguagesSeeder::class,
            RestaurantSeeder::class,
            StyleSeeder::class
        ]);
        // \App\Models\User::factory(10)->create();
    }
}
