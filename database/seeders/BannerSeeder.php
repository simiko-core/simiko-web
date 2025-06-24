<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;
use App\Models\Feed;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        // Get random feeds (both posts and events can be featured as banners)
        $feeds = Feed::inRandomOrder()->take(5)->get();
        
        if ($feeds->isEmpty()) {
            $this->command->warn('No feeds found. Please run FeedSeeder first.');
            return;
        }

        foreach ($feeds as $feed) {
            Banner::create([
                'feed_id' => $feed->id,
                'active' => true,
            ]);
        }

        $this->command->info('Banner seeder completed successfully!');
        $this->command->info('Created ' . Banner::count() . ' banners');
    }
}
