<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Achievement;
use App\Models\UnitKegiatan;
use Carbon\Carbon;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all UKMs
        $ukms = UnitKegiatan::all();

        if ($ukms->isEmpty()) {
            $this->command->warn('No UKM found. Please run UnitKegiatanSeeder first.');
            return;
        }

        // Sample achievement data
        $baseAchievementTitles = [
            'Juara 1 Kompetisi Programming Nasional',
            'Penghargaan UKM Terbaik 2023',
            'Juara 2 Lomba Fotografi Regional',
            'Best Innovation Award Tech Expo',
            'Juara 3 Kompetisi Startup Indonesia',
            'Outstanding Community Service Award',
            'Gold Medal Science Olympiad',
            'Best Design Award UI/UX Competition',
            'Champion Debate Competition',
            'Excellence in Leadership Award',
            'Top 10 National Essay Competition',
            'Best Performance Cultural Festival',
            'Innovation Award Robotics Competition',
            'Social Impact Award',
            'Academic Excellence Recognition'
        ];

        $baseAchievementDescriptions = [
            'Meraih juara pertama dalam kompetisi programming tingkat nasional dengan lebih dari 500 peserta dari seluruh Indonesia.',
            'Mendapat pengakuan sebagai Unit Kegiatan Mahasiswa terbaik tahun 2023 berdasarkan kontribusi dan prestasi.',
            'Berhasil meraih juara kedua dalam lomba fotografi tingkat regional dengan tema "Keindahan Alam Indonesia".',
            'Memperoleh penghargaan inovasi terbaik dalam pameran teknologi dengan produk yang ramah lingkungan.',
            'Meraih posisi ketiga dalam kompetisi startup nasional dengan ide bisnis yang berkelanjutan.',
            'Mendapat apresiasi atas kontribusi luar biasa dalam kegiatan pengabdian masyarakat.',
            'Meraih medali emas dalam olimpiade sains tingkat nasional kategori tim.',
            'Memperoleh penghargaan desain terbaik dalam kompetisi UI/UX tingkat universitas.',
            'Menjadi juara dalam kompetisi debat antar universitas dengan topik ekonomi digital.',
            'Mendapat pengakuan excellence in leadership dari organisasi mahasiswa tingkat nasional.',
            'Masuk 10 besar kompetisi essay nasional dengan tema "Masa Depan Indonesia".',
            'Meraih penampilan terbaik dalam festival budaya tingkat regional.',
            'Memperoleh award inovasi dalam kompetisi robotika dengan robot pembersih lingkungan.',
            'Mendapat penghargaan dampak sosial terbaik untuk program pemberdayaan masyarakat.',
            'Memperoleh pengakuan keunggulan akademik dari fakultas.'
        ];

        $totalCreated = 0;

        // Create achievements for each UKM
        foreach ($ukms as $ukm) {
            // Reset arrays for each UKM to ensure we always have options
            $achievementTitles = $baseAchievementTitles;
            $achievementDescriptions = $baseAchievementDescriptions;
            
            // Create 1-3 achievements per UKM
            $achievementCount = rand(1, 3);
            
            for ($i = 0; $i < $achievementCount && !empty($achievementTitles); $i++) {
                $titleIndex = array_rand($achievementTitles);
                
                Achievement::create([
                    'unit_kegiatan_id' => $ukm->id,
                    'title' => $achievementTitles[$titleIndex],
                    'description' => $achievementDescriptions[$titleIndex],
                    'image' => 'achievements/achievement.png',
                    'created_at' => Carbon::now()->subDays(rand(30, 365)),
                    'updated_at' => Carbon::now()->subDays(rand(1, 30)),
                ]);
                
                $totalCreated++;
                
                // Remove used title to avoid duplicates within same UKM
                unset($achievementTitles[$titleIndex]);
                unset($achievementDescriptions[$titleIndex]);
                
                // Reset array indexes
                $achievementTitles = array_values($achievementTitles);
                $achievementDescriptions = array_values($achievementDescriptions);
            }
        }

        $this->command->info('Achievement seeder completed successfully!');
        $this->command->info('Created ' . $totalCreated . ' achievements');
    }
}
