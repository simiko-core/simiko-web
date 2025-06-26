<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Feed;
use App\Models\UnitKegiatan;
use App\Models\PaymentConfiguration;
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

        // Enhanced content templates by UKM category
        $contentTemplates = $this->getContentTemplates();

        // Event scheduling patterns
        $eventScheduling = $this->getEventSchedulingPatterns();

        // Image pools for different content types
        $imagePool = $this->getImagePool();

        $totalPosts = 0;
        $totalEvents = 0;

        foreach ($ukms as $ukm) {
            $this->command->info("Creating feeds for {$ukm->name} ({$ukm->alias})...");

            // Get content based on UKM category
            $content = $contentTemplates[$ukm->category] ?? $contentTemplates['default'];

            // Create posts (5-8 per UKM with variety)
            $postCount = rand(5, 8);
            $totalPosts += $this->createPosts($ukm, $content['posts'], $postCount, $imagePool['posts']);

            // Create events (3-6 per UKM with realistic scheduling)
            $eventCount = rand(3, 6);
            $totalEvents += $this->createEvents($ukm, $content['events'], $eventCount, $eventScheduling, $imagePool['events']);

            // Create special content (announcements, featured posts)
            $this->createSpecialContent($ukm, $content, $imagePool['posts']);
        }

        // Create cross-UKM collaborative events
        $this->createCollaborativeEvents($ukms, $eventScheduling, $imagePool['events']);

        // Create trending/viral posts
        $this->createTrendingPosts($ukms, $imagePool['posts']);

        $this->command->info('Feed seeder completed successfully!');
        $this->command->info("Created {$totalPosts} posts and {$totalEvents} events");
        $this->command->info('Total feeds: ' . Feed::count());
    }

    private function createPosts($ukm, $postTemplates, $count, $imagePool)
    {
        $created = 0;
        $postTypes = ['announcement', 'tutorial', 'news', 'achievement', 'discussion'];

        for ($i = 0; $i < $count; $i++) {
            $template = $postTemplates[array_rand($postTemplates)];
            $postType = $postTypes[array_rand($postTypes)];

            // Add type-specific prefixes and content modifications
            $title = $this->enhanceTitle($template['title'], $postType);
            $content = $this->enhanceContent($template['content'], $postType, $ukm);

            $createdAt = $this->getRandomPostDate();

            Feed::create([
                'unit_kegiatan_id' => $ukm->id,
                'type' => 'post',
                'title' => $title,
                'content' => $content,
                'image' => $this->selectImage($imagePool, $postType),
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addHours(rand(1, 48)),
            ]);
            $created++;
        }

        return $created;
    }

    private function createEvents($ukm, $eventTemplates, $count, $scheduling, $imagePool)
    {
        $created = 0;
        $eventTypes = ['workshop', 'competition', 'seminar', 'gathering', 'training'];

        for ($i = 0; $i < $count; $i++) {
            $template = $eventTemplates[array_rand($eventTemplates)];
            $eventType = $eventTypes[array_rand($eventTypes)];
            $schedule = $scheduling[$eventType][array_rand($scheduling[$eventType])];

            $isPaid = $schedule['paid_probability'] > rand(0, 100);
            $isOnline = $schedule['online_probability'] > rand(0, 100);

            $eventDate = $this->calculateEventDate($schedule);
            $createdAt = $this->getRandomPostDate($eventDate);

            $event = [
                'unit_kegiatan_id' => $ukm->id,
                'type' => 'event',
                'title' => $this->enhanceTitle($template['title'], $eventType),
                'content' => $this->enhanceEventContent($template['content'], $eventType, $ukm, $isPaid),
                'image' => $this->selectImage($imagePool, $eventType),
                'event_date' => $eventDate,
                'event_type' => $isOnline ? 'online' : 'offline',
                'location' => $this->getEventLocation($isOnline, $eventType),
                'is_paid' => $isPaid,
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addHours(rand(1, 72)),
            ];

            // Create payment configuration for paid events
            if ($isPaid) {
                $paymentConfig = $this->createEventPaymentConfiguration($ukm, $eventType, $template['title']);
                $event['payment_configuration_id'] = $paymentConfig->id;
            }

            Feed::create($event);
            $created++;
        }

        return $created;
    }

    private function createSpecialContent($ukm, $content, $imagePool)
    {
        // Create urgent announcements
        if (rand(0, 2) === 0 && !empty($content['announcements'])) {
            $createdAt = Carbon::now()->subHours(rand(1, 24));
            $announcement = $content['announcements'][array_rand($content['announcements'])];

            Feed::create([
                'unit_kegiatan_id' => $ukm->id,
                'type' => 'post',
                'title' => "[URGENT] {$announcement['title']}",
                'content' => "⚠️ PENGUMUMAN PENTING ⚠️\n\n" . $announcement['content'],
                'image' => $this->selectImage($imagePool),
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addHours(rand(1, 12)),
            ]);
        }

        // Create recruitment posts
        if (rand(0, 3) === 0) {
            $createdAt = Carbon::now()->subDays(rand(1, 7));

            Feed::create([
                'unit_kegiatan_id' => $ukm->id,
                'type' => 'post',
                'title' => "🔥 OPEN RECRUITMENT {$ukm->alias} 2024/2025",
                'content' => "Kami membuka kesempatan untuk bergabung dengan {$ukm->name}! \n\n📝 Persyaratan:\n- Mahasiswa aktif\n- Berkomitmen tinggi\n- Siap berkontribusi\n\n📅 Pendaftaran: " . Carbon::now()->addDays(rand(5, 15))->format('d M Y') . "\n\n#OpenRecruitment #{$ukm->alias}",
                'image' => $this->selectImage($imagePool),
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDays(rand(1, 5)),
            ]);
        }
    }

    private function createCollaborativeEvents($ukms, $scheduling, $imagePool)
    {
        $collaborativeEvents = [
            [
                'title' => 'Festival Teknologi Kampus 2024',
                'content' => 'Festival teknologi terbesar di kampus dengan partisipasi dari berbagai UKM teknologi. Pameran inovasi, kompetisi, dan demo teknologi terbaru.',
                'participants' => ['HMIF', 'HMTE', 'UKM Robot'],
                'type' => 'competition'
            ],
            [
                'title' => 'Expo Kreativitas Mahasiswa',
                'content' => 'Pameran kreativitas mahasiswa dari berbagai bidang seni dan teknologi. Showcase karya terbaik dari seluruh UKM di kampus.',
                'participants' => ['UKM Foto', 'UKM Musik', 'HMIF'],
                'type' => 'gathering'
            ],
            [
                'title' => 'Seminar Nasional: "Masa Depan Teknologi Indonesia"',
                'content' => 'Seminar nasional dengan pembicara dari industri teknologi terkemuka. Membahas tren teknologi masa depan dan peluang karir.',
                'participants' => ['HMIF', 'HMTE', 'HMTM'],
                'type' => 'seminar'
            ]
        ];

        foreach ($collaborativeEvents as $eventData) {
            $organizer = $ukms->whereIn('alias', $eventData['participants'])->random();
            $schedule = $scheduling[$eventData['type']][array_rand($scheduling[$eventData['type']])];
            $createdAt = Carbon::now()->subDays(rand(5, 20));

            Feed::create([
                'unit_kegiatan_id' => $organizer->id,
                'type' => 'event',
                'title' => $eventData['title'],
                'content' => $eventData['content'] . "\n\n🤝 Kolaborasi: " . implode(', ', $eventData['participants']),
                'image' => $this->selectImage($imagePool),
                'event_date' => $this->calculateEventDate($schedule),
                'event_type' => 'offline',
                'location' => 'Auditorium Utama Kampus',
                'is_paid' => $schedule['paid_probability'] > 50,
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDays(rand(1, 10)),
            ]);
        }
    }

    private function createTrendingPosts($ukms, $imagePool)
    {
        $trendingTopics = [
            ['title' => 'Tips Sukses Magang di Startup', 'hashtags' => '#MagangStartup #TipsKarir #Startup'],
            ['title' => 'Pengalaman Ikut Lomba Internasional', 'hashtags' => '#LombaInternasional #Achievement #Proud'],
            ['title' => 'Review Workshop AI & Machine Learning', 'hashtags' => '#MachineLearning #AI #Workshop'],
            ['title' => 'Behind The Scene Project Akhir', 'hashtags' => '#ProjectAkhir #BTS #MahasiswaLife'],
        ];

        foreach ($trendingTopics as $topic) {
            $ukm = $ukms->random();
            $createdAt = Carbon::now()->subHours(rand(2, 12));

            Feed::create([
                'unit_kegiatan_id' => $ukm->id,
                'type' => 'post',
                'title' => "🔥 VIRAL: {$topic['title']}",
                'content' => "Thread viral yang lagi happening! Banyak yang request untuk dibahas lebih detail.\n\n{$topic['hashtags']}\n\n#Viral #Trending #{$ukm->alias}",
                'image' => $this->selectImage($imagePool),
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addHours(rand(1, 6)),
            ]);
        }
    }

    private function createEventPaymentConfiguration($ukm, $eventType, $eventTitle)
    {
        $amounts = [
            'workshop' => [25000, 50000, 75000, 100000],
            'competition' => [50000, 75000, 100000, 150000],
            'seminar' => [15000, 25000, 35000],
            'training' => [75000, 100000, 125000],
            'gathering' => [25000, 35000, 50000]
        ];

        $amount = $amounts[$eventType][array_rand($amounts[$eventType])];

        return PaymentConfiguration::create([
            'unit_kegiatan_id' => $ukm->id,
            'name' => $eventTitle . ' - Registration Fee',
            'description' => "Biaya pendaftaran untuk mengikuti {$eventTitle}",
            'amount' => $amount,
            'currency' => 'IDR',
            'is_active' => true,
            'payment_methods' => $this->getPaymentMethods($ukm),
            'custom_fields' => $this->getEventCustomFields($eventType),
            'settings' => [
                'due_date' => Carbon::now()->addDays(rand(7, 21))->format('Y-m-d'),
                'max_participants' => $this->getMaxParticipants($eventType),
                'terms_conditions' => "Pembayaran tidak dapat dikembalikan. Harap pastikan kehadiran sebelum mendaftar.",
                'notes' => "Payment configuration for {$eventTitle}"
            ]
        ]);
    }

    // Helper methods for content enhancement and configuration

    private function enhanceTitle($baseTitle, $type)
    {
        $prefixes = [
            'announcement' => ['📢', '[INFO]', '⚠️'],
            'tutorial' => ['📚', '[TUTORIAL]', '🎓'],
            'news' => ['📰', '[NEWS]', '🔥'],
            'achievement' => ['🏆', '[ACHIEVEMENT]', '⭐'],
            'discussion' => ['💭', '[DISKUSI]', '🗣️'],
            'workshop' => ['🛠️', '[WORKSHOP]', '📚'],
            'competition' => ['🏁', '[KOMPETISI]', '🏆'],
            'seminar' => ['🎤', '[SEMINAR]', '📊'],
            'gathering' => ['🤝', '[GATHERING]', '🎉'],
            'training' => ['💪', '[TRAINING]', '📈']
        ];

        $prefix = $prefixes[$type][array_rand($prefixes[$type])];
        return "{$prefix} {$baseTitle}";
    }

    private function enhanceContent($baseContent, $type, $ukm)
    {
        $enhancements = [
            'announcement' => "\n\n📍 Info lebih lanjut: Contact person {$ukm->alias}\n#Announcement #{$ukm->alias}",
            'tutorial' => "\n\n💡 Tips: Bookmark post ini untuk referensi!\n#Tutorial #Learning #{$ukm->alias}",
            'news' => "\n\n🔔 Stay tuned untuk update selanjutnya!\n#News #Update #{$ukm->alias}",
            'achievement' => "\n\n🎊 Selamat atas pencapaian luar biasa ini!\n#Achievement #Proud #{$ukm->alias}",
            'discussion' => "\n\n💭 Apa pendapat kalian? Share di comments!\n#Discussion #Opinion #{$ukm->alias}"
        ];

        return $baseContent . ($enhancements[$type] ?? "\n\n#{$ukm->alias}");
    }

    private function enhanceEventContent($baseContent, $eventType, $ukm, $isPaid)
    {
        $content = $baseContent;

        if ($isPaid) {
            $content .= "\n\n💰 Event berbayar - Investasi terbaik untuk pengembangan diri!";
        } else {
            $content .= "\n\n🎉 FREE EVENT - Kesempatan emas jangan sampai terlewat!";
        }

        $content .= "\n\n📝 Daftar sekarang, slot terbatas!";
        $content .= "\n\n#{$eventType} #{$ukm->alias} #Event2024";

        return $content;
    }

    private function getEventLocation($isOnline, $eventType)
    {
        if ($isOnline) {
            return ['Zoom Meeting', 'Google Meet', 'Microsoft Teams', 'Webinar Platform'][array_rand(['Zoom Meeting', 'Google Meet', 'Microsoft Teams', 'Webinar Platform'])];
        }

        $locations = [
            'workshop' => ['Lab Komputer A-201', 'Ruang Workshop B-301', 'Lab Praktikum C-105'],
            'competition' => ['Auditorium Utama', 'Hall Gedung Rektorat', 'Aula Student Center'],
            'seminar' => ['Auditorium Utama', 'Ruang Seminar B-301', 'Hall Gedung Rektorat'],
            'gathering' => ['Aula Student Center', 'Lapangan Basket Kampus', 'Taman Kampus'],
            'training' => ['Ruang Pelatihan A-301', 'Lab Komputer B-205', 'Ruang Seminar C-401']
        ];

        return $locations[$eventType][array_rand($locations[$eventType])] ?? 'Gedung Fakultas Lt. 2';
    }

    private function getPaymentMethods($ukm)
    {
        $bankAccounts = [
            'BCA' => ['1234567890', '2345678901', '3456789012'],
            'Mandiri' => ['9876543210', '8765432109', '7654321098'],
            'BNI' => ['5555666677', '6666777788', '7777888899']
        ];

        $digitalWallets = [
            'Dana' => ['081234567890', '082345678901', '083456789012'],
            'GoPay' => ['085678901234', '086789012345', '087890123456'],
            'OVO' => ['087890123456', '088901234567', '089012345678']
        ];

        $selectedBank = array_rand($bankAccounts);
        $selectedWallet = array_rand($digitalWallets);

        return [
            [
                'method' => "Bank Transfer {$selectedBank}",
                'account_number' => $bankAccounts[$selectedBank][array_rand($bankAccounts[$selectedBank])],
                'account_name' => "Bendahara {$ukm->alias}",
                'bank_name' => "Bank {$selectedBank}",
                'instructions' => "Transfer ke rekening {$selectedBank} dan kirim bukti pembayaran"
            ],
            [
                'method' => $selectedWallet,
                'account_number' => $digitalWallets[$selectedWallet][array_rand($digitalWallets[$selectedWallet])],
                'account_name' => "Kas {$ukm->alias}",
                'instructions' => "Kirim pembayaran via {$selectedWallet} dan sertakan nama dalam pesan"
            ]
        ];
    }

    private function getEventCustomFields($eventType)
    {
        $baseFields = [
            [
                'label' => 'Student ID',
                'name' => 'student_id',
                'type' => 'text',
                'placeholder' => 'Masukkan NIM',
                'required' => true
            ],
            [
                'label' => 'Nomor WhatsApp',
                'name' => 'whatsapp',
                'type' => 'tel',
                'placeholder' => '08123456789',
                'required' => true
            ]
        ];

        $typeSpecificFields = [
            'workshop' => [
                [
                    'label' => 'Tingkat Pengalaman',
                    'name' => 'experience_level',
                    'type' => 'select',
                    'options' => 'Pemula, Menengah, Mahir',
                    'required' => true
                ]
            ],
            'competition' => [
                [
                    'label' => 'Nama Tim',
                    'name' => 'team_name',
                    'type' => 'text',
                    'placeholder' => 'Masukkan nama tim',
                    'required' => true
                ]
            ],
            'seminar' => [
                [
                    'label' => 'Fakultas',
                    'name' => 'faculty',
                    'type' => 'select',
                    'options' => 'Teknik, Ekonomi, Hukum, Kedokteran, Pertanian',
                    'required' => true
                ]
            ]
        ];

        return array_merge($baseFields, $typeSpecificFields[$eventType] ?? []);
    }

    private function getMaxParticipants($eventType)
    {
        $limits = [
            'workshop' => [20, 30, 40],
            'competition' => [15, 20, 25],
            'seminar' => [100, 150, 200],
            'gathering' => [50, 75, 100],
            'training' => [25, 35, 45]
        ];

        return $limits[$eventType][array_rand($limits[$eventType])];
    }

    private function getRandomPostDate($maxDate = null)
    {
        $maxDaysAgo = $maxDate ? Carbon::now()->diffInDays(Carbon::parse($maxDate)) - 1 : 60;
        $maxDaysAgo = max(1, $maxDaysAgo);
        return Carbon::now()->subDays(rand(1, min(60, $maxDaysAgo)));
    }

    private function calculateEventDate($schedule)
    {
        return Carbon::now()->addDays(rand($schedule['min_days'], $schedule['max_days']));
    }

    private function selectImage($imagePool, $type = null)
    {
        // Return null 25% of the time or if imagePool is empty
        if (empty($imagePool) || rand(0, 3) === 0) {
            return null;
        }

        return $imagePool[array_rand($imagePool)];
    }

    // Configuration data methods
    private function getContentTemplates()
    {
        return [
            'Himpunan' => [
                'posts' => [
                    ['title' => 'Roadmap Karir untuk Fresh Graduate', 'content' => 'Panduan lengkap memulai karir setelah lulus kuliah. Tips interview, CV writing, dan networking yang efektif.'],
                    ['title' => 'Review Teknologi Terbaru dalam Industri', 'content' => 'Pembahasan mendalam tentang tren teknologi yang sedang berkembang dan dampaknya terhadap industri.'],
                    ['title' => 'Tips Mengoptimalkan Pembelajaran Online', 'content' => 'Strategi belajar efektif dalam era digital dengan memanfaatkan berbagai platform pembelajaran online.'],
                    ['title' => 'Pentingnya Soft Skills di Dunia Kerja', 'content' => 'Mengapa soft skills sama pentingnya dengan technical skills dalam mengembangkan karir profesional.'],
                    ['title' => 'Project Portfolio untuk Mahasiswa', 'content' => 'Cara membangun portfolio project yang menarik untuk menunjukkan kemampuan kepada rekruiter.']
                ],
                'events' => [
                    ['title' => 'Workshop Pengembangan Software Modern', 'content' => 'Pelatihan intensif pengembangan software menggunakan framework dan tools terkini.'],
                    ['title' => 'Seminar Industri 4.0 dan Masa Depan Teknologi', 'content' => 'Diskusi mendalam tentang revolusi industri 4.0 dan persiapan menghadapi masa depan teknologi.'],
                    ['title' => 'Hackathon Innovation Challenge', 'content' => 'Kompetisi 48 jam untuk mengembangkan solusi inovatif terhadap permasalahan nyata.'],
                    ['title' => 'Bootcamp Full Stack Development', 'content' => 'Program intensif 3 hari untuk menguasai full stack development dari dasar hingga advanced.']
                ],
                'announcements' => [
                    ['title' => 'Pembukaan Pendaftaran Program Mentoring', 'content' => 'Program mentoring untuk bimbingan akademik dan pengembangan karir dengan senior berpengalaman.'],
                    ['title' => 'Informasi Beasiswa Penelitian', 'content' => 'Peluang beasiswa untuk mahasiswa yang ingin melanjutkan penelitian di bidang teknologi.']
                ]
            ],
            'UKM Seni' => [
                'posts' => [
                    ['title' => 'Teknik Dasar Fotografi untuk Pemula', 'content' => 'Panduan lengkap memahami komposisi, pencahayaan, dan teknik fotografi untuk menghasilkan foto yang memukau.'],
                    ['title' => 'Eksplorasi Aliran Musik Nusantara', 'content' => 'Mengenal kekayaan musik tradisional Indonesia dan cara mengapresiasi warisan budaya melalui musik.'],
                    ['title' => 'Digital Art vs Traditional Art', 'content' => 'Perbandingan dan keunikan masing-masing medium seni dalam era digital modern.'],
                    ['title' => 'Tips Perform di Atas Panggung', 'content' => 'Mengatasi demam panggung dan teknik tampil percaya diri di depan audience besar.']
                ],
                'events' => [
                    ['title' => 'Pameran Fotografi "Jejak Nusantara"', 'content' => 'Pameran foto dokumenter perjalanan budaya dan alam Indonesia dari berbagai daerah.'],
                    ['title' => 'Konser Musik Akustik Kampus', 'content' => 'Malam musik akustik menampilkan talenta-talenta terbaik mahasiswa dengan berbagai genre musik.'],
                    ['title' => 'Workshop Editing Video Kreatif', 'content' => 'Pelatihan editing video dengan teknik storytelling yang menarik menggunakan software professional.'],
                    ['title' => 'Festival Seni Multikultural', 'content' => 'Perayaan keberagaman budaya melalui seni tari, musik, dan pertunjukan dari berbagai daerah.']
                ],
                'announcements' => [
                    ['title' => 'Open Call: Kontributor Konten Kreatif', 'content' => 'Kesempatan bergabung sebagai kontributor konten untuk media sosial dan publikasi UKM.'],
                    ['title' => 'Audisi Talent Show Tahunan', 'content' => 'Pendaftaran audisi untuk talent show tahunan terbuka untuk semua mahasiswa dengan berbagai bakat.']
                ]
            ],
            'UKM Olahraga' => [
                'posts' => [
                    ['title' => 'Program Latihan untuk Pemula', 'content' => 'Panduan memulai olahraga yang aman dan efektif untuk mahasiswa yang baru memulai aktifitas fisik.'],
                    ['title' => 'Nutrisi dan Hidrasi untuk Atlet', 'content' => 'Pentingnya asupan nutrisi seimbang dan hidrasi yang tepat untuk mendukung performa olahraga optimal.'],
                    ['title' => 'Mental Training dalam Olahraga', 'content' => 'Aspek psikologis dalam olahraga dan teknik mental training untuk meningkatkan fokus dan kepercayaan diri.'],
                    ['title' => 'Recovery dan Injury Prevention', 'content' => 'Metode pemulihan yang tepat dan pencegahan cedera untuk menjaga konsistensi latihan.']
                ],
                'events' => [
                    ['title' => 'Turnamen Futsal Antar Fakultas', 'content' => 'Kompetisi futsal bergengsi tingkat kampus dengan total hadiah jutaan rupiah untuk para juara.'],
                    ['title' => 'Marathon Campus Run 10K', 'content' => 'Event lari marathon 10K untuk mempromosikan gaya hidup sehat di kalangan mahasiswa.'],
                    ['title' => 'Workshop Injury Prevention', 'content' => 'Edukasi pencegahan cedera olahraga dengan panduan dari fisioterapis dan ahli olahraga.'],
                    ['title' => 'Sports Clinic: Teknik Dasar Berbagai Cabang', 'content' => 'Klinik olahraga untuk mempelajari teknik dasar berbagai cabang olahraga dari pelatih bersertifikat.']
                ],
                'announcements' => [
                    ['title' => 'Seleksi Tim Kampus untuk PORDA', 'content' => 'Pembukaan seleksi atlet untuk mewakili kampus dalam Pekan Olahraga Daerah tahun ini.'],
                    ['title' => 'Program Beasiswa Atlet Berprestasi', 'content' => 'Informasi beasiswa khusus untuk mahasiswa atlet yang menunjukkan prestasi olahraga luar biasa.']
                ]
            ],
            'default' => [
                'posts' => [
                    ['title' => 'Tips Produktif selama Kuliah', 'content' => 'Strategi mengoptimalkan waktu kuliah untuk mencapai prestasi akademik dan mengembangkan diri secara holistik.'],
                    ['title' => 'Mengembangkan Leadership Skills', 'content' => 'Pentingnya kepemimpinan dalam organisasi dan cara mengasah kemampuan memimpin sejak mahasiswa.'],
                    ['title' => 'Work-Life Balance untuk Mahasiswa', 'content' => 'Menjaga keseimbangan antara akademik, organisasi, dan kehidupan pribadi untuk mental health yang optimal.'],
                    ['title' => 'Networking dan Personal Branding', 'content' => 'Membangun jaringan profesional dan personal branding yang kuat untuk masa depan karir yang cerah.']
                ],
                'events' => [
                    ['title' => 'Seminar Kewirausahaan dan Startup', 'content' => 'Inspirasi dan edukasi memulai bisnis dari nol dengan menghadirkan founder startup sukses.'],
                    ['title' => 'Workshop Public Speaking', 'content' => 'Pelatihan berbicara di depan umum untuk meningkatkan confidence dan communication skills.'],
                    ['title' => 'Gathering dan Team Building', 'content' => 'Acara kebersamaan untuk mempererat hubungan antar anggota dan membangun team spirit yang solid.'],
                    ['title' => 'Community Service Project', 'content' => 'Program pengabdian masyarakat untuk memberikan dampak positif dan mengembangkan social awareness.']
                ],
                'announcements' => [
                    ['title' => 'Rekrutmen Pengurus Periode Baru', 'content' => 'Pembukaan pendaftaran untuk posisi pengurus organisasi periode baru dengan berbagai divisi.'],
                    ['title' => 'Program Pertukaran Mahasiswa', 'content' => 'Peluang mengikuti program pertukaran mahasiswa ke universitas partner di dalam dan luar negeri.']
                ]
            ]
        ];
    }

    private function getEventSchedulingPatterns()
    {
        return [
            'workshop' => [
                ['min_days' => 7, 'max_days' => 21, 'paid_probability' => 60, 'online_probability' => 30],
                ['min_days' => 14, 'max_days' => 35, 'paid_probability' => 80, 'online_probability' => 40],
            ],
            'competition' => [
                ['min_days' => 21, 'max_days' => 60, 'paid_probability' => 70, 'online_probability' => 20],
                ['min_days' => 30, 'max_days' => 90, 'paid_probability' => 90, 'online_probability' => 15],
            ],
            'seminar' => [
                ['min_days' => 7, 'max_days' => 30, 'paid_probability' => 40, 'online_probability' => 60],
                ['min_days' => 14, 'max_days' => 45, 'paid_probability' => 30, 'online_probability' => 70],
            ],
            'gathering' => [
                ['min_days' => 10, 'max_days' => 25, 'paid_probability' => 50, 'online_probability' => 10],
                ['min_days' => 15, 'max_days' => 40, 'paid_probability' => 60, 'online_probability' => 5],
            ],
            'training' => [
                ['min_days' => 14, 'max_days' => 30, 'paid_probability' => 85, 'online_probability' => 45],
                ['min_days' => 21, 'max_days' => 50, 'paid_probability' => 95, 'online_probability' => 35],
            ]
        ];
    }

    private function getImagePool()
    {
        return [
            'posts' => [
                'feeds/post-tech-1.jpg',
                'feeds/post-study-1.jpg',
                'feeds/post-campus-1.jpg',
                'feeds/post-achievement-1.jpg',
                'feeds/post-tutorial-1.jpg',
                'feeds/post-news-1.jpg',
                'feeds/post-discussion-1.jpg',
                'feeds/post-announcement-1.jpg'
            ],
            'events' => [
                'feeds/event-workshop-1.jpg',
                'feeds/event-seminar-1.jpg',
                'feeds/event-competition-1.jpg',
                'feeds/event-gathering-1.jpg',
                'feeds/event-training-1.jpg',
                'feeds/event-expo-1.jpg',
                'feeds/event-conference-1.jpg',
                'feeds/event-hackathon-1.jpg'
            ]
        ];
    }
}
