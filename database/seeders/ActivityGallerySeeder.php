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

        $this->command->info('Creating sophisticated activity gallery items...');

        $totalCreated = 0;

        foreach ($ukms as $ukm) {
            $this->command->info("Creating gallery for {$ukm->name} ({$ukm->alias})...");

            // Create realistic number of gallery items based on UKM activity level
            $galleryCount = $this->getGalleryCountByCategory($ukm->category);
            $captions = $this->getCategorySpecificCaptions($ukm->category, $ukm->alias);
            $activityTypes = $this->getActivityTypes($ukm->category);

            for ($i = 0; $i < $galleryCount; $i++) {
                $activityType = $activityTypes[array_rand($activityTypes)];
                $caption = $this->generateContextualCaption($captions, $activityType, $ukm);
                $createdDate = $this->getRealisticPhotoDate($activityType);

                ActivityGallery::create([
                    'unit_kegiatan_id' => $ukm->id,
                    'image' => $this->generateImagePath($ukm->category, $activityType, $i),
                    'caption' => $caption,
                    'created_at' => $createdDate,
                    'updated_at' => $createdDate->copy()->addHours(rand(1, 24)),
                ]);

                $totalCreated++;
            }

            // Create some special gallery items (group photos, achievements, behind-the-scenes)
            $this->createSpecialGalleryItems($ukm);
            $totalCreated += 3; // Adding 3 special items per UKM
        }

        $this->command->info('ActivityGallery seeder completed successfully!');
        $this->command->info("Created {$totalCreated} activity gallery items for {$ukms->count()} UKMs");
    }

    private function getGalleryCountByCategory($category)
    {
        return match ($category) {
            'Himpunan' => rand(8, 12), // Academic organizations are very active
            'UKM Seni' => rand(10, 15), // Arts groups have lots of visual content
            'UKM Olahraga' => rand(6, 10), // Sports have good action shots
            'UKM Teknologi' => rand(7, 11), // Tech groups document projects well
            'UKM Keilmuan' => rand(6, 9), // Academic groups moderate activity
            'UKM Kemasyarakatan' => rand(8, 12), // Community service groups are active
            'UKM Keagamaan' => rand(5, 8), // Religious groups moderate documentation
            'UKM Kewirausahaan' => rand(6, 9), // Business groups moderate activity
            'UKM Media' => rand(9, 13), // Media groups document everything
            default => rand(6, 10),
        };
    }

    private function getCategorySpecificCaptions($category, $alias)
    {
        $baseCaptions = [
            'Himpunan' => [
                'Workshop Pengembangan Software - Sesi hands-on coding dengan mentor senior',
                'Seminar Teknologi Terkini - Diskusi mendalam dengan praktisi industri',
                'Study Group Intensif - Persiapan menghadapi ujian komprehensif',
                'Kompetisi Programming - Tim {alias} menunjukkan kemampuan terbaiknya',
                'Tech Talk Series - Sharing knowledge dari alumni yang sukses',
                'Bootcamp Development - Intensive training selama 3 hari berturut-turut',
                'Industry Visit - Kunjungan ke startup dan perusahaan teknologi terkemuka',
                'Code Review Session - Peer learning untuk meningkatkan kualitas kode',
                'Hackathon 48 Jam - Marathon coding untuk menyelesaikan challenge',
                'Final Project Exhibition - Showcase karya terbaik mahasiswa semester ini'
            ],
            'UKM Seni' => [
                'Pameran Fotografi Tahunan - "Jejak Nusantara" karya anggota terbaik',
                'Konser Musik Akustik - Malam apresiasi seni dengan berbagai genre',
                'Workshop Fotografi Portrait - Teknik pencahayaan dan komposisi profesional',
                'Pertunjukan Teater - Drama musikal yang menginspirasi audience',
                'Art & Craft Exhibition - Karya seni rupa dari berbagai medium',
                'Open Stage Performance - Kesempatan emas untuk showcase bakat',
                'Behind The Scene Shooting - Proses kreatif pembuatan film pendek',
                'Digital Art Workshop - Menguasai tools design dengan Adobe Creative Suite',
                'Cultural Night - Kolaborasi seni tradisional dan kontemporer',
                'Photo Walk Session - Hunting foto di lokasi-lokasi ikonik kota'
            ],
            'UKM Olahraga' => [
                'Final Turnamen Futsal - Pertandingan sengit antar fakultas',
                'Training Camp Intensif - Persiapan fisik dan mental atlet terbaik',
                'Marathon 10K Campus Run - Event lari untuk mempromosikan hidup sehat',
                'Pelatihan Teknik Dasar - Fundamental skills untuk member baru',
                'Friendly Match - Uji coba kemampuan tim dengan klub luar',
                'Sports Festival - Kompetisi multi-cabang olahraga tingkat universitas',
                'Coaching Clinic - Sesi khusus dengan pelatih nasional berpengalaman',
                'Team Building - Membangun chemistry dan koordinasi tim yang solid',
                'Championship Victory - Merayakan pencapaian juara regional',
                'Conditioning Training - Latihan fisik untuk meningkatkan stamina'
            ],
            'UKM Teknologi' => [
                'Robot Competition - Demonstrasi robot terbaik hasil karya mahasiswa',
                'Innovation Lab - R&D project untuk solusi teknologi masa depan',
                'Arduino Workshop - Pembelajaran IoT dan embedded systems',
                'AI/ML Bootcamp - Deep learning dengan TensorFlow dan PyTorch',
                'Prototype Testing - Uji coba produk inovasi di environment nyata',
                'Tech Expo Participation - Showcase inovasi di pameran teknologi',
                'Drone Racing Championship - Kompetisi menerbangkan drone custom-built',
                '3D Printing Workshop - Rapid prototyping untuk product development',
                'Robotics Assembly - Proses pembuatan robot dari komponen awal',
                'Smart City Project - Implementasi IoT untuk solusi kota pintar'
            ],
            'UKM Kemasyarakatan' => [
                'Bakti Sosial di Panti Asuhan - Berbagi kebahagiaan dengan anak-anak',
                'Program Belajar Mengajar - Volunteer teaching di daerah terpencil',
                'Bersih-Bersih Lingkungan - Aksi peduli lingkungan bersama masyarakat',
                'Santunan Anak Yatim - Program rutin setiap bulan Ramadan',
                'Donor Darah Massal - Kerjasama dengan PMI untuk kemanusiaan',
                'Pemberdayaan UMKM - Training digital marketing untuk pelaku usaha',
                'Posyandu Balita - Pemeriksaan kesehatan gratis di desa binaan',
                'Festival Kampung - Apresiasi budaya lokal dan ekonomi kreatif',
                'Rehabilitasi Rumah - Gotong royong memperbaiki rumah warga',
                'Edukasi Lingkungan - Sosialisasi pengelolaan sampah yang baik'
            ]
        ];

        return $baseCaptions[$category] ?? [
            'Kegiatan Rutin Mingguan - Pertemuan regular untuk koordinasi program',
            'Workshop Skill Development - Pelatihan soft skills dan hard skills',
            'Seminar Motivasi - Sesi inspiratif dengan pembicara berpengalaman',
            'Team Building Activity - Mempererat hubungan antar anggota',
            'Community Service - Kontribusi nyata untuk masyarakat sekitar',
            'Study Tour Educational - Perjalanan edukatif ke tempat bersejarah',
            'Leadership Training - Pengembangan kemampuan kepemimpinan',
            'Cultural Exchange - Program pertukaran budaya dan pengetahuan',
            'Innovation Workshop - Brainstorming ide-ide kreatif dan inovatif',
            'Achievement Celebration - Perayaan pencapaian dan prestasi terbaik'
        ];
    }

    private function getActivityTypes($category)
    {
        return match ($category) {
            'Himpunan' => ['workshop', 'seminar', 'competition', 'study_group', 'bootcamp'],
            'UKM Seni' => ['exhibition', 'performance', 'workshop', 'photoshoot', 'concert'],
            'UKM Olahraga' => ['training', 'competition', 'match', 'clinic', 'championship'],
            'UKM Teknologi' => ['lab', 'competition', 'workshop', 'expo', 'testing'],
            'UKM Kemasyarakatan' => ['service', 'teaching', 'cleanup', 'donation', 'empowerment'],
            default => ['meeting', 'workshop', 'seminar', 'activity', 'event'],
        };
    }

    private function generateContextualCaption($captions, $activityType, $ukm)
    {
        $caption = $captions[array_rand($captions)];

        // Replace placeholder with actual UKM alias
        $caption = str_replace('{alias}', $ukm->alias, $caption);

        // Add contextual timing information
        $timingContext = $this->getTimingContext($activityType);
        if ($timingContext) {
            $caption .= ' ' . $timingContext;
        }

        return $caption;
    }

    private function getTimingContext($activityType)
    {
        $contexts = [
            'workshop' => ['(Hari 1 dari 3)', '(Sesi pagi)', '(Weekend intensive)', '(Batch kedua)'],
            'competition' => ['(Final round)', '(Semifinal)', '(Preliminary)', '(Grand finale)'],
            'training' => ['(Sesi rutin)', '(Persiapan kompetisi)', '(Level lanjut)', '(Fundamental)'],
            'exhibition' => ['(Opening ceremony)', '(Day 2)', '(Closing night)', '(Peak hours)'],
            'service' => ['(Minggu ke-2)', '(Program perdana)', '(Follow-up visit)', '(Evaluasi hasil)'],
        ];

        if (rand(0, 2) === 0 && isset($contexts[$activityType])) { // 33% chance to add context
            return $contexts[$activityType][array_rand($contexts[$activityType])];
        }

        return null;
    }

    private function getRealisticPhotoDate($activityType)
    {
        // Different activities happen at different times
        $baseDate = match ($activityType) {
            'workshop', 'seminar' => Carbon::now()->subDays(rand(7, 60)), // Recent learning activities
            'competition', 'championship' => Carbon::now()->subDays(rand(14, 90)), // Competitions are events
            'exhibition', 'performance' => Carbon::now()->subDays(rand(30, 120)), // Arts events
            'service', 'donation' => Carbon::now()->subDays(rand(10, 45)), // Community service
            'training' => Carbon::now()->subDays(rand(3, 30)), // Regular training
            default => Carbon::now()->subDays(rand(7, 90)),
        };

        // Add some random hours to make it more realistic
        return $baseDate->addHours(rand(8, 20)); // Activities usually during daytime
    }

    private function generateImagePath($category, $activityType, $index)
    {
        $categoryPrefix = match ($category) {
            'Himpunan' => 'tech',
            'UKM Seni' => 'arts',
            'UKM Olahraga' => 'sports',
            'UKM Teknologi' => 'innovation',
            'UKM Kemasyarakatan' => 'community',
            default => 'general',
        };

        return "activity_galleries/{$categoryPrefix}-{$activityType}-" . ($index + 1) . '.jpg';
    }

    private function createSpecialGalleryItems($ukm)
    {
        $specialItems = [
            [
                'type' => 'group_photo',
                'caption' => "Foto bersama keluarga besar {$ukm->alias} - Bonding yang tak terlupakan!",
                'days_ago' => rand(30, 90)
            ],
            [
                'type' => 'achievement',
                'caption' => "Moment bersejarah - {$ukm->alias} meraih prestasi membanggakan di tingkat nasional",
                'days_ago' => rand(60, 180)
            ],
            [
                'type' => 'behind_scenes',
                'caption' => "Behind the scenes persiapan event besar - Kerja keras yang membuahkan hasil manis",
                'days_ago' => rand(15, 45)
            ]
        ];

        foreach ($specialItems as $item) {
            $createdDate = Carbon::now()->subDays($item['days_ago']);

            ActivityGallery::create([
                'unit_kegiatan_id' => $ukm->id,
                'image' => "activity_galleries/special-{$item['type']}-{$ukm->alias}.jpg",
                'caption' => $item['caption'],
                'created_at' => $createdDate,
                'updated_at' => $createdDate->copy()->addHours(rand(1, 12)),
            ]);
        }
    }
}
