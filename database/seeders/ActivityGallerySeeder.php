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
            $totalCreated += rand(3, 4); // Adding 3-4 special items per UKM
        }

        $this->command->info('ActivityGallery seeder completed successfully!');
        $this->command->info("Created {$totalCreated} activity gallery items for {$ukms->count()} UKMs");
        $this->command->info('Gallery distribution:');
        $this->command->info('- Regular activities: ~' . intval($totalCreated * 0.75) . ' photos');
        $this->command->info('- Special moments: ~' . intval($totalCreated * 0.25) . ' photos (group photos, achievements, collaborations)');
        $this->command->info('Content includes: workshops, competitions, community service, performances, and university events.');
    }

    private function getGalleryCountByCategory($category)
    {
        return match ($category) {
            'Himpunan' => rand(10, 14), // Academic organizations are very active
            'UKM Seni' => rand(12, 18), // Arts groups have lots of visual content
            'UKM Olahraga' => rand(8, 12), // Sports have good action shots
            'UKM Teknologi' => rand(9, 13), // Tech groups document projects well
            'UKM Keilmuan' => rand(7, 10), // Academic groups moderate activity
            'UKM Kemasyarakatan' => rand(10, 14), // Community service groups are very active
            'UKM Keagamaan' => rand(6, 9), // Religious groups moderate documentation
            'UKM Kewirausahaan' => rand(7, 11), // Business groups moderate activity
            'UKM Media' => rand(11, 16), // Media groups document everything
            default => rand(8, 12),
        };
    }

    private function getCategorySpecificCaptions($category, $alias)
    {
        $baseCaptions = [
            'Himpunan' => [
                '<div class="caption-content"><h4>Workshop Full-Stack Development dengan React & Laravel</h4><p>Sesi intensif dengan mentor dari Gojek dan Tokopedia 💻</p></div>',
                '<div class="caption-content"><h4>Seminar Nasional "Teknologi 4.0 untuk Indonesia Emas"</h4><p>Diskusi mendalam dengan praktisi dari BUMN dan startup unicorn 🚀</p></div>',
                '<div class="caption-content"><h4>Study Group Persiapan Sertifikasi Cloud AWS</h4><p>Persiapan intensif menghadapi ujian AWS Solutions Architect 📚</p></div>',
                '<div class="caption-content"><h4>GEMASTIK 2024 - Persiapan Final</h4><p>Tim {alias} latihan keras untuk kompetisi pemrograman nasional 🏆</p></div>',
                '<div class="caption-content"><h4>Tech Talk: "Career Path di Industri Teknologi Indonesia"</h4><p>Sharing session dengan alumni yang bekerja di Google, Microsoft, dan Bukalapak ⭐</p></div>',
                '<div class="caption-content"><h4>Bootcamp Mobile Development Flutter</h4><p>3 hari intensive training membuat aplikasi Android dan iOS 🛠️</p></div>',
                '<div class="caption-content"><h4>Industry Visit ke Jakarta Smart City Lounge</h4><p>Kunjungan edukatif ke pusat teknologi smart city Jakarta 🏢</p></div>',
                '<div class="caption-content"><h4>Code Review Session: Clean Code Practices</h4><p>Peer learning dengan senior developer untuk improve coding skills 🔍</p></div>',
                '<div class="caption-content"><h4>Hackathon 48 Jam: "Solusi Digital untuk UMKM"</h4><p>Marathon coding bersama Kementerian Koperasi dan UKM ⚡</p></div>',
                '<div class="caption-content"><h4>Final Project Showcase Semester Genap</h4><p>Presentasi capstone project terbaik dengan juri dari industri 🎯</p></div>',
                '<div class="caption-content"><h4>Pelatihan UI/UX Design Thinking</h4><p>Workshop design sprint methodology untuk product development 🎨</p></div>',
                '<div class="caption-content"><h4>Kompetisi Internal Algoritma dan Struktur Data</h4><p>Seleksi anggota terbaik untuk mewakili di ICPC Asia Jakarta 🧮</p></div>'
            ],
            'UKM Seni' => [
                '<div class="caption-content"><h4>Pameran Fotografi "Pesona Nusantara dalam Bingkai"</h4><p>Karya dokumentasi keindahan budaya Indonesia dari Sabang sampai Merauke 📷</p></div>',
                '<div class="caption-content"><h4>Konser Musik "Harmoni Nusantara"</h4><p>Kolaborasi musik tradisional dan modern untuk generasi muda 🎸</p></div>',
                '<div class="caption-content"><h4>Workshop Fotografi Street Photography Jakarta</h4><p>Hunting foto kehidupan urban dengan mentor National Geographic Indonesia 💡</p></div>',
                '<div class="caption-content"><h4>Pentas Teater "Sang Pemimpi: Adaptasi Novel Ahmad Fuadi"</h4><p>Drama musikal yang menginspirasi tentang perjuangan pendidikan 🎭</p></div>',
                '<div class="caption-content"><h4>Pameran Seni Rupa "Ekspresi Milenial Indonesia"</h4><p>Karya lukis, patung, dan instalasi art dari mahasiswa se-Jabodetabek 🎨</p></div>',
                '<div class="caption-content"><h4>Open Mic Night: "Suara Anak Bangsa"</h4><p>Panggung terbuka untuk musisi muda berbakat showcase original songs ⭐</p></div>',
                '<div class="caption-content"><h4>Behind The Scene Film Pendek "Bhinneka"</h4><p>Proses kreatif pembuatan film tentang toleransi di Indonesia 🎬</p></div>',
                '<div class="caption-content"><h4>Digital Art Workshop dengan Wacom dan Adobe</h4><p>Masterclass illustration dan motion graphics untuk konten digital 🖥️</p></div>',
                '<div class="caption-content"><h4>Festival Budaya "Wonderful Indonesia"</h4><p>Kolaborasi tari, musik, dan fashion show budaya daerah 🌟</p></div>',
                '<div class="caption-content"><h4>Photo Walk: "Heritage Architecture Jakarta"</h4><p>Dokumentasi bangunan bersejarah di Kota Tua dan sekitarnya 📸</p></div>',
                '<div class="caption-content"><h4>Kolaborasi dengan Balai Bahasa: "Dongeng Digital"</h4><p>Proyek animasi cerita rakyat Nusantara untuk edukasi anak 📚</p></div>',
                '<div class="caption-content"><h4>Pelatihan Fotografi Product untuk UMKM</h4><p>Program CSR mengajarkan skill fotografi untuk wirausaha lokal 📦</p></div>'
            ],
            'UKM Olahraga' => [
                '<div class="caption-content"><h4>Final LIMA Basketball: Lawan Universitas Indonesia</h4><p>Pertandingan sengit memperebutkan juara nasional di GBK Senayan ⚽</p></div>',
                '<div class="caption-content"><h4>Training Camp Persiapan PON Papua 2024</h4><p>TC intensif dengan pelatih nasional untuk seleksi atlet terbaik 💪</p></div>',
                '<div class="caption-content"><h4>Jakarta Marathon 2024: 500 Peserta Universitas</h4><p>Event lari terbesar untuk mempromosikan gaya hidup sehat mahasiswa 🏃‍♂️</p></div>',
                '<div class="caption-content"><h4>Pelatihan Teknik Dasar dengan Pelatih Persija</h4><p>Fundamental football skills bersama coaching staff professional 🎯</p></div>',
                '<div class="caption-content"><h4>Friendly Match vs Tim Mahakarya UGM</h4><p>Uji coba kemampuan sebelum LIMA Regional Jawa 🤝</p></div>',
                '<div class="caption-content"><h4>Universitas Sport Festival 2024</h4><p>16 cabang olahraga, 2000+ peserta dari seluruh fakultas 🏆</p></div>',
                '<div class="caption-content"><h4>Coaching Clinic dengan Atlet Olimpiade Tokyo</h4><p>Sesi khusus mental training dan teknik advanced dari medalis 👨‍🏫</p></div>',
                '<div class="caption-content"><h4>Team Building: Outbound di Puncak Bogor</h4><p>Membangun chemistry tim dan mental juara yang sesungguhnya 🤜🤛</p></div>',
                '<div class="caption-content"><h4>Juara 1 Rektor Cup Se-Jawa Timur</h4><p>Merayakan kemenangan bergengsi tingkat regional 🥇</p></div>',
                '<div class="caption-content"><h4>Latihan Conditioning di Fitness Center</h4><p>Program strength & conditioning dengan alat modern 💯</p></div>',
                '<div class="caption-content"><h4>Turnamen Futsal Ramadan: Berbagi Takjil</h4><p>Kompetisi olahraga sekaligus kegiatan sosial di bulan suci 🕌</p></div>',
                '<div class="caption-content"><h4>Klinik Cedera Olahraga dengan Dokter Tim Nasional</h4><p>Edukasi pencegahan dan penanganan injury untuk atlet 🏥</p></div>'
            ],
            'UKM Teknologi' => [
                '<div class="caption-content"><h4>Kontes Robot Indonesia 2024: Robot "Garuda Nusantara"</h4><p>Demonstrasi robot AGV yang akan berlaga di tingkat nasional 🤖</p></div>',
                '<div class="caption-content"><h4>Innovation Lab: Smart Farming IoT Project</h4><p>R&D sistem monitoring tanaman otomatis untuk petani Indonesia 🔬</p></div>',
                '<div class="caption-content"><h4>Workshop Arduino untuk Smart Home</h4><p>Pembelajaran IoT dan home automation dengan Raspberry Pi 🔧</p></div>',
                '<div class="caption-content"><h4>AI/ML Bootcamp: Computer Vision untuk Healthcare</h4><p>Deep learning dengan TensorFlow untuk deteksi penyakit 🧠</p></div>',
                '<div class="caption-content"><h4>Prototype Testing: Alat Bantu Dengar Digital</h4><p>Uji coba inovasi assistive technology untuk disabilitas ⚗️</p></div>',
                '<div class="caption-content"><h4>Indonesia Innovation Expo di JCC</h4><p>Showcase teknologi mahasiswa di pameran nasional 🚀</p></div>',
                '<div class="caption-content"><h4>Drone Racing Championship Jakarta</h4><p>Kompetisi UAV racing dengan drone custom-built 🚁</p></div>',
                '<div class="caption-content"><h4>3D Printing Workshop: Rapid Prototyping</h4><p>Belajar additive manufacturing untuk product development 🖨️</p></div>',
                '<div class="caption-content"><h4>Assembly Robot Line Follower</h4><p>Proses pembuatan robot dari PCB design hingga programming ⚙️</p></div>',
                '<div class="caption-content"><h4>Smart City Project: Jakarta Traffic Management</h4><p>Implementasi AI untuk optimasi lalu lintas ibukota 🏙️</p></div>',
                '<div class="caption-content"><h4>Kolaborasi dengan BPPT: Teknologi Renewable Energy</h4><p>Riset solar panel efficiency untuk energi terbarukan 🔋</p></div>',
                '<div class="caption-content"><h4>Hackathon BUMN: Solusi Digital Transformation</h4><p>48 jam marathon coding dengan mentoring dari CTO perusahaan BUMN ⚡</p></div>'
            ],
            'UKM Kemasyarakatan' => [
                '<div class="caption-content"><h4>Bakti Sosial Ramadan: 500 Paket Sembako</h4><p>Berbagi kebahagiaan dengan masyarakat prasejahtera di Jakarta Timur ❤️</p></div>',
                '<div class="caption-content"><h4>Program "Guru Digital": Mengajar di Daerah 3T</h4><p>Volunteer teaching IT literacy di Papua dan Maluku 📚</p></div>',
                '<div class="caption-content"><h4>Aksi Bersih Pantai Ancol: #IndonesiaBebasPlastik</h4><p>Environmental action bersama 200+ mahasiswa dan masyarakat 🌍</p></div>',
                '<div class="caption-content"><h4>Santunan Anak Yatim: Program Rutin Jumat Berkah</h4><p>Kepedulian sosial setiap bulan dengan 50 anak asuh binaan 🤲</p></div>',
                '<div class="caption-content"><h4>Donor Darah PMI: Target 200 Kantong</h4><p>Kerjasama dengan Palang Merah Indonesia untuk kemanusiaan 🩸</p></div>',
                '<div class="caption-content"><h4>UMKM Digital Academy: Training E-commerce</h4><p>Pemberdayaan 100+ pedagang kecil untuk go digital via Shopee dan Tokopedia 💼</p></div>',
                '<div class="caption-content"><h4>Posyandu Digital: Cek Kesehatan Gratis</h4><p>Pelayanan kesehatan masyarakat dengan teknologi telemedicine 👶</p></div>',
                '<div class="caption-content"><h4>Festival Kampung Kreatif: "Batik Goes Digital"</h4><p>Apresiasi UMKM batik dengan platform digital marketing 🎪</p></div>',
                '<div class="caption-content"><h4>Program Bedah Rumah: Gotong Royong Membangun</h4><p>Renovasi 10 rumah tidak layak huni di Kampung Akuarium 🏠</p></div>',
                '<div class="caption-content"><h4>Edukasi Bank Sampah: "Sampah Jadi Rupiah"</h4><p>Sosialisasi pengelolaan sampah untuk ekonomi sirkular ♻️</p></div>',
                '<div class="caption-content"><h4>Merdeka Belajar di Desa: Program Kemendesa</h4><p>Kolaborasi dengan pemerintah untuk akses pendidikan di desa 📖</p></div>',
                '<div class="caption-content"><h4>Trauma Healing Post-Disaster: Gempa Cianjur</h4><p>Tim psikososial untuk pemulihan mental anak-anak korban bencana 🫂</p></div>'
            ],
            'UKM Keagamaan' => [
                '<div class="caption-content"><h4>Kajian Rutin: "Islam dan Teknologi Modern"</h4><p>Diskusi bagaimana agama bersinergi dengan perkembangan zaman 📖</p></div>',
                '<div class="caption-content"><h4>Buka Puasa Bersama 1000 Mahasiswa</h4><p>Iftar akbar di masjid kampus dengan menu nusantara 🌙</p></div>',
                '<div class="caption-content"><h4>Pesantren Kilat: "Generasi Qurani di Era Digital"</h4><p>Program intensif 3 hari dengan khatib nasional 🕌</p></div>',
                '<div class="caption-content"><h4>Study Tour Masjid Bersejarah Jawa</h4><p>Wisata religi ke Demak, Kudus, dan Cirebon untuk mengenal sejarah Islam 🚌</p></div>',
                '<div class="caption-content"><h4>Seminar Ekonomi Syariah: "Fintech Halal Indonesia"</h4><p>Diskusi masa depan keuangan syariah dengan pakar dari OJK 💰</p></div>',
                '<div class="caption-content"><h4>Pelatihan Qari-Qariah Tingkat Universitas</h4><p>Persiapan MTQ dengan guru mengaji Al-Azhar 🎵</p></div>',
                '<div class="caption-content"><h4>Aksi Kemanusiaan: Bantuan untuk Palestina</h4><p>Penggalangan dana dan doa bersama untuk saudara di Gaza 🇵🇸</p></div>',
                '<div class="caption-content"><h4>Dialog Lintas Agama: "Toleransi dalam Bhinneka"</h4><p>Diskusi harmoni beragama dengan pemuka agama se-Jakarta 🤝</p></div>'
            ],
            'UKM Kewirausahaan' => [
                '<div class="caption-content"><h4>Business Plan Competition: "Startup for Indonesia"</h4><p>Kompetisi ide bisnis dengan hadiah seed funding 10 juta 💡</p></div>',
                '<div class="caption-content"><h4>Mentoring Session dengan Founder Gojek</h4><p>Sharing experience membangun unicorn startup Indonesia 🚀</p></div>',
                '<div class="caption-content"><h4>UMKM Expo: "Proudly Made by Students"</h4><p>Showcase 50+ produk mahasiswa dari F&B sampai tech 🛍️</p></div>',
                '<div class="caption-content"><h4>Workshop Digital Marketing untuk Generasi Z</h4><p>Strategy content creation dan social media marketing 📱</p></div>',
                '<div class="caption-content"><h4>Investor Pitch Day: "Demo Day Batch 3"</h4><p>Presentasi startup mahasiswa kepada angel investor 💼</p></div>',
                '<div class="caption-content"><h4>Study Visit ke Silicon Valley Indonesia (BSD)</h4><p>Kunjungan ke ekosistem startup dan tech companies 🏢</p></div>',
                '<div class="caption-content"><h4>E-commerce Training: "Jualan Online Era New Normal"</h4><p>Practical guide selling di Shopee, Tokopedia, dan Instagram 📦</p></div>',
                '<div class="caption-content"><h4>Financial Literacy: "Kelola Uang ala Milenial Cerdas"</h4><p>Workshop investment dan budgeting untuk mahasiswa 💰</p></div>'
            ]
        ];

        return $baseCaptions[$category] ?? [
            '<div class="caption-content"><h4>Rapat Koordinasi Bulanan</h4><p>Evaluasi program dan planning kegiatan periode mendatang 📅</p></div>',
            '<div class="caption-content"><h4>Leadership Training: "Pemimpin Masa Depan Indonesia"</h4><p>Pengembangan karakter kepemimpinan yang berintegritas 💪</p></div>',
            '<div class="caption-content"><h4>Seminar Motivasi dengan Successful Alumni</h4><p>Inspirasi dari kakak senior yang telah sukses di bidangnya ✨</p></div>',
            '<div class="caption-content"><h4>Team Building: "One Team, One Dream"</h4><p>Mempererat kebersamaan keluarga besar organisasi 🤝</p></div>',
            '<div class="caption-content"><h4>Program Pengabdian Masyarakat</h4><p>Kontribusi nyata mahasiswa untuk pembangunan bangsa ❤️</p></div>',
            '<div class="caption-content"><h4>Educational Tour: "Belajar dari Sejarah Bangsa"</h4><p>Kunjungan ke museum dan situs bersejarah Indonesia 🏛️</p></div>',
            '<div class="caption-content"><h4>Workshop Soft Skills: "Character Building"</h4><p>Pengembangan karakter dan etika professional 👑</p></div>',
            '<div class="caption-content"><h4>Cultural Exchange Program ASEAN</h4><p>Pertukaran budaya dengan mahasiswa dari negara tetangga 🌍</p></div>',
            '<div class="caption-content"><h4>Innovation Challenge: "Solusi untuk Indonesia"</h4><p>Brainstorming ide kreatif untuk memajukan bangsa 💡</p></div>',
            '<div class="caption-content"><h4>Celebration Night: "Prestasi Membanggakan"</h4><p>Syukuran atas pencapaian terbaik di tingkat nasional 🏆</p></div>'
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
            'workshop' => ['(Hari ke-1 dari 3)', '(Sesi pagi jam 08.00)', '(Weekend intensive)', '(Batch angkatan 2024)', '(Sesi lanjutan)'],
            'competition' => ['(Final round)', '(Semifinal)', '(Penyisihan)', '(Grand finale)', '(Babak eliminasi)'],
            'training' => ['(Latihan rutin)', '(Persiapan LIMA)', '(TC intensif)', '(Fundamental)', '(Level advanced)'],
            'exhibition' => ['(Soft opening)', '(Hari kedua)', '(Malam penutupan)', '(Peak hours)', '(Preview eksklusif)'],
            'service' => ['(Minggu ke-2)', '(Program perdana)', '(Follow-up)', '(Evaluasi)', '(Fase implementasi)'],
            'seminar' => ['(Keynote session)', '(Panel diskusi)', '(Q&A session)', '(Networking time)', '(Closing ceremony)'],
            'bootcamp' => ['(Day 1 of 5)', '(Intensive week)', '(Final project)', '(Demo day)', '(Graduation)'],
            'performance' => ['(Dress rehearsal)', '(Opening night)', '(Matinee show)', '(Final show)', '(Encore performance)'],
            'lab' => ['(Prototype phase)', '(Testing stage)', '(Debug session)', '(Integration test)', '(UAT phase)'],
            'match' => ['(First half)', '(Extra time)', '(Penalty shootout)', '(Final whistle)', '(Post-match analysis)']
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
        return 'activity_galleries/dummy.png';
    }

    private function createSpecialGalleryItems($ukm)
    {
        $specialItems = [
            [
                'type' => 'group_photo',
                'caption' => "<div class='caption-content'><h4>Foto Bersama Keluarga Besar {$ukm->alias} 2024</h4><p>Satu jiwa, satu tekad, satu tujuan untuk memajukan Indonesia! 🇮🇩</p></div>",
                'days_ago' => rand(30, 90)
            ],
            [
                'type' => 'achievement',
                'caption' => "<div class='caption-content'><h4>Moment Bersejarah: Prestasi Tingkat Nasional</h4><p>{$ukm->alias} mengharumkan nama universitas di kompetisi bergengsi Indonesia 🏆</p></div>",
                'days_ago' => rand(60, 180)
            ],
            [
                'type' => 'behind_scenes',
                'caption' => "<div class='caption-content'><h4>Behind The Scenes: Persiapan Event Besar</h4><p>Kerja keras tim organizing committee demi kesuksesan acara nasional 💪</p></div>",
                'days_ago' => rand(15, 45)
            ],
            [
                'type' => 'collaboration',
                'caption' => "<div class='caption-content'><h4>Kolaborasi Lintas UKM: Satu Indonesia</h4><p>Bersatu dalam keberagaman untuk menciptakan karya terbaik bangsa 🤝</p></div>",
                'days_ago' => rand(45, 120)
            ],
            [
                'type' => 'community_impact',
                'caption' => "<div class='caption-content'><h4>Dampak Nyata untuk Masyarakat</h4><p>Program {$ukm->alias} memberikan manfaat langsung bagi rakyat Indonesia ❤️</p></div>",
                'days_ago' => rand(20, 60)
            ]
        ];

        // Select 3-4 special items randomly
        $selectedItems = array_slice($specialItems, 0, rand(3, 4));

        foreach ($selectedItems as $item) {
            $createdDate = Carbon::now()->subDays($item['days_ago']);

            ActivityGallery::create([
                'unit_kegiatan_id' => $ukm->id,
                'image' => 'activity_galleries/dummy.png',
                'caption' => $item['caption'],
                'created_at' => $createdDate,
                'updated_at' => $createdDate->copy()->addHours(rand(1, 12)),
            ]);
        }
    }
}
