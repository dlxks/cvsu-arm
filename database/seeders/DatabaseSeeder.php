<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->createMany([[
            'name' => 'Test User',
            'email' => 'tristan.sangangbayan@cvsu.edu.ph',
            // 'role' => 'admin'
        ], [
            'name' => 'Test User2',
            'email' => 'sangangbayant@gmail.com',
            // 'role' => 'faculty'
        ]]);
    }
}
