<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Feed;
use App\Models\UnitKegiatan;
use Carbon\Carbon;

class FeedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ukms = UnitKegiatan::all();

        if ($ukms->isEmpty()) {
            $this->command->warn('No UKM found. Please run UnitKegiatanSeeder first.');
            return;
        }

        // Categorized content by UKM type
        $contentByType = [
            'HMIF' => [
                'posts' => [
                    ['title' => 'Tips Belajar Algoritma dan Struktur Data', 'content' => 'Pelajari konsep dasar algoritma dengan pendekatan yang mudah dipahami. Mulai dari sorting, searching, hingga dynamic programming.'],
                    ['title' => 'Roadmap Menjadi Full Stack Developer', 'content' => 'Panduan lengkap untuk menjadi full stack developer dari zero to hero. Mulai dari HTML, CSS, JavaScript hingga framework modern.'],
                    ['title' => 'Open Source Project untuk Pemula', 'content' => 'Rekomendasi project open source yang cocok untuk pemula. Berkontribusi ke open source sambil belajar teknologi baru.'],
                ],
                'events' => [
                    ['title' => 'Hackathon 48 Jam: Smart Campus Solution', 'content' => 'Kompetisi pengembangan aplikasi untuk solusi smart campus. Total hadiah 50 juta rupiah untuk 3 juara terbaik.'],
                    ['title' => 'Workshop Machine Learning dengan Python', 'content' => 'Pelatihan intensif machine learning menggunakan Python dan library scikit-learn. Cocok untuk pemula hingga intermediate.'],
                    ['title' => 'Seminar "Future of Web Development"', 'content' => 'Seminar tentang tren teknologi web development masa depan dengan pembicara dari perusahaan teknologi terkemuka.'],
                ]
            ],
            'HMTE' => [
                'posts' => [
                    ['title' => 'Pengenalan Sistem Kontrol Otomatis', 'content' => 'Dasar-dasar sistem kontrol dalam teknik elektro. Memahami PID controller dan aplikasinya dalam industri.'],
                    ['title' => 'Energi Terbarukan: Solar Panel dan Wind Turbine', 'content' => 'Eksplorasi teknologi energi terbarukan yang sedang berkembang pesat. Analisis efisiensi dan implementasi.'],
                    ['title' => 'Internet of Things (IoT) dalam Smart Home', 'content' => 'Implementasi IoT untuk rumah pintar menggunakan sensor dan microcontroller. Project praktis yang bisa dicoba.'],
                ],
                'events' => [
                    ['title' => 'Kompetisi Robot Line Follower Nasional', 'content' => 'Kompetisi robot line follower tingkat nasional dengan peserta dari seluruh Indonesia. Kategori analog dan digital tersedia.'],
                    ['title' => 'Workshop PCB Design dengan Eagle', 'content' => 'Pelatihan desain PCB menggunakan software Eagle. Dari schematic hingga layout PCB siap produksi.'],
                    ['title' => 'Expo Teknologi Elektro 2024', 'content' => 'Pameran teknologi elektro terbaru dengan demo langsung dari mahasiswa dan dosen. Open untuk umum.'],
                ]
            ],
            'default' => [
                'posts' => [
                    ['title' => 'Pengumuman Kegiatan Rutin Mingguan', 'content' => 'Informasi jadwal kegiatan rutin mingguan untuk semua anggota. Jangan lupa untuk hadir tepat waktu.'],
                    ['title' => 'Tips Manajemen Waktu untuk Mahasiswa', 'content' => 'Strategi efektif mengelola waktu antara kuliah, organisasi, dan kehidupan pribadi untuk mencapai work-life balance.'],
                    ['title' => 'Sharing Session: Pengalaman Magang', 'content' => 'Alumni berbagi pengalaman magang di berbagai perusahaan. Tips dan trik untuk mendapatkan tempat magang terbaik.'],
                ],
                'events' => [
                    ['title' => 'Gathering Anggota Semester Ganjil', 'content' => 'Acara gathering untuk mempererat hubungan antar anggota. Ada games, doorprize, dan makan bersama.'],
                    ['title' => 'Workshop Soft Skills Development', 'content' => 'Pelatihan pengembangan soft skills meliputi public speaking, leadership, dan teamwork untuk mahasiswa.'],
                    ['title' => 'Community Service: Bakti Sosial', 'content' => 'Kegiatan pengabdian masyarakat berupa bakti sosial ke daerah terpencil. Mari bersama membantu sesama.'],
                ]
            ]
        ];

        $locations = [
            'Auditorium Utama Kampus',
            'Gedung Fakultas Teknik Lt. 3',
            'Lab Komputer A-201',
            'Ruang Seminar B-301',
            'Hall Gedung Rektorat',
            'Aula Student Center',
            'Perpustakaan Pusat Lt. 2',
            'Online via Zoom Meeting',
            'Google Meet Room',
            'Lapangan Basket Kampus',
            'Gedung Serbaguna Lt. 1'
        ];

        $paymentMethods = [
            [
                ['method' => 'Bank Transfer BCA', 'account_number' => '1234567890', 'account_name' => 'Bendahara UKM'],
                ['method' => 'Dana', 'account_number' => '081234567890', 'account_name' => 'Bendahara UKM'],
            ],
            [
                ['method' => 'Bank Transfer Mandiri', 'account_number' => '9876543210', 'account_name' => 'Kas UKM'],
                ['method' => 'GoPay', 'account_number' => '085678901234', 'account_name' => 'Kas UKM'],
            ],
            [
                ['method' => 'Bank Transfer BNI', 'account_number' => '5555666677', 'account_name' => 'Sekretaris UKM'],
                ['method' => 'OVO', 'account_number' => '087890123456', 'account_name' => 'Sekretaris UKM'],
                ['method' => 'ShopeePay', 'account_number' => '089012345678', 'account_name' => 'Sekretaris UKM'],
            ]
        ];

        $totalPosts = 0;
        $totalEvents = 0;

        foreach ($ukms as $ukm) {
            // Get content based on UKM alias or use default
            $content = $contentByType[$ukm->alias] ?? $contentByType['default'];
            
            // Create 4-6 posts per UKM
            $postCount = rand(4, 6);
            for ($i = 0; $i < $postCount; $i++) {
                $postData = $content['posts'][array_rand($content['posts'])];
                
                Feed::create([
                    'unit_kegiatan_id' => $ukm->id,
                    'type' => 'post',
                    'title' => $postData['title'],
                    'content' => $postData['content'],
                    'image' => rand(0, 1) ? 'feeds/sample-post-' . rand(1, 5) . '.jpg' : null,
                    'created_at' => Carbon::now()->subDays(rand(1, 45)),
                    'updated_at' => Carbon::now()->subDays(rand(1, 30)),
                ]);
                $totalPosts++;
            }

            // Create 3-4 events per UKM
            $eventCount = rand(3, 4);
            for ($i = 0; $i < $eventCount; $i++) {
                $eventData = $content['events'][array_rand($content['events'])];
                $isOnline = rand(0, 3) === 0; // 25% chance online
                $isPaid = rand(0, 2) === 0;   // 33% chance paid
                $eventDate = Carbon::now()->addDays(rand(7, 90));
                
                Feed::create([
                    'unit_kegiatan_id' => $ukm->id,
                    'type' => 'event',
                    'title' => $eventData['title'],
                    'content' => $eventData['content'],
                    'image' => rand(0, 1) ? 'feeds/sample-event-' . rand(1, 5) . '.jpg' : null,
                    'event_date' => $eventDate,
                    'event_type' => $isOnline ? 'online' : 'offline',
                    'location' => $isOnline ? 
                        ['Online via Zoom Meeting', 'Google Meet Room', 'Microsoft Teams'][array_rand(['Online via Zoom Meeting', 'Google Meet Room', 'Microsoft Teams'])] : 
                        $locations[array_rand($locations)],
                    'is_paid' => $isPaid,
                    'price' => $isPaid ? rand(15000, 150000) : null,
                    'payment_methods' => $isPaid ? $paymentMethods[array_rand($paymentMethods)] : null,
                    'created_at' => Carbon::now()->subDays(rand(1, 21)),
                    'updated_at' => Carbon::now()->subDays(rand(1, 14)),
                ]);
                $totalEvents++;
            }
        }

        // Create some additional recent posts
        $recentUkms = $ukms->random(min(3, $ukms->count()));
        foreach ($recentUkms as $ukm) {
            $content = $contentByType[$ukm->alias] ?? $contentByType['default'];
            $postData = $content['posts'][array_rand($content['posts'])];
            
            Feed::create([
                'unit_kegiatan_id' => $ukm->id,
                'type' => 'post',
                'title' => '[HOT] ' . $postData['title'],
                'content' => $postData['content'] . ' Update terbaru dengan informasi lengkap dan terkini.',
                'image' => 'feeds/hot-post-' . rand(1, 3) . '.jpg',
                'created_at' => Carbon::now()->subHours(rand(1, 48)),
                'updated_at' => Carbon::now()->subHours(rand(1, 24)),
            ]);
            $totalPosts++;
        }

        $this->command->info('Feed seeder completed successfully!');
        $this->command->info('Created ' . $totalPosts . ' posts and ' . $totalEvents . ' events');
        $this->command->info('Total feeds: ' . Feed::count());
    }
}
