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
                'max_participants' => $this->getMaxParticipants($eventType),
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
                'content' => '<div class="urgent-announcement"><h3>⚠️ PENGUMUMAN PENTING ⚠️</h3>' . $announcement['content'] . '</div>',
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
                'content' => '<div class="recruitment-post"><p><strong>Kami membuka kesempatan</strong> untuk bergabung dengan ' . $ukm->name . '!</p><div class="requirements"><h4>📝 Persyaratan:</h4><ul><li>Mahasiswa aktif</li><li>Berkomitmen tinggi</li><li>Siap berkontribusi</li></ul></div><p><strong>📅 Pendaftaran:</strong> ' . Carbon::now()->addDays(rand(5, 15))->format('d M Y') . '</p><p>#OpenRecruitment #' . $ukm->alias . '</p></div>',
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
                'title' => 'Festival Teknologi Indonesia "TechFest 2024"',
                'content' => '<div class="collab-event"><p><strong>Festival teknologi terbesar di kampus</strong> dengan tema "Indonesia Digital Ready" dan partisipasi dari berbagai himpunan teknologi.</p><p>🎯 <strong>Highlight acara:</strong></p><ul><li>🔬 Pameran inovasi teknologi Indonesia</li><li>🏆 Kompetisi startup dan coding</li><li>💻 Demo teknologi AI, IoT, dan robotika</li><li>🚀 Talkshow dengan founder unicorn Indonesia</li></ul></div>',
                'participants' => ['HMTI', 'HMTE', 'UKM Robot'],
                'type' => 'competition'
            ],
            [
                'title' => 'Expo Kreativitas "Bhinneka Tunggal Ika Arts"',
                'content' => '<div class="collab-event"><p><strong>Pameran kreativitas mahasiswa</strong> yang memadukan seni tradisional Indonesia dengan teknologi modern.</p><p>✨ <strong>Kolaborasi spektakuler:</strong></p><ul><li>📸 Digital photography exhibition</li><li>🎵 Fusion music performance</li><li>💻 Interactive digital art installation</li><li>🎭 Cultural tech storytelling</li></ul><p><em>Showcase karya terbaik mahasiswa Indonesia!</em></p></div>',
                'participants' => ['UKM Foto', 'UKM PSM', 'HMTI'],
                'type' => 'gathering'
            ],
            [
                'title' => 'Seminar Nasional "Teknologi untuk Indonesia Maju"',
                'content' => '<div class="collab-event"><p><strong>Seminar nasional bergengsi</strong> dengan pembicara dari industri teknologi terkemuka Indonesia dan regional.</p><p>🇮🇩 <strong>Tema pembahasan:</strong></p><ul><li>🏭 Industri 4.0 untuk manufaktur Indonesia</li><li>⚡ Smart grid dan energy transition</li><li>🏗️ Infrastructure development dengan teknologi</li><li>🌐 Digital transformation di berbagai sektor</li></ul><p>🔮 Membahas <em>roadmap teknologi Indonesia 2030</em> dan peluang karir masa depan.</p></div>',
                'participants' => ['HMTI', 'HMTE', 'HMTM'],
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
                'content' => $eventData['content'] . '<div class="collaboration"><p><strong>🤝 Kolaborasi:</strong> ' . implode(', ', $eventData['participants']) . '</p></div>',
                'image' => $this->selectImage($imagePool),
                'event_date' => $this->calculateEventDate($schedule),
                'event_type' => 'offline',
                'location' => 'Auditorium Utama Kampus',
                'is_paid' => $schedule['paid_probability'] > 50,
                'max_participants' => $this->getMaxParticipants($eventData['type']),
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDays(rand(1, 10)),
            ]);
        }
    }

    private function createTrendingPosts($ukms, $imagePool)
    {
        $trendingTopics = [
            ['title' => 'Tips Sukses Magang di Startup Unicorn Indonesia', 'hashtags' => '#MagangGojek #TipsKarir #StartupIndonesia'],
            ['title' => 'Pengalaman Ikut Google I/O dan Represent Indonesia', 'hashtags' => '#GoogleIO #IndonesiaTech #ProudMoment'],
            ['title' => 'Review Workshop AI oleh Praktisi Tokopedia', 'hashtags' => '#MachineLearning #AI #TokopediaCare'],
            ['title' => 'Behind The Scene Bikin Aplikasi UMKM Indonesia', 'hashtags' => '#ProjectAkhir #UMKM #TechForGood'],
            ['title' => 'Thread: Cara Lolos Interview di Gojek Engineering', 'hashtags' => '#InterviewTips #GojekTech #CareerAdvice'],
            ['title' => 'Mahasiswa Indonesia Juara Kompetisi Robotika Asia', 'hashtags' => '#RobotikaIndonesia #AchievementUnlocked #BanggaIndonesia'],
        ];

        foreach ($trendingTopics as $topic) {
            $ukm = $ukms->random();
            $createdAt = Carbon::now()->subHours(rand(2, 12));

            Feed::create([
                'unit_kegiatan_id' => $ukm->id,
                'type' => 'post',
                'title' => "🔥 VIRAL: {$topic['title']}",
                'content' => '<div class="viral-post"><p><strong>Thread viral ini</strong> lagi rame banget! Banyak yang DM minta tips dan tricknya.</p><p>📱 <strong>Share pengalaman:</strong> Drop comment kalau kalian ada pengalaman serupa!</p><p>' . $topic['hashtags'] . '</p><p>#Viral #TrendingIndonesia #' . $ukm->alias . '</p></div>',
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
            'amount' => $amount,
            'currency' => 'IDR',
            'payment_methods' => $this->getPaymentMethods($ukm),
            'custom_fields' => $this->getEventCustomFields($eventType)
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
                    ['title' => 'Roadmap Karir Software Engineer di Indonesia', 'content' => '<div class="post-content"><p><strong>Panduan lengkap memulai karir</strong> sebagai software engineer di ekosistem teknologi Indonesia.</p><p>🇮🇩 <strong>Perusahaan tech unicorn Indonesia:</strong></p><ul><li>🚗 Gojek - Super app terdepan</li><li>🛒 Tokopedia - E-commerce terbesar</li><li>✈️ Traveloka - Online travel agent</li><li>🏪 Bukalapak - Marketplace UMKM</li></ul><p><em>Siapkan dirimu untuk berkontribusi membangun Indonesia Digital!</em></p></div>'],
                    ['title' => 'Review Teknologi Terbaru: AI dalam Industri Indonesia', 'content' => '<div class="post-content"><p><strong>Pembahasan mendalam</strong> tentang implementasi Artificial Intelligence di perusahaan teknologi Indonesia dan dampaknya terhadap transformasi digital.</p><p>🤖 <strong>Use case AI di Indonesia:</strong></p><ul><li>🚴 GoPay fraud detection</li><li>📱 Tokopedia recommendation engine</li><li>🎯 Shopee personalization</li></ul><p>💡 Peluang karir AI engineer semakin terbuka lebar!</p></div>'],
                    ['title' => 'Tips Mengoptimalkan Pembelajaran Coding untuk Mahasiswa Indonesia', 'content' => '<div class="post-content"><p><strong>Strategi belajar coding efektif</strong> dengan memanfaatkan platform pembelajaran Indonesia dan internasional.</p><p>📚 <strong>Platform rekomendasi:</strong></p><ul><li>🎓 Dicoding - Platform belajar coding Indonesia</li><li>💻 Buildwithangga - UI/UX dan programming</li><li>🌟 Progate - Interactive coding lessons</li></ul><p>🎯 Konsisten adalah kunci sukses!</p></div>'],
                    ['title' => 'Pentingnya Open Source dalam Ekosistem Tech Indonesia', 'content' => '<div class="post-content"><p>Mengapa <em>open source contribution</em> penting untuk pengembangan karir di industri teknologi Indonesia.</p><p>🔗 <strong>Benefit kontribusi open source:</strong></p><ul><li>📈 Portfolio yang kuat</li><li>🤝 Networking dengan developer global</li><li>💼 Peluang kerja di tech company</li></ul><p>🇮🇩 <strong>Mari bangun ekosistem tech Indonesia yang lebih kuat!</strong></p></div>'],
                    ['title' => 'Project Portfolio untuk Fresh Graduate Indonesia', 'content' => '<div class="post-content"><p><strong>Cara membangun portfolio project</strong> yang menarik untuk melamar kerja di startup dan perusahaan teknologi Indonesia.</p><p>💼 <strong>Project ideas yang relevan:</strong></p><ul><li>🏪 E-commerce UMKM platform</li><li>📱 Aplikasi transportasi lokal</li><li>💳 Fintech untuk financial inclusion</li><li>🌾 AgriTech untuk petani Indonesia</li></ul><p>📁 Portfolio yang kuat = Peluang kerja yang lebih besar di Indonesia!</p></div>']
                ],
                'events' => [
                    ['title' => 'Workshop React.js untuk Pengembangan Startup Indonesia', 'content' => '<div class="event-content"><p><strong>Pelatihan intensif</strong> pengembangan web modern menggunakan React.js untuk mendukung ekosistem startup Indonesia.</p><p>🚀 <strong>Materi workshop:</strong></p><ul><li>⚛️ React fundamentals dan hooks</li><li>🎨 UI/UX design system Indonesia</li><li>📱 Progressive Web App (PWA)</li><li>🌐 Deployment ke Vercel/Netlify</li></ul><p>💡 Build the next Indonesian unicorn!</p></div>'],
                    ['title' => 'Seminar Industri 4.0: Transformasi Digital UMKM Indonesia', 'content' => '<div class="event-content"><p><strong>Diskusi mendalam</strong> tentang peran teknologi dalam transformasi digital UMKM Indonesia dan kontribusi mahasiswa.</p><p>🏪 <strong>Topik pembahasan:</strong></p><ul><li>📊 Digitalisasi proses bisnis UMKM</li><li>💳 Integrasi payment gateway lokal</li><li>📈 Digital marketing untuk UMKM</li><li>🤝 Kolaborasi startup-UMKM</li></ul><p>🇮🇩 Wujudkan UMKM Indonesia yang berdaya saing global!</p></div>'],
                    ['title' => 'Hackathon Solusi Smart City Jakarta', 'content' => '<div class="event-content"><p><strong>Kompetisi 48 jam</strong> untuk mengembangkan solusi teknologi mengatasi permasalahan urban Jakarta dan kota besar Indonesia.</p><p>🏙️ <strong>Challenge themes:</strong></p><ul><li>🚇 Smart transportation (TransJakarta, MRT)</li><li>🌊 Flood early warning system</li><li>♻️ Waste management optimization</li><li>💼 E-governance solutions</li></ul><p>⚡ Ciptakan Jakarta yang lebih smart dan livable!</p></div>'],
                    ['title' => 'Bootcamp Full Stack Developer Indonesia', 'content' => '<div class="event-content"><p><strong>Program intensif 3 hari</strong> untuk menguasai full stack development dengan tech stack yang populer di startup Indonesia.</p><p>💻 <strong>Tech stack:</strong></p><ul><li>⚛️ Frontend: React.js + TypeScript</li><li>🟢 Backend: Node.js + Express</li><li>🗄️ Database: PostgreSQL</li><li>☁️ Cloud: AWS/Google Cloud</li></ul><p>🚀 From zero to hero dalam 3 hari - siap berkarir di startup Indonesia!</p></div>']
                ],
                'announcements' => [
                    ['title' => 'Pembukaan Program Mentoring dengan Praktisi Tech Indonesia', 'content' => '<div class="announcement-content"><p><strong>Program mentoring eksklusif</strong> dengan senior software engineer dari Gojek, Tokopedia, Shopee, dan startup unicorn Indonesia lainnya.</p><p>👨‍💻 <strong>Benefit mentoring:</strong></p><ul><li>🎯 Career guidance dari praktisi</li><li>💼 Mock interview untuk tech company</li><li>🤝 Networking dengan industri</li><li>📈 Code review dan technical feedback</li></ul><p>🌟 Kesempatan emas untuk accelerate karir tech mu!</p></div>'],
                    ['title' => 'Informasi Beasiswa Google Developer Student Clubs Indonesia', 'content' => '<div class="announcement-content"><p><strong>Peluang beasiswa</strong> untuk mahasiswa yang ingin mengikuti program Google Developer Student Clubs dan Google Developer Certification.</p><p>🎓 <strong>Program tersedia:</strong></p><ul><li>📱 Android Developer Certification</li><li>☁️ Google Cloud Platform Training</li><li>🤖 Machine Learning Engineer Path</li><li>🌐 Web Development Fundamentals</li></ul><p>🇮🇩 Representasikan Indonesia di komunitas developer global!</p></div>']
                ]
            ],
            'UKM Seni' => [
                'posts' => [
                    ['title' => 'Teknik Fotografi Wisata Indonesia untuk Content Creator', 'content' => '<div class="post-content"><p><strong>Panduan lengkap memotret</strong> keindahan wisata Indonesia untuk content creator dan social media influencer.</p><p>🏝️ <strong>Destinasi fotogenik Indonesia:</strong></p><ul><li>🌋 Bromo sunrise photography</li><li>🏖️ Pink beach Komodo composition</li><li>🏛️ Borobudur temple golden hour</li><li>🌊 Raja Ampat underwater photography</li></ul><p>📸 <em>Promosikan Wonderful Indonesia melalui lensa!</em></p></div>'],
                    ['title' => 'Eksplorasi Musik Tradisional Nusantara di Era Digital', 'content' => '<div class="post-content"><p><strong>Mengenal kekayaan musik tradisional Indonesia</strong> dan cara melestarikannya melalui teknologi digital modern.</p><p>🎵 <strong>Musik tradisional populer:</strong></p><ul><li>🎼 Gamelan Jawa dan Bali</li><li>🥁 Gordang sambilan Batak</li><li>🎺 Tifa dari Papua</li><li>🎻 Sasando dari NTT</li></ul><p>🇮🇩 Jelajahi keindahan musik nusantara yang tak terbatas!</p></div>'],
                    ['title' => 'Digital Art Indonesia: Dari Wayang hingga NFT', 'content' => '<div class="post-content"><p><strong>Evolusi seni digital Indonesia</strong> dari seni tradisional wayang hingga NFT (Non-Fungible Token) modern.</p><p>🎨 <strong>Perkembangan digital art:</strong></p><ul><li>🎭 Wayang digital animation</li><li>🖼️ Batik pattern dalam digital design</li><li>🎮 Character design untuk game Indonesia</li><li>💎 NFT art dengan tema budaya lokal</li></ul><p>✨ Tradisi bertemu teknologi dalam harmoni sempurna!</p></div>'],
                    ['title' => 'Tips Perform di Festival Seni Mahasiswa Nasional', 'content' => '<div class="post-content"><p><strong>Persiapan mengikuti</strong> Festival dan Lomba Seni Siswa Nasional (FLS2N) tingkat mahasiswa di Indonesia.</p><p>🎭 <strong>Kategori populer:</strong></p><ul><li>🎤 Vocal solo dan group</li><li>💃 Tari kreasi dan tradisional</li><li>🎬 Film pendek dokumenter</li><li>📷 Fotografi jurnalistik</li></ul><p>🏆 <em>Wakili kampus di kompetisi seni tingkat nasional!</em></p></div>']
                ],
                'events' => [
                    ['title' => 'Pameran Fotografi "Pesona Indonesia Timur"', 'content' => '<div class="event-content"><p><strong>Pameran foto dokumenter</strong> perjalanan eksplorasi keindahan alam dan budaya Indonesia Timur dari Maluku hingga Papua.</p><p>🌍 <strong>Highlight pameran:</strong></p><ul><li>🏝️ Kepulauan Raja Ampat</li><li>🦅 Burung Cenderawasih Papua</li><li>🏛️ Arsitektur tradisional Toraja</li><li>🌊 Underwater Bunaken dan Wakatobi</li></ul><p>📸 Saksikan keindahan Indonesia Timur melalui lensa para fotografer muda!</p></div>'],
                    ['title' => 'Konser Kolaborasi Musik Tradisional-Modern "Nusantara Harmony"', 'content' => '<div class="event-content"><p><strong>Malam musik spektakuler</strong> yang memadukan instrumen tradisional Nusantara dengan aransemen musik modern contemporary.</p><p>🎼 <strong>Kolaborasi istimewa:</strong></p><ul><li>🥁 Gamelan Jawa + Electronic music</li><li>🎺 Angklung Sunda + Jazz fusion</li><li>🎻 Sasando NTT + Acoustic guitar</li><li>🪘 Tifa Papua + World music</li></ul><p>🎵 Harmoni budaya dalam irama yang memukau jiwa!</p></div>'],
                    ['title' => 'Workshop Stop Motion Animation dengan Tema Folklore Indonesia', 'content' => '<div class="event-content"><p><strong>Pelatihan teknik animasi</strong> stop motion untuk menceritakan kembali cerita rakyat dan folklore Indonesia dengan pendekatan modern.</p><p>🎬 <strong>Materi workshop:</strong></p><ul><li>📽️ Teknik dasar stop motion</li><li>🎭 Character design wayang modern</li><li>📚 Adaptasi cerita rakyat Indonesia</li><li>🎵 Sound design dengan musik tradisional</li></ul><p>✨ Hidupkan kembali cerita nenek moyang dalam format digital!</p></div>'],
                    ['title' => 'Festival Seni Multikultural "Bhinneka Tunggal Ika"', 'content' => '<div class="event-content"><p><strong>Perayaan keberagaman budaya Indonesia</strong> melalui seni tari, musik, kuliner, dan pameran kerajinan dari 34 provinsi Indonesia.</p><p>🌈 <strong>Highlights festival:</strong></p><ul><li>💃 Tari daerah dari Sabang sampai Merauke</li><li>🍛 Food court kuliner nusantara</li><li>🛍️ Bazar kerajinan tangan lokal</li><li>🎪 Workshop batik dan tenun tradisional</li></ul><p>🇮🇩 Unity in diversity through Indonesian arts and culture!</p></div>']
                ],
                'announcements' => [
                    ['title' => 'Open Call: Kontributor Konten Wonderful Indonesia', 'content' => '<div class="announcement-content"><p><strong>Kesempatan berkolaborasi</strong> dengan Kementerian Pariwisata RI untuk campaign "Wonderful Indonesia" melalui konten kreatif mahasiswa.</p><p>🎯 <strong>Content needed:</strong></p><ul><li>📹 Video promosi destinasi wisata</li><li>📸 Photography hidden gems Indonesia</li><li>✍️ Travel blog dan storytelling</li><li>🎨 Graphic design tourism campaign</li></ul><p>🌟 Promosikan Indonesia ke dunia melalui karya kreatifmu!</p></div>'],
                    ['title' => 'Audisi Indonesian Idol & The Voice Indonesia Campus Edition', 'content' => '<div class="announcement-content"><p><strong>Pendaftaran audisi</strong> khusus mahasiswa untuk Indonesian Idol dan The Voice Indonesia Campus Edition 2024.</p><p>🎤 <strong>Kategori audisi:</strong></p><ul><li>🎵 Solo vocal (pop, rock, jazz, R&B)</li><li>🎼 Original song composition</li><li>🎸 Acoustic performance</li><li>🎭 Musical theatre</li></ul><p>⭐ Waktunya shine bright di panggung nasional Indonesia!</p></div>']
                ]
            ],
            'UKM Olahraga' => [
                'posts' => [
                    ['title' => 'Persiapan Atlet Mahasiswa untuk PON Papua 2024', 'content' => '<div class="post-content"><p><strong>Panduan persiapan fisik dan mental</strong> untuk mahasiswa atlet yang akan berkompetisi di Pekan Olahraga Nasional Papua.</p><p>🏃‍♂️ <strong>Fokus persiapan:</strong></p><ul><li>💪 Strength & conditioning program</li><li>🧠 Sports psychology training</li><li>🥗 Nutrition plan untuk atlet Indonesia</li><li>🏥 Injury prevention & recovery</li></ul><p>🇮🇩 <em>Harumkan nama daerah di PON Papua!</em></p></div>'],
                    ['title' => 'Sepak Bola Indonesia: Dari PSSI hingga Liga 1', 'content' => '<div class="post-content"><p><strong>Analisis perkembangan sepak bola Indonesia</strong> dari level grassroot hingga professional dan peluang untuk mahasiswa.</p><p>⚽ <strong>Ekosistem sepak bola Indonesia:</strong></p><ul><li>🏆 Liga 1 Indonesia</li><li>⭐ Timnas Indonesia U-23</li><li>🎓 Football academy development</li><li>👥 Futsal dan sepak bola pantai</li></ul><p>🥅 Jadilah bagian dari kebangkitan sepak bola Indonesia!</p></div>'],
                    ['title' => 'Badminton Indonesia: Melanjutkan Tradisi Emas Thomas-Uber Cup', 'content' => '<div class="post-content"><p><strong>Sejarah keemasan badminton Indonesia</strong> dan bagaimana mahasiswa bisa berkontribusi melanjutkan prestasi di level internasional.</p><p>🏸 <strong>Legenda badminton Indonesia:</strong></p><ul><li>🥇 Taufik Hidayat - Olympic Gold</li><li>👑 Susi Susanti - All England Champion</li><li>🏆 Kevin/Marcus - World Champions</li><li>⭐ Greysia/Apriyani - Tokyo 2020 Gold</li></ul><p>🇮🇩 <em>Merah Putih berkibar di setiap smash kemenangan!</em></p></div>'],
                    ['title' => 'Olahraga Tradisional Indonesia yang Mendunia', 'content' => '<div class="post-content"><p><strong>Mengenal olahraga tradisional Indonesia</strong> yang telah diakui dunia dan berpotensi masuk Asian Games.</p><p>🎯 <strong>Olahraga tradisional unggulan:</strong></p><ul><li>🥏 Sepak takraw - The kick volleyball</li><li>🏃‍♂️ Pencak silat - Indonesian martial arts</li><li>🎪 Panjat pinang - Traditional climbing</li><li>🏹 Sumpit - Traditional blowgun</li></ul><p>🌟 Lestarikan warisan budaya melalui olahraga!</p></div>']
                ],
                'events' => [
                    ['title' => 'Turnamen LIMA Basketball Indonesia Championship', 'content' => '<div class="event-content"><p><strong>Kompetisi basket mahasiswa terbesar</strong> se-Indonesia dengan partisipasi 64 universitas dari seluruh nusantara.</p><p>🏀 <strong>Tournament structure:</strong></p><ul><li>📍 Regional qualifiers: Jakarta, Surabaya, Medan, Makassar</li><li>🏆 National finals di Jakarta</li><li>📺 Live streaming di YouTube dan TV nasional</li><li>💰 Total prize pool 500 juta rupiah</li></ul><p>🔥 Tunjukkan skill terbaik basketmu di level nasional!</p></div>'],
                    ['title' => 'Training Camp Intensif Atlet PON dengan Pelatih Nasional', 'content' => '<div class="event-content"><p><strong>Pemusatan latihan eksklusif</strong> untuk mahasiswa atlet berprestasi dengan pelatih timnas Indonesia dan fasilitas training center terbaik.</p><p>💪 <strong>Program training:</strong></p><ul><li>🏃‍♂️ Athletic performance testing</li><li>🥗 Sports nutrition consultation</li><li>🧠 Mental conditioning dengan sports psychologist</li><li>🏥 Medical check-up komprehensif</li></ul><p>⭐ Persiapan terbaik untuk menjadi atlet elite Indonesia!</p></div>'],
                    ['title' => 'Marathon Jakarta "Wonderful Indonesia Run"', 'content' => '<div class="event-content"><p><strong>Event lari maraton internasional</strong> yang menampilkan keindahan Jakarta dengan rute melewati landmark ikonik ibukota.</p><p>🏃‍♂️ <strong>Race categories:</strong></p><ul><li>🎽 Full Marathon (42.2K)</li><li>🏃‍♀️ Half Marathon (21.1K)</li><li>🚶‍♂️ Fun Run (10K & 5K)</li><li>👶 Kids Run (1K & 3K)</li></ul><p>🌆 Jelajahi Jakarta sambil promoting healthy lifestyle!</p></div>'],
                    ['title' => 'Festival Olahraga Tradisional "Pesona Nusantara Games"', 'content' => '<div class="event-content"><p><strong>Kompetisi multi-cabang olahraga tradisional</strong> Indonesia dengan partisipasi mahasiswa dari berbagai daerah.</p><p>🎪 <strong>Cabang olahraga:</strong></p><ul><li>🥏 Sepak takraw championship</li><li>🥋 Pencak silat tournament</li><li>🏹 Lomba panahan tradisional</li><li>🚣‍♂️ Dayung perahu naga</li></ul><p>🇮🇩 Bangkitkan semangat sportivitas dalam tradisi nusantara!</p></div>']
                ],
                'announcements' => [
                    ['title' => 'Seleksi Atlet Mahasiswa untuk SEA Games 2025', 'content' => '<div class="announcement-content"><p><strong>Peluang emas bergabung</strong> dengan Tim Indonesia untuk SEA Games 2025 di Thailand melalui seleksi khusus atlet mahasiswa berprestasi.</p><p>🥇 <strong>Cabang olahraga prioritas:</strong></p><ul><li>🏊‍♂️ Renang dan polo air</li><li>🏸 Badminton ganda dan beregu</li><li>⚽ Sepak bola U-23</li><li>🥋 Pencak silat dan taekwondo</li></ul><p>🇮🇩 Wakili Indonesia di ajang olahraga bergengsi Asia Tenggara!</p></div>'],
                    ['title' => 'Beasiswa Atlet Berprestasi Kemenpora RI', 'content' => '<div class="announcement-content"><p><strong>Program beasiswa penuh</strong> dari Kementerian Pemuda dan Olahraga RI untuk mahasiswa atlet berprestasi tingkat nasional dan internasional.</p><p>💰 <strong>Benefit beasiswa:</strong></p><ul><li>💵 Biaya kuliah full coverage</li><li>🏠 Asrama atlet dan makan</li><li>🏥 Asuransi kesehatan dan cedera</li><li>💪 Akses training center nasional</li></ul><p>🌟 Raih prestasi sambil menyelesaikan pendidikan!</p></div>']
                ]
            ],
            'UKM Teknologi' => [
                'posts' => [
                    ['title' => 'Robotika Indonesia untuk Kompetisi Internasional', 'content' => '<div class="post-content"><p><strong>Perkembangan robotika Indonesia</strong> dalam kompetisi internasional dan kontribusi universitas dalam riset robotika.</p><p>🤖 <strong>Kompetisi robotika bergengsi:</strong></p><ul><li>🏆 RoboCup Soccer World Championship</li><li>🚁 DJI RoboMaster Competition</li><li>🎯 FIRA Robot World Cup</li><li>🌍 World Robot Olympiad (WRO)</li></ul><p>⚡ Banggakan Indonesia di pentas robotika dunia!</p></div>'],
                    ['title' => 'IoT untuk Smart Farming Indonesia', 'content' => '<div class="post-content"><p><strong>Penerapan Internet of Things</strong> untuk modernisasi pertanian Indonesia dan pemberdayaan petani dengan teknologi.</p><p>🌾 <strong>Solusi IoT agriculture:</strong></p><ul><li>📊 Soil moisture monitoring system</li><li>🌡️ Weather station otomatis</li><li>💧 Smart irrigation control</li><li>🐛 Pest detection menggunakan AI</li></ul><p>🇮🇩 <em>Teknologi untuk swasembada pangan Indonesia!</em></p></div>'],
                    ['title' => 'Startup Deep Tech Indonesia yang Menginspirasi', 'content' => '<div class="post-content"><p><strong>Profil startup deep technology Indonesia</strong> yang berhasil mengembangkan solusi inovatif dengan teknologi canggih.</p><p>🚀 <strong>Startup deep tech unggulan:</strong></p><ul><li>🤖 Nodeflux - Computer vision AI</li><li>🏥 Araya - Healthcare AI platform</li><li>🌾 HARA - AgriTech data exchange</li><li>⚡ Koltiva - Supply chain traceability</li></ul><p>💡 Jadilah bagian dari revolusi teknologi Indonesia!</p></div>'],
                    ['title' => 'Machine Learning untuk Industri Indonesia', 'content' => '<div class="post-content"><p><strong>Implementasi machine learning</strong> di berbagai sektor industri Indonesia dan peluang karir untuk data scientist.</p><p>🧠 <strong>ML use cases Indonesia:</strong></p><ul><li>🏦 Credit scoring di fintech</li><li>🛒 Recommendation engine e-commerce</li><li>🚗 Route optimization transportasi</li><li>🏥 Medical diagnosis assistance</li></ul><p>📈 <em>Data is the new oil - Indonesia butuh data scientist handal!</em></p></div>']
                ],
                'events' => [
                    ['title' => 'Kompetisi Robot Indonesia (KRI) Regional Jawa', 'content' => '<div class="event-content"><p><strong>Kompetisi robotika terbesar Indonesia</strong> tingkat regional dengan berbagai kategori robot yang menantang kreativitas mahasiswa.</p><p>🏆 <strong>Kategori kompetisi:</strong></p><ul><li>🤖 Kontes Robot ABU (fire fighting)</li><li>⚽ Kontes Robot Sepak Bola Indonesia</li><li>🚁 Kontes Robot Terbang Indonesia</li><li>🎯 Kontes Robot Pemadam Api</li></ul><p>🔥 Uji kemampuan engineering dan programming terbaikmu!</p></div>'],
                    ['title' => 'Workshop AI & Machine Learning dengan Google Developers', 'content' => '<div class="event-content"><p><strong>Pelatihan intensif AI/ML</strong> bersama Google Developer Expert Indonesia dan praktisi machine learning dari startup unicorn.</p><p>🧠 <strong>Workshop modules:</strong></p><ul><li>🐍 Python untuk data science</li><li>🔗 TensorFlow dan Keras hands-on</li><li>☁️ Google Cloud AI Platform</li><li>📊 Computer vision dan NLP projects</li></ul><p>🚀 Master AI/ML dengan guidance dari industry experts!</p></div>'],
                    ['title' => 'Hackathon FinTech "Digital Payment Revolution"', 'content' => '<div class="event-content"><p><strong>Marathon coding 48 jam</strong> untuk mengembangkan solusi financial technology yang mendukung inklusi keuangan Indonesia.</p><p>💳 <strong>Challenge themes:</strong></p><ul><li>🏪 QRIS integration untuk UMKM</li><li>📱 Mobile banking untuk daerah terpencil</li><li>🤖 AI-powered personal finance</li><li>🔐 Blockchain untuk supply chain finance</li></ul><p>💰 Ciptakan solusi fintech untuk Indonesia yang lebih inklusif!</p></div>'],
                    ['title' => 'Expo Teknologi "Indonesia Innovation Week"', 'content' => '<div class="event-content"><p><strong>Pameran teknologi dan inovasi</strong> terbesar Indonesia dengan showcase produk tech dari universitas, startup, dan perusahaan teknologi.</p><p>🌟 <strong>Exhibition highlights:</strong></p><ul><li>🤖 Robotics dan automation demo</li><li>🥽 Virtual reality experience zone</li><li>🚗 Autonomous vehicle prototype</li><li>🏥 Healthcare innovation showcase</li></ul><p>🇮🇩 Witness the future of Indonesian technology!</p></div>']
                ],
                'announcements' => [
                    ['title' => 'Program Magang Google Summer of Code Indonesia', 'content' => '<div class="announcement-content"><p><strong>Kesempatan magang bergengsi</strong> dengan Google Summer of Code untuk berkontribusi pada proyek open source global.</p><p>💻 <strong>Program benefits:</strong></p><ul><li>💰 Stipend hingga $6,000 USD</li><li>🌍 Mentoring dari developer global</li><li>📜 Certificate dari Google</li><li>🤝 Networking dengan open source community</li></ul><p>🌟 Representasikan Indonesia di komunitas developer dunia!</p></div>'],
                    ['title' => 'Beasiswa Penelitian AI dari Kemenristek/BRIN', 'content' => '<div class="announcement-content"><p><strong>Pendanaan penelitian AI</strong> untuk mahasiswa yang mengembangkan solusi artificial intelligence untuk permasalahan Indonesia.</p><p>🧠 <strong>Research focus areas:</strong></p><ul><li>🌾 Agricultural AI solutions</li><li>🏥 Healthcare AI systems</li><li>🌍 Climate change mitigation</li><li>🏛️ Smart city technologies</li></ul><p>🔬 Jadilah pioneer AI research untuk Indonesia!</p></div>']
                ]
            ],
            'UKM Kemasyarakatan' => [
                'posts' => [
                    ['title' => 'Konservasi Lingkungan Indonesia: Aksi Nyata Mahasiswa', 'content' => '<div class="post-content"><p><strong>Gerakan pelestarian lingkungan</strong> oleh mahasiswa Indonesia untuk mengatasi krisis iklim dan degradasi lingkungan.</p><p>🌍 <strong>Aksi konservasi:</strong></p><ul><li>🌳 Penanaman mangrove di pesisir Indonesia</li><li>♻️ Bank sampah dan daur ulang komunitas</li><li>🐢 Konservasi penyu di pantai Pangumbahan</li><li>🦎 Pelestarian komodo di Pulau Komodo</li></ul><p>🇮🇩 <em>Jaga Indonesia untuk generasi mendatang!</em></p></div>'],
                    ['title' => 'Pemberdayaan UMKM Melalui Digital Marketing', 'content' => '<div class="post-content"><p><strong>Program pemberdayaan UMKM Indonesia</strong> melalui pelatihan digital marketing dan e-commerce untuk meningkatkan daya saing.</p><p>📱 <strong>Materi pelatihan:</strong></p><ul><li>📸 Content creation untuk social media</li><li>🛒 Online marketplace (Tokopedia, Shopee)</li><li>💳 Payment gateway dan QRIS</li><li>📊 Digital marketing analytics</li></ul><p>💪 <em>Berdayakan UMKM untuk ekonomi Indonesia yang kuat!</em></p></div>'],
                    ['title' => 'Literasi Digital untuk Masyarakat Pedesaan', 'content' => '<div class="post-content"><p><strong>Program literasi digital</strong> untuk masyarakat pedesaan Indonesia agar tidak tertinggal dalam era digital.</p><p>📚 <strong>Materi literasi:</strong></p><ul><li>📱 Penggunaan smartphone dan internet</li><li>💳 Mobile banking dan e-wallet</li><li>🏥 Telemedicine dan konsultasi online</li><li>📚 E-learning dan pendidikan online</li></ul><p>🌐 <em>Digital inclusion untuk seluruh rakyat Indonesia!</em></p></div>'],
                    ['title' => 'Relawan Bencana Indonesia: Siaga Untuk Sesama', 'content' => '<div class="post-content"><p><strong>Peran mahasiswa sebagai relawan</strong> dalam penanggulangan bencana alam di Indonesia dan sistem early warning.</p><p>🚨 <strong>Jenis bencana di Indonesia:</strong></p><ul><li>🌋 Erupsi gunung berapi (Merapi, Krakatau)</li><li>🌊 Tsunami dan gempa bumi</li><li>💧 Banjir dan tanah longsor</li><li>🔥 Kebakaran hutan dan lahan</li></ul><p>🤝 <em>Gotong royong dalam menghadapi musibah!</em></p></div>']
                ],
                'events' => [
                    ['title' => 'Bakti Sosial Massal "Indonesia Berbagi" di NTT', 'content' => '<div class="event-content"><p><strong>Program bakti sosial lintas universitas</strong> untuk membantu masyarakat Nusa Tenggara Timur dalam bidang pendidikan, kesehatan, dan infrastruktur.</p><p>❤️ <strong>Program kegiatan:</strong></p><ul><li>🏫 Renovasi sekolah dan perpustakaan</li><li>🏥 Pelayanan kesehatan gratis</li><li>💧 Pembangunan sumur bor dan MCK</li><li>🌱 Pelatihan pertanian organik</li></ul><p>🇮🇩 Wujudkan Indonesia yang merata dan berkeadilan!</p></div>'],
                    ['title' => 'Program Mengajar Indonesia di Daerah 3T', 'content' => '<div class="event-content"><p><strong>Program pengabdian masyarakat</strong> di daerah Terdepan, Terpencil, dan Tertinggal (3T) untuk meningkatkan kualitas pendidikan.</p><p>📚 <strong>Fokus program:</strong></p><ul><li>👩‍🏫 Teaching assistance untuk guru lokal</li><li>💻 Pelatihan teknologi pendidikan</li><li>📖 Program literasi dan numerasi</li><li>🎨 Ekstrakurikuler seni dan olahraga</li></ul><p>🎓 Jadilah agen perubahan pendidikan Indonesia!</p></div>'],
                    ['title' => 'Festival Gotong Royong "Semangat Persatuan Indonesia"', 'content' => '<div class="event-content"><p><strong>Perayaan budaya gotong royong</strong> sebagai nilai luhur bangsa Indonesia melalui berbagai kegiatan sosial kemasyarakatan.</p><p>🤝 <strong>Kegiatan festival:</strong></p><ul><li>🏘️ Kerja bakti massal dan renovasi rumah</li><li>🌳 Penanaman pohon di area publik</li><li>🍛 Dapur umum untuk masyarakat kurang mampu</li><li>🎪 Pentas seni dan budaya daerah</li></ul><p>🇮🇩 Hidupkan kembali semangat gotong royong Pancasila!</p></div>'],
                    ['title' => 'Donor Darah Massal "Tetes Kehidupan untuk Indonesia"', 'content' => '<div class="event-content"><p><strong>Kampanye donor darah nasional</strong> untuk memenuhi kebutuhan stok darah di seluruh Indonesia, terutama daerah yang kekurangan.</p><p>🩸 <strong>Target kegiatan:</strong></p><ul><li>💉 Donor darah 10,000 kantong</li><li>🏥 Distribusi ke 50 rumah sakit</li><li>📚 Edukasi donor darah rutin</li><li>🎖️ Penghargaan donor darah sukarela</li></ul><p>❤️ Selamatkan nyawa sesama dengan setetes darahmu!</p></div>']
                ],
                'announcements' => [
                    ['title' => 'Rekrutmen Relawan Indonesia Teaching di Papua', 'content' => '<div class="announcement-content"><p><strong>Kesempatan menjadi relawan pengajar</strong> di Papua untuk mendukung program pemerataan pendidikan berkualitas di seluruh Indonesia.</p><p>📍 <strong>Lokasi penempatan:</strong></p><ul><li>🏔️ Kabupaten Pegunungan Bintang</li><li>🌊 Kabupaten Asmat</li><li>🏝️ Kabupaten Kepulauan Yapen</li><li>🌿 Kabupaten Mamberamo Tengah</li></ul><p>🎓 Bantu wujudkan Papua cerdas dan berpendidikan!</p></div>'],
                    ['title' => 'Program KKN Tematik Desa Digital Indonesia', 'content' => '<div class="announcement-content"><p><strong>KKN tematik khusus</strong> untuk mengembangkan desa digital dan smart village di berbagai daerah Indonesia.</p><p>💻 <strong>Program digitalisasi:</strong></p><ul><li>🌐 Website desa dan e-government</li><li>📱 Aplikasi pelayanan warga</li><li>💳 QRIS untuk UMKM desa</li><li>📚 E-learning untuk anak desa</li></ul><p>🚀 Jadilah pioneer transformasi digital pedesaan!</p></div>']
                ]
            ],
            'default' => [
                'posts' => [
                    ['title' => 'Pengembangan Soft Skills untuk Mahasiswa Indonesia', 'content' => '<div class="post-content"><p><strong>Pentingnya soft skills</strong> dalam menghadapi dunia kerja dan berkompetisi di era global.</p><p>💡 <strong>Soft skills essential:</strong></p><ul><li>🗣️ Komunikasi bahasa Indonesia dan Inggris</li><li>👥 Leadership dan teamwork</li><li>🧠 Critical thinking dan problem solving</li><li>🎯 Adaptability dan learning agility</li></ul><p>🇮🇩 <em>Siapkan diri untuk menjadi pemimpin masa depan Indonesia!</em></p></div>'],
                    ['title' => 'Membangun Personal Branding di Era Digital', 'content' => '<div class="post-content"><p><strong>Strategi membangun personal branding</strong> yang kuat untuk mahasiswa Indonesia di era digital dan social media.</p><p>📱 <strong>Platform yang efektif:</strong></p><ul><li>💼 LinkedIn untuk professional networking</li><li>📸 Instagram untuk creative showcase</li><li>🐦 Twitter untuk thought leadership</li><li>🎥 YouTube untuk video content</li></ul><p>✨ <em>Tunjukkan potensi terbaikmu kepada dunia!</em></p></div>']
                ],
                'events' => [
                    ['title' => 'Seminar Nasional "Generasi Emas Indonesia 2045"', 'content' => '<div class="event-content"><p><strong>Diskusi strategis</strong> tentang peran generasi muda dalam mewujudkan visi Indonesia Emas 2045.</p><p>🎯 <strong>Tema pembahasan:</strong></p><ul><li>🏭 Revolusi industri 4.0 dan 5.0</li><li>🌱 Sustainable development goals</li><li>🎓 Human capital development</li><li>🚀 Innovation dan entrepreneurship</li></ul><p>🇮🇩 Bersiaplah menjadi generasi emas Indonesia!</p></div>'],
                    ['title' => 'Leadership Camp "Pemimpin Masa Depan Indonesia"', 'content' => '<div class="event-content"><p><strong>Pelatihan kepemimpinan intensif</strong> untuk membentuk karakter pemimpin yang berintegritas dan nasionalis.</p><p>👑 <strong>Materi leadership:</strong></p><ul><li>🧠 Strategic thinking dan decision making</li><li>🤝 Team building dan conflict resolution</li><li>🎯 Vision setting dan goal achievement</li><li>🇮🇩 Nilai-nilai Pancasila dalam kepemimpinan</li></ul><p>🌟 Lead by example, serve the nation!</p></div>']
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
            'posts' => ['feeds/dummy.png'],
            'events' => ['feeds/dummy.png']
        ];
    }
}
