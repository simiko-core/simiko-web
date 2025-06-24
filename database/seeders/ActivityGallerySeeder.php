<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActivityGallery;
use App\Models\UnitKegiatan;
use Carbon\Carbon;

class ActivityGallerySeeder extends Seeder
{
    public function run(): void
    {
        $ukms = UnitKegiatan::all();

        if ($ukms->isEmpty()) {
            $this->command->warn('No UKM found. Please run UnitKegiatanSeeder first.');
            return;
        }

        $captions = [
            'Workshop Programming Fundamentals - Sesi diskusi interaktif',
            'Seminar Teknologi AI - Presentasi pembicara ahli',
            'Kompetisi Coding Marathon - Peserta sedang fokus coding',
            'Field Trip ke Perusahaan Teknologi - Kunjungan industri',
            'Pelatihan UI/UX Design - Praktik desain aplikasi',
            'Hackathon 48 Jam - Tim sedang brainstorming',
            'Gathering UKM - Acara kebersamaan anggota',
            'Pameran Karya Mahasiswa - Display project terbaik',
            'Workshop Fotografi - Sesi praktek outdoor',
            'Konser Musik Akustik - Penampilan band kampus',
            'Pertandingan Olahraga - Final turnamen antar fakultas',
            'Pendakian Gunung - Dokumentasi perjalanan',
            'Kompetisi Robot - Demonstrasi robot terbaik',
            'Debate Competition - Peserta sedang berargumentasi',
            'Community Service - Kegiatan pengabdian masyarakat',
            'Study Tour - Kunjungan ke tempat bersejarah',
            'Technical Training - Sesi hands-on praktikum',
            'Cultural Festival - Penampilan seni budaya',
            'Innovation Expo - Pameran produk inovatif',
            'Leadership Training - Sesi pengembangan diri'
        ];

        $totalCreated = 0;

        foreach ($ukms as $ukm) {
            // Create 5-8 gallery items per UKM
            $galleryCount = rand(5, 8);
            
            for ($i = 0; $i < $galleryCount; $i++) {
                ActivityGallery::create([
                    'unit_kegiatan_id' => $ukm->id,
                    'image' => 'activity_galleries/sample-activity-' . ($i + 1) . '.jpg',
                    'caption' => $captions[array_rand($captions)],
                    'created_at' => Carbon::now()->subDays(rand(1, 180)),
                    'updated_at' => Carbon::now()->subDays(rand(1, 30)),
                ]);
                
                $totalCreated++;
            }
        }

        $this->command->info('ActivityGallery seeder completed successfully!');
        $this->command->info('Created ' . $totalCreated . ' activity gallery items for ' . $ukms->count() . ' UKMs');
    }
} 