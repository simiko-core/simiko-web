<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;
use App\Models\Post;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $posts = Post::inRandomOrder()->take(5)->get();
        foreach ($posts as $post) {
            Banner::create([
                'post_id' => $post->id,
                'active' => true,
            ]);
        }
    }
}
