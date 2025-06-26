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
                '<div class="caption-content"><h4>Workshop Pengembangan Software</h4><p>Sesi hands-on coding dengan mentor senior 💻</p></div>',
                '<div class="caption-content"><h4>Seminar Teknologi Terkini</h4><p>Diskusi mendalam dengan praktisi industri 🚀</p></div>',
                '<div class="caption-content"><h4>Study Group Intensif</h4><p>Persiapan menghadapi ujian komprehensif 📚</p></div>',
                '<div class="caption-content"><h4>Kompetisi Programming</h4><p>Tim {alias} menunjukkan kemampuan terbaiknya 🏆</p></div>',
                '<div class="caption-content"><h4>Tech Talk Series</h4><p>Sharing knowledge dari alumni yang sukses ⭐</p></div>',
                '<div class="caption-content"><h4>Bootcamp Development</h4><p>Intensive training selama 3 hari berturut-turut 🛠️</p></div>',
                '<div class="caption-content"><h4>Industry Visit</h4><p>Kunjungan ke startup dan perusahaan teknologi terkemuka 🏢</p></div>',
                '<div class="caption-content"><h4>Code Review Session</h4><p>Peer learning untuk meningkatkan kualitas kode 🔍</p></div>',
                '<div class="caption-content"><h4>Hackathon 48 Jam</h4><p>Marathon coding untuk menyelesaikan challenge ⚡</p></div>',
                '<div class="caption-content"><h4>Final Project Exhibition</h4><p>Showcase karya terbaik mahasiswa semester ini 🎯</p></div>'
            ],
            'UKM Seni' => [
                '<div class="caption-content"><h4>Pameran Fotografi Tahunan</h4><p>"Jejak Nusantara" karya anggota terbaik 📷</p></div>',
                '<div class="caption-content"><h4>Konser Musik Akustik</h4><p>Malam apresiasi seni dengan berbagai genre 🎸</p></div>',
                '<div class="caption-content"><h4>Workshop Fotografi Portrait</h4><p>Teknik pencahayaan dan komposisi profesional 💡</p></div>',
                '<div class="caption-content"><h4>Pertunjukan Teater</h4><p>Drama musikal yang menginspirasi audience 🎭</p></div>',
                '<div class="caption-content"><h4>Art & Craft Exhibition</h4><p>Karya seni rupa dari berbagai medium 🎨</p></div>',
                '<div class="caption-content"><h4>Open Stage Performance</h4><p>Kesempatan emas untuk showcase bakat ⭐</p></div>',
                '<div class="caption-content"><h4>Behind The Scene Shooting</h4><p>Proses kreatif pembuatan film pendek 🎬</p></div>',
                '<div class="caption-content"><h4>Digital Art Workshop</h4><p>Menguasai tools design dengan Adobe Creative Suite 🖥️</p></div>',
                '<div class="caption-content"><h4>Cultural Night</h4><p>Kolaborasi seni tradisional dan kontemporer 🌟</p></div>',
                '<div class="caption-content"><h4>Photo Walk Session</h4><p>Hunting foto di lokasi-lokasi ikonik kota 📸</p></div>'
            ],
            'UKM Olahraga' => [
                '<div class="caption-content"><h4>Final Turnamen Futsal</h4><p>Pertandingan sengit antar fakultas ⚽</p></div>',
                '<div class="caption-content"><h4>Training Camp Intensif</h4><p>Persiapan fisik dan mental atlet terbaik 💪</p></div>',
                '<div class="caption-content"><h4>Marathon 10K Campus Run</h4><p>Event lari untuk mempromosikan hidup sehat 🏃‍♂️</p></div>',
                '<div class="caption-content"><h4>Pelatihan Teknik Dasar</h4><p>Fundamental skills untuk member baru 🎯</p></div>',
                '<div class="caption-content"><h4>Friendly Match</h4><p>Uji coba kemampuan tim dengan klub luar 🤝</p></div>',
                '<div class="caption-content"><h4>Sports Festival</h4><p>Kompetisi multi-cabang olahraga tingkat universitas 🏆</p></div>',
                '<div class="caption-content"><h4>Coaching Clinic</h4><p>Sesi khusus dengan pelatih nasional berpengalaman 👨‍🏫</p></div>',
                '<div class="caption-content"><h4>Team Building</h4><p>Membangun chemistry dan koordinasi tim yang solid 🤜🤛</p></div>',
                '<div class="caption-content"><h4>Championship Victory</h4><p>Merayakan pencapaian juara regional 🥇</p></div>',
                '<div class="caption-content"><h4>Conditioning Training</h4><p>Latihan fisik untuk meningkatkan stamina ⚡</p></div>'
            ],
            'UKM Teknologi' => [
                '<div class="caption-content"><h4>Robot Competition</h4><p>Demonstrasi robot terbaik hasil karya mahasiswa 🤖</p></div>',
                '<div class="caption-content"><h4>Innovation Lab</h4><p>R&D project untuk solusi teknologi masa depan 🔬</p></div>',
                '<div class="caption-content"><h4>Arduino Workshop</h4><p>Pembelajaran IoT dan embedded systems 🔧</p></div>',
                '<div class="caption-content"><h4>AI/ML Bootcamp</h4><p>Deep learning dengan TensorFlow dan PyTorch 🧠</p></div>',
                '<div class="caption-content"><h4>Prototype Testing</h4><p>Uji coba produk inovasi di environment nyata ⚗️</p></div>',
                '<div class="caption-content"><h4>Tech Expo Participation</h4><p>Showcase inovasi di pameran teknologi 🚀</p></div>',
                '<div class="caption-content"><h4>Drone Racing Championship</h4><p>Kompetisi menerbangkan drone custom-built 🚁</p></div>',
                '<div class="caption-content"><h4>3D Printing Workshop</h4><p>Rapid prototyping untuk product development 🖨️</p></div>',
                '<div class="caption-content"><h4>Robotics Assembly</h4><p>Proses pembuatan robot dari komponen awal ⚙️</p></div>',
                '<div class="caption-content"><h4>Smart City Project</h4><p>Implementasi IoT untuk solusi kota pintar 🏙️</p></div>'
            ],
            'UKM Kemasyarakatan' => [
                '<div class="caption-content"><h4>Bakti Sosial di Panti Asuhan</h4><p>Berbagi kebahagiaan dengan anak-anak ❤️</p></div>',
                '<div class="caption-content"><h4>Program Belajar Mengajar</h4><p>Volunteer teaching di daerah terpencil 📚</p></div>',
                '<div class="caption-content"><h4>Bersih-Bersih Lingkungan</h4><p>Aksi peduli lingkungan bersama masyarakat 🌍</p></div>',
                '<div class="caption-content"><h4>Santunan Anak Yatim</h4><p>Program rutin setiap bulan Ramadan 🤲</p></div>',
                '<div class="caption-content"><h4>Donor Darah Massal</h4><p>Kerjasama dengan PMI untuk kemanusiaan 🩸</p></div>',
                '<div class="caption-content"><h4>Pemberdayaan UMKM</h4><p>Training digital marketing untuk pelaku usaha 💼</p></div>',
                '<div class="caption-content"><h4>Posyandu Balita</h4><p>Pemeriksaan kesehatan gratis di desa binaan 👶</p></div>',
                '<div class="caption-content"><h4>Festival Kampung</h4><p>Apresiasi budaya lokal dan ekonomi kreatif 🎪</p></div>',
                '<div class="caption-content"><h4>Rehabilitasi Rumah</h4><p>Gotong royong memperbaiki rumah warga 🏠</p></div>',
                '<div class="caption-content"><h4>Edukasi Lingkungan</h4><p>Sosialisasi pengelolaan sampah yang baik ♻️</p></div>'
            ]
        ];

        return $baseCaptions[$category] ?? [
            '<div class="caption-content"><h4>Kegiatan Rutin Mingguan</h4><p>Pertemuan regular untuk koordinasi program 📅</p></div>',
            '<div class="caption-content"><h4>Workshop Skill Development</h4><p>Pelatihan soft skills dan hard skills 💪</p></div>',
            '<div class="caption-content"><h4>Seminar Motivasi</h4><p>Sesi inspiratif dengan pembicara berpengalaman ✨</p></div>',
            '<div class="caption-content"><h4>Team Building Activity</h4><p>Mempererat hubungan antar anggota 🤝</p></div>',
            '<div class="caption-content"><h4>Community Service</h4><p>Kontribusi nyata untuk masyarakat sekitar ❤️</p></div>',
            '<div class="caption-content"><h4>Study Tour Educational</h4><p>Perjalanan edukatif ke tempat bersejarah 🏛️</p></div>',
            '<div class="caption-content"><h4>Leadership Training</h4><p>Pengembangan kemampuan kepemimpinan 👑</p></div>',
            '<div class="caption-content"><h4>Cultural Exchange</h4><p>Program pertukaran budaya dan pengetahuan 🌍</p></div>',
            '<div class="caption-content"><h4>Innovation Workshop</h4><p>Brainstorming ide-ide kreatif dan inovatif 💡</p></div>',
            '<div class="caption-content"><h4>Achievement Celebration</h4><p>Perayaan pencapaian dan prestasi terbaik 🏆</p></div>'
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
                'caption' => "<div class='caption-content'><h4>Foto Bersama Keluarga Besar {$ukm->alias}</h4><p>Bonding yang tak terlupakan! 🤗</p></div>",
                'days_ago' => rand(30, 90)
            ],
            [
                'type' => 'achievement',
                'caption' => "<div class='caption-content'><h4>Moment Bersejarah</h4><p>{$ukm->alias} meraih prestasi membanggakan di tingkat nasional 🏆</p></div>",
                'days_ago' => rand(60, 180)
            ],
            [
                'type' => 'behind_scenes',
                'caption' => "<div class='caption-content'><h4>Behind The Scenes</h4><p>Persiapan event besar - Kerja keras yang membuahkan hasil manis 💪</p></div>",
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
