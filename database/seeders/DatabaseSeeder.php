<?php

namespace Database\Seeders;

use App\Models\Player;
use App\Models\User;
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
        /**
         * Create a user that we can log in with
         */
        User::factory()->create([
            'name' => 'Demo Admin',
            'email' => 'demo@demo.com',
        ]);


        $this->call(PlayerSeeder::class);
    }
}
