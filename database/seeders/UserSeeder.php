<?php

namespace Database\Seeders;

use App\Models\Follower;
use App\Models\Reaction;
use App\Models\Tweet;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->count(20)
            ->has(
                Tweet::factory()->count(3)
                ->has(Reaction::factory()->count(2), 'reactions')
            )
            ->has(Follower::factory()->count(3))
            ->create();
    }
}
