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
        $this->command->info('🚀 Starting comprehensive database seeding...');
        $this->command->info('This will create realistic data for the entire Simiko system.');
        $this->command->newLine();

        // Run seeders in proper order
        $this->command->info('📋 Seeding Phase 1: Core Setup');
        $this->call([
            ShieldSeeder::class,           // Setup roles and permissions first
            UnitKegiatanSeeder::class,     // Create UKMs
            UserSeeder::class,             // Create users (including UKM admins)
        ]);

        $this->command->info('📋 Seeding Phase 2: Organizational Structure');
        $this->call([
            UnitKegiatanProfileSeeder::class, // Create UKM profiles
            PendaftaranAnggotaSeeder::class,  // Create registrations
        ]);

        $this->command->info('📋 Seeding Phase 3: Content & Activities');
        $this->call([
            FeedSeeder::class,             // Create feeds (posts and events) with payment configs
            ActivityGallerySeeder::class,  // Create activity galleries
            AchievementSeeder::class,      // Create achievements
        ]);

        $this->command->info('📋 Seeding Phase 4: Financial System');
        $this->call([
            PaymentSeeder::class,          // Create comprehensive payment transactions
        ]);

        $this->command->info('📋 Seeding Phase 5: Presentation & Marketing');
        $this->call([
            BannerSeeder::class,           // Create strategic banners
        ]);

        $this->command->newLine();
        $this->showDatabaseSummary();
        $this->showRecommendations();
    }

    private function showDatabaseSummary()
    {
        $this->command->info('🎉 Database seeding completed successfully!');
        $this->command->newLine();

        // Core Data Summary
        $this->command->info('📊 COMPREHENSIVE DATABASE SUMMARY:');
        $this->command->info('═══════════════════════════════════════');

        // User Management
        $totalUsers = User::count();
        $adminUsers = User::whereHas('admin')->count();
        $regularUsers = $totalUsers - $adminUsers - 1; // Exclude super admin

        $this->command->info('👥 USER MANAGEMENT:');
        $this->command->info("   • Total Users: {$totalUsers}");
        $this->command->info("   • Super Admin: 1");
        $this->command->info("   • UKM Admins: {$adminUsers}");
        $this->command->info("   • Regular Users: {$regularUsers}");

        // Organization Data
        $totalUkms = \App\Models\UnitKegiatan::count();
        $ukmByCategory = \App\Models\UnitKegiatan::select('category')
            ->selectRaw('count(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category');

        $this->command->info('🏛️ ORGANIZATIONS:');
        $this->command->info("   • Total UKMs: {$totalUkms}");
        foreach ($ukmByCategory as $category => $count) {
            $this->command->info("   • {$category}: {$count}");
        }

        // Content Statistics
        $totalFeeds = \App\Models\Feed::count();
        $totalPosts = \App\Models\Feed::where('type', 'post')->count();
        $totalEvents = \App\Models\Feed::where('type', 'event')->count();
        $paidEvents = \App\Models\Feed::where('type', 'event')->where('is_paid', true)->count();

        $this->command->info('📱 CONTENT & ACTIVITIES:');
        $this->command->info("   • Total Content: {$totalFeeds}");
        $this->command->info("   • Posts: {$totalPosts}");
        $this->command->info("   • Events: {$totalEvents}");
        $this->command->info("   • Paid Events: {$paidEvents}");

        // Gallery & Achievements
        $totalGallery = \App\Models\ActivityGallery::count();
        $totalAchievements = \App\Models\Achievement::count();

        $this->command->info('🎨 VISUAL CONTENT:');
        $this->command->info("   • Activity Photos: {$totalGallery}");
        $this->command->info("   • Achievements: {$totalAchievements}");

        // Payment System
        $totalConfigs = \App\Models\PaymentConfiguration::count();
        $totalTransactions = \App\Models\PaymentTransaction::count();
        $paidTransactions = \App\Models\PaymentTransaction::where('status', 'paid')->count();
        $pendingTransactions = \App\Models\PaymentTransaction::where('status', 'pending')->count();
        $totalRevenue = \App\Models\PaymentTransaction::where('status', 'paid')->sum('amount');

        $this->command->info('💰 PAYMENT SYSTEM:');
        $this->command->info("   • Payment Configurations: {$totalConfigs}");
        $this->command->info("   • Total Transactions: {$totalTransactions}");
        $this->command->info("   • Paid Transactions: {$paidTransactions}");
        $this->command->info("   • Pending Transactions: {$pendingTransactions}");
        $this->command->info("   • Total Revenue: Rp " . number_format($totalRevenue, 0, ',', '.'));

        // Membership Data
        $totalRegistrations = \App\Models\PendaftaranAnggota::count();
        $acceptedMembers = \App\Models\PendaftaranAnggota::where('status', 'accepted')->count();
        $pendingRegistrations = \App\Models\PendaftaranAnggota::where('status', 'pending')->count();

        $this->command->info('📝 MEMBERSHIP:');
        $this->command->info("   • Total Applications: {$totalRegistrations}");
        $this->command->info("   • Accepted Members: {$acceptedMembers}");
        $this->command->info("   • Pending Applications: {$pendingRegistrations}");

        // Marketing
        $totalBanners = \App\Models\Banner::count();
        $activeBanners = \App\Models\Banner::where('active', true)->count();

        $this->command->info('📢 MARKETING:');
        $this->command->info("   • Total Banners: {$totalBanners}");
        $this->command->info("   • Active Banners: {$activeBanners}");
    }

    private function showRecommendations()
    {
        $this->command->newLine();
        $this->command->info('💡 NEXT STEPS & RECOMMENDATIONS:');
        $this->command->info('═══════════════════════════════════════');

        $this->command->info('🔐 AUTHENTICATION:');
        $this->command->info('   • Super Admin: superadmin@gmail.com / superadmin');
        $this->command->info('   • UKM Admin: [ukm-alias]@gmail.com / [ukm-alias]');
        $this->command->info('   • Regular User: Use any generated user email / password123');

        $this->command->newLine();
        $this->command->info('🌐 ACCESS POINTS:');
        $this->command->info('   • Admin Panel: /admin (Super Admin)');
        $this->command->info('   • UKM Panel: /admin-panel (UKM Admins)');
        $this->command->info('   • API Documentation: /api/documentation');
        $this->command->info('   • API Base URL: /api');

        $this->command->newLine();
        $this->command->info('📊 DATA QUALITY FEATURES:');
        $this->command->info('   ✅ Realistic payment transactions with multiple states');
        $this->command->info('   ✅ Category-specific content for each UKM type');
        $this->command->info('   ✅ Sophisticated achievement system with levels');
        $this->command->info('   ✅ Strategic banner selection algorithm');
        $this->command->info('   ✅ Activity galleries with contextual captions');
        $this->command->info('   ✅ Event scheduling with realistic dates');
        $this->command->info('   ✅ Payment configurations with custom fields');
        $this->command->info('   ✅ Multi-status transaction scenarios');

        $this->command->newLine();
        $this->command->info('🔧 DEVELOPMENT TOOLS:');
        $this->command->info('   • Run API tests: php artisan test');
        $this->command->info('   • Generate docs: php artisan l5-swagger:generate');
        $this->command->info('   • Clear cache: php artisan optimize:clear');
        $this->command->info('   • Reset database: php artisan migrate:fresh --seed');

        $this->command->newLine();
        $this->command->alert('🎯 Your Simiko application is now ready with comprehensive, realistic data!');
        $this->command->info('The seeded data includes sophisticated patterns that simulate real-world usage.');
    }
}
