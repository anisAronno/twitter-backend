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

        User::create([
            "name" => "anis",
            "email" => "anis904692@gmail.com",
            "username" => "anisaronno",
            "password" => "password",
            "image" => "https://avatars.githubusercontent.com/u/38912435?v=4",
        ]);

        User::factory()->count(20)
            ->has(
                Tweet::factory()->count(3)
                ->has(Reaction::factory()->count(2), 'reactions')
            )
            ->has(Follower::factory()->count(3))
            ->create();
    }
}
