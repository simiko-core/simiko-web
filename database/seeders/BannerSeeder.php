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

        $this->command->info('Creating strategic banner selections for Indonesian university ecosystem...');

        // Get feeds that are good candidates for banners
        $bannerCandidates = $this->getBannerCandidates();

        if ($bannerCandidates->isEmpty()) {
            $this->command->warn('No suitable feeds found for banners.');
            return;
        }

        $createdBanners = 0;

        // Create high-priority banners (upcoming events like GEMASTIK, competitions)
        $upcomingEvents = $this->getUpcomingEvents($bannerCandidates);
        foreach ($upcomingEvents->take(3) as $event) {
            $this->createBanner($event, true, 'upcoming_event');
            $createdBanners++;
        }

        // Create featured content banners (trending posts, achievements)
        $featuredPosts = $this->getFeaturedPosts($bannerCandidates);
        foreach ($featuredPosts->take(2) as $post) {
            $this->createBanner($post, true, 'featured_content');
            $createdBanners++;
        }

        // Create diversity banners (representation from different UKM categories)
        $diversityFeeds = $this->getDiversityFeeds($bannerCandidates);
        foreach ($diversityFeeds->take(4) as $feed) {
            $this->createBanner($feed, true, 'diversity');
            $createdBanners++;
        }

        // Create national program banners (government initiatives, collaborations)
        $nationalPrograms = $this->getNationalProgramFeeds($bannerCandidates);
        foreach ($nationalPrograms->take(2) as $program) {
            $this->createBanner($program, true, 'national_program');
            $createdBanners++;
        }

        // Create some inactive banners (past content that could be reactivated)
        $pastContent = $this->getPastContent();
        foreach ($pastContent->take(2) as $content) {
            $this->createBanner($content, false, 'archive');
            $createdBanners++;
        }

        $this->command->info('Banner seeder completed successfully!');
        $this->command->info("Created {$createdBanners} strategic banners from {$totalFeeds} available feeds");
        $this->command->info('Banner distribution by type:');
        $this->command->info('- Upcoming events (competitions, workshops): ' . $upcomingEvents->take(3)->count());
        $this->command->info('- Featured content (achievements, trending): ' . $featuredPosts->take(2)->count());
        $this->command->info('- UKM diversity representation: ' . $diversityFeeds->take(4)->count());
        $this->command->info('- National programs & collaborations: ' . $nationalPrograms->take(2)->count());
        $this->command->info('- Archive banners: ' . $pastContent->take(2)->count());
        $this->command->info('Active banners: ' . Banner::where('active', true)->count());
        $this->command->info('Inactive banners: ' . Banner::where('active', false)->count());
    }

    /**
     * Get feeds that are good candidates for banners (Indonesian university priority)
     */
    private function getBannerCandidates()
    {
        return Feed::with(['unitKegiatan', 'paymentConfiguration'])
            ->whereNotNull('image') // Must have images for visual appeal
            ->where('created_at', '>=', Carbon::now()->subDays(180)) // Not too old (6 months)
            ->where(function ($query) {
                // Prioritize events and important posts
                $query->where('type', 'event')
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('type', 'post')
                            ->where('created_at', '>=', Carbon::now()->subDays(60)); // Recent posts only
                    });
            })
            ->orderByDesc('created_at')
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
     * Get feeds related to national programs and government initiatives
     */
    private function getNationalProgramFeeds($candidates)
    {
        // Look for feeds that mention Indonesian government programs or national initiatives
        $nationalKeywords = [
            'GEMASTIK',
            'KRI',
            'LIMA',
            'PON',
            'Kemendikbudristek',
            'BPPT',
            'Kemkominfo',
            'Digital Indonesia',
            'Program 1000 Startup',
            'Kampus Merdeka',
            'MBKM',
            'Wonderful Indonesia',
            'Indonesia Emas',
            'Smart City',
            'Desa Digital',
            'Kalpataru',
            'PMI',
            'BNPB',
            'OJK',
            'BUMN',
            'Pancasila',
            'Bhinneka'
        ];

        return $candidates->filter(function ($feed) use ($nationalKeywords) {
            $content = strtolower($feed->title . ' ' . $feed->content);
            foreach ($nationalKeywords as $keyword) {
                if (str_contains($content, strtolower($keyword))) {
                    return true;
                }
            }
            return false;
        })->sortByDesc('created_at')->values();
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
        // Priority scoring system for Indonesian university context
        $scoredFeeds = $feeds->map(function ($feed) {
            $score = 0;

            // Recent content gets higher score
            $daysOld = Carbon::now()->diffInDays($feed->created_at);
            $score += max(0, 30 - $daysOld); // Up to 30 points for recent content

            // Events get bonus points
            if ($feed->type === 'event') {
                $score += 25; // Increased from 20

                // Upcoming events get extra bonus
                if ($feed->event_date && Carbon::parse($feed->event_date)->isFuture()) {
                    $score += 20; // Increased from 15
                }

                // Paid events get slight bonus (usually important)
                if ($feed->is_paid) {
                    $score += 10; // Increased from 5
                }
            }

            // Indonesian national program bonus
            $content = strtolower($feed->title . ' ' . $feed->content);
            $nationalKeywords = [
                'gemastik' => 25,
                'kri' => 25,
                'lima' => 20,
                'pon' => 15,
                'kemendikbudristek' => 15,
                'bppt' => 10,
                'kemkominfo' => 10,
                'kampus merdeka' => 20,
                'mbkm' => 20,
                'indonesia emas' => 15,
                'wonderful indonesia' => 15,
                'smart city' => 10,
                'desa digital' => 10,
                'gojek' => 15,
                'tokopedia' => 15,
                'bukalapak' => 10,
                'shopee' => 10,
                'kalpataru' => 20,
                'juara' => 15,
                'nasional' => 10,
                'internasional' => 20
            ];

            foreach ($nationalKeywords as $keyword => $points) {
                if (str_contains($content, $keyword)) {
                    $score += $points;
                }
            }

            // Competition and achievement bonus
            $competitionKeywords = ['kompetisi', 'competition', 'juara', 'champion', 'winner', 'prestasi', 'achievement'];
            foreach ($competitionKeywords as $keyword) {
                if (str_contains($content, $keyword)) {
                    $score += 15;
                    break; // Only add once per feed
                }
            }

            // Community impact bonus (important for Indonesian universities)
            $communityKeywords = ['bakti sosial', 'pengabdian', 'masyarakat', 'community', '3t', 'desa', 'umkm'];
            foreach ($communityKeywords as $keyword) {
                if (str_contains($content, $keyword)) {
                    $score += 10;
                    break;
                }
            }

            // Title length bonus (not too short, not too long)
            $titleLength = strlen($feed->title);
            if ($titleLength >= 25 && $titleLength <= 80) { // Adjusted for Indonesian titles
                $score += 15; // Increased from 10
            }

            // Collaboration bonus
            $collaborationKeywords = ['kolaborasi', 'kerjasama', 'collaboration', 'partnership', 'bersama'];
            foreach ($collaborationKeywords as $keyword) {
                if (str_contains($content, $keyword)) {
                    $score += 12;
                    break;
                }
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

        $statusText = $active ? '🟢 AKTIF' : '🔸 ARSIP';
        $typeText = match ($type) {
            'upcoming_event' => '📅 Event Mendatang',
            'featured_content' => '⭐ Konten Unggulan',
            'diversity' => '🎯 Representasi UKM',
            'national_program' => '🇮🇩 Program Nasional',
            'archive' => '📂 Arsip',
            default => ucfirst(str_replace('_', ' ', $type))
        };

        $this->command->info("[$statusText] $typeText: \"{$feed->title}\" dari {$feed->unitKegiatan->alias}");
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
            'national_program' => Carbon::now()->subDays(rand(1, 14)), // Recent national programs
            default => Carbon::now()->subDays(rand(1, 30)),
        };
    }
}
