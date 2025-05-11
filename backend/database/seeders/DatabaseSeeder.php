<?php

namespace Database\Seeders;

use App\Models\ToolType;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ToolTypeSeeder::class,
            StatusSeeder::class,
            ToolSeeder::class,
            CustomToolSeeder::class,
            CoatingsTypeSeeder::class,
            CoatingsPricesSeeder::class
        ]);
    }
}
