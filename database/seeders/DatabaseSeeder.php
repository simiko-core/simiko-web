<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run seeders in proper order
        $this->call([
            ShieldSeeder::class,           // Setup roles and permissions first
            UnitKegiatanSeeder::class,     // Create UKMs
            UserSeeder::class,             // Create users (including UKM admins)
            UnitKegiatanProfileSeeder::class, // Create UKM profiles
            PendaftaranAnggotaSeeder::class,  // Create registrations
            FeedSeeder::class,             // Create feeds (posts and events)
            PaymentSeeder::class,          // Create payment configurations and transactions
            AchievementSeeder::class,      // Create achievements
            ActivityGallerySeeder::class,  // Create activity galleries
            BannerSeeder::class,           // Create banners (depends on feeds)
        ]);

        $this->command->info('🎉 All seeders completed successfully!');
        $this->command->info('📊 Database Summary:');
        $this->command->info('- Users: ' . User::count());
        $this->command->info('- UKMs: ' . \App\Models\UnitKegiatan::count());
        $this->command->info('- Feeds: ' . \App\Models\Feed::count());
        $this->command->info('- Payment Configurations: ' . \App\Models\PaymentConfiguration::count());
        $this->command->info('- Payment Transactions: ' . \App\Models\PaymentTransaction::count());
        $this->command->info('- Achievements: ' . \App\Models\Achievement::count());
        $this->command->info('- Activity Galleries: ' . \App\Models\ActivityGallery::count());
        $this->command->info('- Registrations: ' . \App\Models\PendaftaranAnggota::count());
        $this->command->info('- Banners: ' . \App\Models\Banner::count());
    }
}
