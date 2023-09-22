<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'user_type' => 'admin',
            'name' => 'admin',
            'email' => 'admin@api.com',
        ]);
        \App\Models\User::factory()->create([
            'user_type' => 'member',
            'name' => 'member',
            'email' => 'member@api.com',
        ]);

        \App\Models\Post::factory()->create([
            'user_id'=>1,
            'title'=>'title-post',
            'content'=>'title-content',
        ]);

        \App\Models\Post::factory()->create([
            'user_id'=>2,
            'title'=>'title-post-1',
            'content'=>'title-content-1',
        ]);
    }
}
