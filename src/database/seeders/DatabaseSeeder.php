<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Waitlist;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Waitlist::factory(20)->create();

        // User::factory(10)->create();

        User::factory()->create([
            'name'  => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }
}
