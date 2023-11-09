<?php

namespace Database\Seeders;

use App\Models\Follower;
use App\Models\Reaction;
use App\Models\Tweet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Tweet::truncate();
        Reaction::truncate();
        Follower::truncate();
        Schema::enableForeignKeyConstraints();

        User::factory()->count(3)
            ->has(
                Tweet::factory()->count(100)
                ->has(Reaction::factory()->count(20), 'reactions')
            )
            ->has(Follower::factory()->count(10), 'followers')
            ->has(Follower::factory()->count(10), 'following')
            ->create();
    }
}
