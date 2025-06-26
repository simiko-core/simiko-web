<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;
use App\Models\Feed;
use Carbon\Carbon;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        // Check if feeds exist
        $totalFeeds = Feed::count();
        if ($totalFeeds === 0) {
            $this->command->warn('No feeds found. Please run FeedSeeder first.');
            return;
        }

        $this->command->info('Creating strategic banner selections...');

        // Get feeds that are good candidates for banners
        $bannerCandidates = $this->getBannerCandidates();

        if ($bannerCandidates->isEmpty()) {
            $this->command->warn('No suitable feeds found for banners.');
            return;
        }

        $createdBanners = 0;

        // Create high-priority banners (upcoming events)
        $upcomingEvents = $this->getUpcomingEvents($bannerCandidates);
        foreach ($upcomingEvents->take(2) as $event) {
            $this->createBanner($event, true, 'upcoming_event');
            $createdBanners++;
        }

        // Create featured content banners (recent popular posts)
        $featuredPosts = $this->getFeaturedPosts($bannerCandidates);
        foreach ($featuredPosts->take(2) as $post) {
            $this->createBanner($post, true, 'featured_content');
            $createdBanners++;
        }

        // Create diversity banners (different UKMs representation)
        $diversityFeeds = $this->getDiversityFeeds($bannerCandidates);
        foreach ($diversityFeeds->take(3) as $feed) {
            $this->createBanner($feed, true, 'diversity');
            $createdBanners++;
        }

        // Create some inactive banners (past content that could be reactivated)
        $pastContent = $this->getPastContent();
        foreach ($pastContent->take(2) as $content) {
            $this->createBanner($content, false, 'archive');
            $createdBanners++;
        }

        $this->command->info('Banner seeder completed successfully!');
        $this->command->info("Created {$createdBanners} banners from {$totalFeeds} available feeds");
        $this->command->info('Active banners: ' . Banner::where('active', true)->count());
        $this->command->info('Inactive banners: ' . Banner::where('active', false)->count());
    }

    /**
     * Get feeds that are good candidates for banners
     */
    private function getBannerCandidates()
    {
        return Feed::with(['unitKegiatan', 'paymentConfiguration'])
            ->whereNotNull('image') // Must have images for visual appeal
            ->where('created_at', '>=', Carbon::now()->subDays(180)) // Not too old
            ->get();
    }

    /**
     * Get upcoming events that should be promoted
     */
    private function getUpcomingEvents($candidates)
    {
        return $candidates
            ->where('type', 'event')
            ->where('event_date', '>=', Carbon::now())
            ->where('event_date', '<=', Carbon::now()->addDays(60))
            ->sortBy('event_date')
            ->values();
    }

    /**
     * Get featured posts (recent, from active UKMs)
     */
    private function getFeaturedPosts($candidates)
    {
        return $candidates
            ->where('type', 'post')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->sortByDesc('created_at')
            ->values();
    }

    /**
     * Get feeds from different UKMs for diversity
     */
    private function getDiversityFeeds($candidates)
    {
        $ukmGroups = $candidates->groupBy('unit_kegiatan_id');
        $diversityFeeds = collect();

        // Get one representative feed from each UKM
        foreach ($ukmGroups as $ukmId => $feeds) {
            $bestFeed = $this->selectBestFeedFromUkm($feeds);
            if ($bestFeed) {
                $diversityFeeds->push($bestFeed);
            }
        }

        return $diversityFeeds->shuffle();
    }

    /**
     * Select the best feed from a UKM for banner promotion
     */
    private function selectBestFeedFromUkm($feeds)
    {
        // Priority scoring system
        $scoredFeeds = $feeds->map(function ($feed) {
            $score = 0;

            // Recent content gets higher score
            $daysOld = Carbon::now()->diffInDays($feed->created_at);
            $score += max(0, 30 - $daysOld); // Up to 30 points for recent content

            // Events get bonus points
            if ($feed->type === 'event') {
                $score += 20;

                // Upcoming events get extra bonus
                if ($feed->event_date && Carbon::parse($feed->event_date)->isFuture()) {
                    $score += 15;
                }

                // Paid events get slight bonus (usually important)
                if ($feed->is_paid) {
                    $score += 5;
                }
            }

            // Title length bonus (not too short, not too long)
            $titleLength = strlen($feed->title);
            if ($titleLength >= 20 && $titleLength <= 60) {
                $score += 10;
            }

            return ['feed' => $feed, 'score' => $score];
        });

        $bestFeed = $scoredFeeds->sortByDesc('score')->first();
        return $bestFeed ? $bestFeed['feed'] : null;
    }

    /**
     * Get past content that could be archive banners
     */
    private function getPastContent()
    {
        return Feed::with(['unitKegiatan'])
            ->whereNotNull('image')
            ->where('created_at', '<', Carbon::now()->subDays(60))
            ->where('created_at', '>=', Carbon::now()->subDays(365))
            ->inRandomOrder()
            ->limit(5)
            ->get();
    }

    /**
     * Create a banner with specific metadata
     */
    private function createBanner($feed, $active, $type)
    {
        Banner::create([
            'feed_id' => $feed->id,
            'active' => $active,
            'created_at' => $this->getBannerCreationDate($type),
            'updated_at' => now(),
        ]);

        $this->command->info("Created {$type} banner: \"{$feed->title}\" from {$feed->unitKegiatan->alias}");
    }

    /**
     * Get realistic creation date for banner based on type
     */
    private function getBannerCreationDate($type)
    {
        return match ($type) {
            'upcoming_event' => Carbon::now()->subDays(rand(1, 7)), // Recent promotion
            'featured_content' => Carbon::now()->subDays(rand(3, 14)), // Recent featuring
            'diversity' => Carbon::now()->subDays(rand(7, 30)), // Moderate age
            'archive' => Carbon::now()->subDays(rand(30, 90)), // Older banner
            default => Carbon::now()->subDays(rand(1, 30)),
        };
    }
}
