<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


// run php artisan db:seed to exec DatabaseSeeder class,
// which runs the registered seeders to call facs
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Register seeders
        $this->call([
            UserSeeder::class,
        ]);
        // User::factory()->count(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
