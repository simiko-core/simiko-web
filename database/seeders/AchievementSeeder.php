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

        $this->command->info('Creating comprehensive achievements for each UKM...');

        $totalCreated = 0;

        // Create achievements for each UKM
        foreach ($ukms as $ukm) {
            $this->command->info("Creating achievements for {$ukm->name} ({$ukm->alias})...");

            $achievements = $this->getAchievementsByCategory($ukm->category, $ukm->alias);
            $achievementCount = $this->getAchievementCount($ukm->category);

            // Select random achievements from the category pool
            $selectedAchievements = collect($achievements)->random(min($achievementCount, count($achievements)));

            foreach ($selectedAchievements as $achievement) {
                $createdDate = $this->getRealisticAchievementDate($achievement['level']);

                Achievement::create([
                    'unit_kegiatan_id' => $ukm->id,
                    'title' => $achievement['title'],
                    'description' => $achievement['description'],
                    'image' => 'achievements/dummy.png',
                    'created_at' => $createdDate,
                    'updated_at' => $createdDate->copy()->addDays(rand(1, 7)),
                ]);

                $totalCreated++;
            }

            // Create some collaborative achievements (multi-UKM)
            if (rand(0, 3) === 0) { // 25% chance
                $this->createCollaborativeAchievement($ukm);
                $totalCreated++;
            }
        }

        // Create some university-wide achievements
        $this->createUniversityAchievements($ukms);
        $totalCreated += 4;

        $this->command->info('Achievement seeder completed successfully!');
        $this->command->info("Created {$totalCreated} achievements across {$ukms->count()} UKMs");
        $this->command->info('Achievement distribution:');
        $this->command->info('- Individual UKM achievements: ' . ($totalCreated - 4 - ($ukms->count() * 0.25)));
        $this->command->info('- Collaborative achievements: ~' . intval($ukms->count() * 0.25));
        $this->command->info('- University-wide achievements: 4');
        $this->command->info('Achievement types include: competitions, innovations, recognitions, collaborations, and community impact.');
    }

    private function getAchievementCount($category)
    {
        return match ($category) {
            'Himpunan' => rand(4, 6), // Academic organizations tend to have more formal achievements
            'UKM Seni' => rand(5, 7), // Arts groups showcase more creative achievements
            'UKM Olahraga' => rand(4, 6), // Sports focus on competition achievements
            'UKM Teknologi' => rand(4, 6), // Tech groups have innovation achievements
            'UKM Kemasyarakatan' => rand(3, 5), // Community service varies
            'UKM Keagamaan' => rand(2, 4), // Religious organizations
            'UKM Kewirausahaan' => rand(3, 5), // Entrepreneurship groups
            default => rand(3, 5),
        };
    }

    private function getAchievementsByCategory($category, $alias)
    {
        $achievements = [
            'Himpunan' => [
                [
                    'title' => 'Juara 1 Kompetisi Pemrograman GEMASTIK XXXVII 2024',
                    'description' => "<div class='achievement-desc'><p><strong>Tim {$alias} berhasil meraih juara pertama</strong> dalam Gemastik (Pagelaran Mahasiswa Nasional Bidang TIK) kategori <em>Software Development</em> tingkat nasional.</p><p>🏆 Mengalahkan <strong>200+ tim</strong> dari seluruh universitas di Indonesia dengan solusi aplikasi smart learning.</p></div>",
                    'type' => 'competition',
                    'level' => 'national'
                ],
                [
                    'title' => 'Best Innovation Award - Technopreneur Competition ITS 2024',
                    'description' => "<div class='achievement-desc'><p><strong>Ide startup EdTech dari {$alias} meraih</strong> penghargaan <em>Best Innovation Award</em> dalam Technopreneur Competition ITS 2024.</p><p>💡 Solusi platform belajar adaptif mendapat apresiasi tinggi dari praktisi Gojek, Tokopedia, dan Bukalapak.</p></div>",
                    'type' => 'innovation',
                    'level' => 'national'
                ],
                [
                    'title' => 'Sertifikasi ABET untuk Program Studi Teknik Informatika',
                    'description' => "<div class='achievement-desc'><p><strong>Kontribusi aktif {$alias}</strong> dalam membantu program studi meraih sertifikasi <strong>ABET (Accreditation Board for Engineering and Technology)</strong>.</p><p>📈 Pencapaian ini menempatkan prodi setara dengan standar internasional MIT dan Stanford.</p></div>",
                    'type' => 'certification',
                    'level' => 'international'
                ],
                [
                    'title' => 'Juara 2 Hackathon Digital Indonesia Awards 2024',
                    'description' => "<div class='achievement-desc'><p><strong>Tim {$alias} meraih posisi runner-up</strong> dalam Hackathon Digital Indonesia Awards dengan tema 'Smart Government Solutions'.</p><p>🏙️ Aplikasi e-government yang dikembangkan telah diimplementasikan di Pemkot Surabaya dan Bandung.</p></div>",
                    'type' => 'competition',
                    'level' => 'national'
                ],
                [
                    'title' => 'Outstanding Academic Performance Recognition',
                    'description' => "<div class='achievement-desc'><p><strong>{$alias} meraih pengakuan</strong> <em>Outstanding Academic Performance</em> dari Kemendikbudristek karena:</p><ul><li>📚 IPK rata-rata anggota 3.7 (tertinggi di fakultas)</li><li>🎓 Tingkat kelulusan cum laude mencapai 85%</li><li>🔬 Publikasi ilmiah mahasiswa terbanyak</li></ul></div>",
                    'type' => 'academic',
                    'level' => 'national'
                ],
                [
                    'title' => 'Finalis Indonesia ICT Awards 2024',
                    'description' => "<div class='achievement-desc'><p><strong>Aplikasi mobile {$alias}</strong> masuk finalis <em>Indonesia ICT Awards</em> kategori Smart Education.</p><p>📱 Aplikasi pembelajaran bahasa daerah dengan AI telah diunduh <strong>100K+ pengguna</strong> di seluruh nusantara.</p></div>",
                    'type' => 'innovation',
                    'level' => 'national'
                ]
            ],
            'UKM Seni' => [
                [
                    'title' => 'Juara 1 Festival Film Pendek Indonesia (FFI) 2024',
                    'description' => "<div class='achievement-desc'><p><strong>Film pendek karya anggota {$alias} meraih juara pertama</strong> dalam Festival Film Indonesia kategori <em>Film Pendek Mahasiswa</em>.</p><p>🎬 Film berdurasi 15 menit dengan tema 'Bhinneka Tunggal Ika Modern' mendapat standing ovation dari juri internasional.</p></div>",
                    'type' => 'competition',
                    'level' => 'national'
                ],
                [
                    'title' => 'Pameran Kolektif di Museum Nasional Indonesia',
                    'description' => "<div class='achievement-desc'><p><strong>{$alias} mendapat kehormatan</strong> menggelar pameran kolektif di <em>Museum Nasional Indonesia</em> dengan tema 'Seni Digital Nusantara'.</p><p>🎨 Menampilkan <strong>75+ karya digital art</strong> yang memadukan motif tradisional dengan teknologi NFT dan augmented reality.</p></div>",
                    'type' => 'exhibition',
                    'level' => 'national'
                ],
                [
                    'title' => 'Best Cultural Performance - ASEAN Youth Festival',
                    'description' => "<div class='achievement-desc'><p><strong>Pertunjukan tari kontemporer {$alias}</strong> meraih <em>Best Cultural Performance</em> di ASEAN Youth Festival Bangkok 2024.</p><p>🎭 Penampilan 'Metamorfosis Garuda' yang memadukan tari Saman dan breakdance mendapat apresiasi luar biasa.</p></div>",
                    'type' => 'performance',
                    'level' => 'international'
                ],
                [
                    'title' => 'Artist Residency Program - Wonderful Indonesia',
                    'description' => "<div class='achievement-desc'><p><strong>Lima anggota {$alias} terpilih</strong> mengikuti <em>Artist Residency Program</em> Wonderful Indonesia di Yogyakarta dan Bali.</p><p>🌏 Berkolaborasi dengan seniman internasional dalam proyek dokumentasi budaya Indonesia untuk promosi pariwisata.</p></div>",
                    'type' => 'residency',
                    'level' => 'national'
                ],
                [
                    'title' => 'Penghargaan Pemuda Pelopor Bidang Seni dan Budaya',
                    'description' => "<div class='achievement-desc'><p><strong>Ketua {$alias} menerima</strong> penghargaan <em>Pemuda Pelopor</em> dari Kemenpora RI bidang Seni dan Budaya.</p><p>🏛️ Berkat dedikasi dalam melestarikan dan mengembangkan seni tradisional melalui platform digital modern.</p></div>",
                    'type' => 'recognition',
                    'level' => 'national'
                ],
                [
                    'title' => 'Kolaborasi dengan Balai Bahasa Kemendikbud',
                    'description' => "<div class='achievement-desc'><p><strong>Proyek dokumentasi {$alias}</strong> bekerja sama dengan Balai Bahasa Kemendikbud untuk digitalisasi cerita rakyat Nusantara.</p><p>📚 Berhasil mengkonversi <strong>50+ cerita rakyat</strong> menjadi animasi dan podcast yang viral di media sosial.</p></div>",
                    'type' => 'collaboration',
                    'level' => 'national'
                ]
            ],
            'UKM Olahraga' => [
                [
                    'title' => 'Juara 1 LIMA Basketball Putra Divisi Utama 2024',
                    'description' => "<div class='achievement-desc'><p><strong>Tim basket putra {$alias} berhasil meraih juara pertama</strong> di <em>Liga Mahasiswa (LIMA) Basketball Divisi Utama 2024</em>.</p><p>🏀 Mengalahkan Universitas Indonesia di final dengan skor dramatis 78-76 setelah overtime yang menegangkan.</p></div>",
                    'type' => 'competition',
                    'level' => 'national'
                ],
                [
                    'title' => 'Atlet Berprestasi PON Papua XXI 2024',
                    'description' => "<div class='achievement-desc'><p><strong>Alumni {$alias} meraih medali emas</strong> dalam Pekan Olahraga Nasional (PON) XXI Papua cabang atletik nomor lari marathon.</p><p>🥇 Prestasi ini sekaligus membuka peluang seleksi untuk Asian Games 2026 di Nagoya, Jepang.</p></div>",
                    'type' => 'individual',
                    'level' => 'national'
                ],
                [
                    'title' => 'Fair Play Award - Turnamen Sepak Bola Rektor Cup',
                    'description' => "<div class='achievement-desc'><p><strong>Tim sepak bola {$alias} meraih</strong> <em>Fair Play Award</em> dalam Rektor Cup 2024 se-Jawa Timur.</p><p>⚽ Prestasi luar biasa: tidak pernah mendapat kartu kuning selama 8 pertandingan - menunjukkan sportivitas tinggi!</p></div>",
                    'type' => 'sportsmanship',
                    'level' => 'regional'
                ],
                [
                    'title' => 'Program Olahraga Inklusi Terbaik Indonesia',
                    'description' => "<div class='achievement-desc'><p><strong>Program pembinaan olahraga inklusi {$alias}</strong> meraih penghargaan dari Kemenpora RI dan NPC Indonesia.</p><p>♿ Berhasil membina <strong>25 atlet disabilitas</strong> yang kini berkompetisi di tingkat nasional dan internasional.</p></div>",
                    'type' => 'development',
                    'level' => 'national'
                ],
                [
                    'title' => 'Finisher Marathon Borobudur 2024',
                    'description' => "<div class='achievement-desc'><p><strong>Seluruh 30 anggota {$alias}</strong> berhasil menyelesaikan <em>Borobudur Marathon 2024</em> dengan kategori half marathon.</p><p>🏃‍♂️ Pencapaian kolektif ini menunjukkan komitmen tinggi terhadap gaya hidup sehat dan semangat pantang menyerah.</p></div>",
                    'type' => 'endurance',
                    'level' => 'national'
                ],
                [
                    'title' => 'Juara Umum POPNAS (Pekan Olahraga Pelajar Nasional)',
                    'description' => "<div class='achievement-desc'><p><strong>Alumni pembinaan {$alias}</strong> berhasil mengantarkan provinsal meraih juara umum POPNAS 2024.</p><p>🏆 Kontribusi pelatihan dasar yang solid menghasilkan <strong>15 medali emas</strong> dari berbagai cabang olahraga.</p></div>",
                    'type' => 'coaching',
                    'level' => 'national'
                ]
            ],
            'UKM Teknologi' => [
                [
                    'title' => 'Juara 1 Kontes Robot Indonesia (KRI) 2024',
                    'description' => "<div class='achievement-desc'><p><strong>Robot AGV buatan {$alias} meraih juara pertama</strong> dalam Kontes Robot Indonesia (KRI) 2024 kategori <em>Autonomous Ground Vehicle</em>.</p><p>🤖 Robot 'Garuda Nusantara' berhasil menyelesaikan misi logistik dengan akurasi <strong>100%</strong> dan waktu tercepat!</p></div>",
                    'type' => 'competition',
                    'level' => 'national'
                ],
                [
                    'title' => 'Paten Granted - Sistem IoT Smart Farming Indonesia',
                    'description' => "<div class='achievement-desc'><p><strong>Inovasi Smart Farming System</strong> karya {$alias} berhasil mendapat paten dari <em>Direktorat Jenderal Kekayaan Intelektual Kemenkumham RI</em>.</p><p>🌱 Sistem monitoring tanaman otomatis telah diimplementasikan di <strong>50+ lahan petani</strong> di Jawa Tengah dan Jawa Timur.</p></div>",
                    'type' => 'patent',
                    'level' => 'national'
                ],
                [
                    'title' => 'Startup Unicorn Pathway - Program 1000 Digital Startup',
                    'description' => "<div class='achievement-desc'><p><strong>Startup AgriTech {$alias}</strong> terpilih dalam <em>Program 1000 Digital Startup</em> Kemkominfo RI dan mendapat pendanaan tahap awal.</p><p>💰 Memperoleh seed funding <strong>Rp 2.5 miliar</strong> untuk pengembangan platform marketplace hasil tani digital.</p></div>",
                    'type' => 'startup',
                    'level' => 'national'
                ],
                [
                    'title' => 'Juara 2 ASEAN Data Science Explorers 2024',
                    'description' => "<div class='achievement-desc'><p><strong>Tim Data Science {$alias}</strong> meraih posisi runner-up dalam kompetisi <em>ASEAN Data Science Explorers</em> yang diselenggarakan SAP.</p><p>📊 Solusi AI untuk prediksi cuaca dan mitigasi bencana alam mendapat apresiasi tinggi dari juri internasional.</p></div>",
                    'type' => 'competition',
                    'level' => 'international'
                ],
                [
                    'title' => 'Google Developer Student Clubs Lead Indonesia',
                    'description' => "<div class='achievement-desc'><p><strong>Anggota {$alias} terpilih</strong> sebagai <em>Google Developer Student Clubs Lead</em> untuk Indonesia periode 2024-2025.</p><p>🐧 Memimpin komunitas developer mahasiswa terbesar di Indonesia dengan <strong>10,000+ anggota</strong> dari 200+ universitas.</p></div>",
                    'type' => 'leadership',
                    'level' => 'international'
                ],
                [
                    'title' => 'Microsoft Imagine Cup Asia Pacific Finalist',
                    'description' => "<div class='achievement-desc'><p><strong>Aplikasi HealthTech {$alias}</strong> masuk finalis <em>Microsoft Imagine Cup Asia Pacific 2024</em> kategori Health & Life Sciences.</p><p>🏥 Aplikasi telemedicine untuk daerah 3T (Terdepan, Terpencil, Tertinggal) mendapat mentoring langsung dari Microsoft Azure team.</p></div>",
                    'type' => 'innovation',
                    'level' => 'international'
                ]
            ],
            'UKM Kemasyarakatan' => [
                [
                    'title' => 'Kalpataru Award Kategori Pelopor Lingkungan 2024',
                    'description' => "<div class='achievement-desc'><p><strong>Program konservasi mangrove {$alias}</strong> meraih penghargaan <em>Kalpataru</em> dari Presiden RI kategori Pelopor Lingkungan 2024.</p><p>🌳 Berhasil merehabilitasi <strong>100 hektar</strong> ekosistem mangrove di pesisir pantai utara Jawa dan melibatkan <strong>20 desa</strong> nelayan.</p></div>",
                    'type' => 'environment',
                    'level' => 'national'
                ],
                [
                    'title' => 'Duta Pemuda Anti Narkoba Kemenkopolhukam RI',
                    'description' => "<div class='achievement-desc'><p><strong>Ketua {$alias} ditunjuk</strong> sebagai <em>Duta Pemuda Anti Narkoba</em> oleh Kemenkopolhukam RI periode 2024-2026.</p><p>🚫 Program penyuluhan dan pencegahan narkoba telah menjangkau <strong>50+ sekolah</strong> dan <strong>10,000+ pelajar</strong> se-Jawa Timur.</p></div>",
                    'type' => 'appointment',
                    'level' => 'national'
                ],
                [
                    'title' => 'Program Desa Digital Terbaik Kemendesa PDTT',
                    'description' => "<div class='achievement-desc'><p><strong>Inisiatif Desa Digital {$alias}</strong> meraih predikat terbaik nasional dari Kemendesa PDTT RI tahun 2024.</p><p>📱 Transformasi digital 5 desa di Jawa Tengah meningkatkan UMKM lokal hingga <strong>300%</strong> dan membuka akses pasar online.</p></div>",
                    'type' => 'development',
                    'level' => 'national'
                ],
                [
                    'title' => 'Rapid Response Team Bencana BNPB Recognition',
                    'description' => "<div class='achievement-desc'><p><strong>Tim Tanggap Darurat {$alias}</strong> mendapat pengakuan khusus dari <em>BNPB (Badan Nasional Penanggulangan Bencana)</em>.</p><p>🚨 Respons tercepat saat gempa Cianjur: membantu evakuasi <strong>2,000+ warga</strong> dan distribusi bantuan dalam 24 jam pertama.</p></div>",
                    'type' => 'disaster_relief',
                    'level' => 'national'
                ],
                [
                    'title' => 'Anugerah Peduli Pendidikan Kemendikbudristek',
                    'description' => "<div class='achievement-desc'><p><strong>Program 'Sekolah Impian' {$alias}</strong> meraih <em>Anugerah Peduli Pendidikan</em> dari Kemendikbudristek RI.</p><p>📚 Membangun <strong>10 perpustakaan desa</strong> dan memberikan beasiswa kepada <strong>200+ anak</strong> dari keluarga prasejahtera.</p></div>",
                    'type' => 'education',
                    'level' => 'national'
                ],
                [
                    'title' => 'Social Innovation Challenge Winner - UNDP Indonesia',
                    'description' => "<div class='achievement-desc'><p><strong>Solusi pemberdayaan perempuan {$alias}</strong> memenangkan <em>Social Innovation Challenge</em> UNDP Indonesia 2024.</p><p>👩 Platform e-commerce khusus produk UMKM perempuan telah memberdayakan <strong>500+ wirausaha perempuan</strong> di Indonesia Timur.</p></div>",
                    'type' => 'innovation',
                    'level' => 'international'
                ]
            ]
        ];

        return $achievements[$category] ?? [
            [
                'title' => 'Excellence in Student Leadership Award 2024',
                'description' => "<div class='achievement-desc'><p><strong>Anggota {$alias} menunjukkan kepemimpinan luar biasa</strong> dalam berbagai kegiatan kemahasiswaan tingkat universitas.</p><p>👑 Mendapat pengakuan dari Rektor atas dedikasi dan kontribusi dalam mengembangkan soft skills mahasiswa.</p></div>",
                'type' => 'leadership',
                'level' => 'university'
            ],
            [
                'title' => 'Active Participation Award Kemendikbudristek',
                'description' => "<div class='achievement-desc'><p><strong>{$alias} mendapat penghargaan</strong> <em>Active Participation Award</em> dari Kemendikbudristek karena konsistensi tinggi dalam kegiatan kemahasiswaan.</p><p>⭐ Menjadi contoh organisasi mahasiswa berprestasi selama <strong>3 tahun berturut-turut</strong>.</p></div>",
                'type' => 'participation',
                'level' => 'national'
            ]
        ];
    }

    private function getRealisticAchievementDate($level)
    {
        // Different levels of achievement happen at different times
        $daysAgo = match ($level) {
            'international' => rand(60, 365), // International achievements are rarer and older
            'national' => rand(30, 180), // National achievements are significant events
            'regional' => rand(14, 120), // Regional achievements are more frequent
            'university' => rand(7, 90), // University level are most recent
            'institutional' => rand(90, 365), // Institutional changes take time
            default => rand(30, 180),
        };

        return Carbon::now()->subDays($daysAgo);
    }

    private function generateAchievementImagePath($type, $alias)
    {
        return 'achievements/dummy.png';
    }

    private function createCollaborativeAchievement($ukm)
    {
        $collaborativeAchievements = [
            [
                'title' => 'Proyek Kolaborasi Multi-UKM: Smart Campus Innovation',
                'description' => "<div class='achievement-desc'><p><strong>Proyek kolaborasi 5 UKM</strong> yang dipimpin {$ukm->alias} berhasil mengembangkan sistem <em>Smart Campus</em> terintegrasi.</p><p>💡 Mendapat pendanaan <strong>Rp 500 juta</strong> dari <em>Hibah Kedaireka Kemendikbudristek</em> untuk implementasi di 10 universitas.</p></div>",
                'type' => 'collaboration'
            ],
            [
                'title' => 'Juara 1 Cross-Faculty Innovation Challenge 2024',
                'description' => "<div class='achievement-desc'><p><strong>Tim lintas fakultas yang dikoordinasi {$ukm->alias}</strong> meraih juara pertama <em>Cross-Faculty Innovation Challenge</em> Kemendikbudristek.</p><p>🎓 Solusi AI untuk deteksi dini stunting mendapat apresiasi dari Kemenkes dan akan diimplementasikan nasional.</p></div>",
                'type' => 'cross_faculty'
            ],
            [
                'title' => 'Koordinator Utama ASEAN Student Exchange Program',
                'description' => "<div class='achievement-desc'><p><strong>{$ukm->alias} ditunjuk sebagai koordinator utama</strong> program <em>ASEAN Student Exchange</em> untuk wilayah Indonesia periode 2024-2025.</p><p>🌏 Memfasilitasi pertukaran <strong>150+ mahasiswa</strong> dengan universitas top di Thailand, Malaysia, Singapura, dan Vietnam.</p></div>",
                'type' => 'exchange_leadership'
            ],
            [
                'title' => 'Best Collaborative Project - Indonesia Student Awards',
                'description' => "<div class='achievement-desc'><p><strong>Inisiatif kolaboratif {$ukm->alias}</strong> dengan 3 UKM lain meraih <em>Best Collaborative Project</em> dalam Indonesia Student Awards 2024.</p><p>🏆 Program 'Desa Digital Nusantara' telah mentransformasi <strong>25 desa</strong> di 5 provinsi menjadi desa mandiri teknologi.</p></div>",
                'type' => 'collaborative_project'
            ],
            [
                'title' => 'Partnership Program dengan BUMN Indonesia',
                'description' => "<div class='achievement-desc'><p><strong>Konsorsium UKM yang dipimpin {$ukm->alias}</strong> menjalin kemitraan strategis dengan PLN, Telkom, dan Pertamina.</p><p>🏢 Program magang dan riset bersama memberikan kesempatan <strong>100+ mahasiswa</strong> per tahun untuk pengalaman industri.</p></div>",
                'type' => 'partnership'
            ]
        ];

        $achievement = $collaborativeAchievements[array_rand($collaborativeAchievements)];
        $createdDate = Carbon::now()->subDays(rand(30, 120));

        Achievement::create([
            'unit_kegiatan_id' => $ukm->id,
            'title' => $achievement['title'],
            'description' => $achievement['description'],
            'image' => 'achievements/dummy.png',
            'created_at' => $createdDate,
            'updated_at' => $createdDate->copy()->addDays(rand(1, 14)),
        ]);
    }

    private function createUniversityAchievements($ukms)
    {
        $universityAchievements = [
            [
                'title' => 'Peringkat 1 Klasterisasi Kemendikbudristek 2024',
                'description' => '<div class="achievement-desc"><p><strong>Universitas meraih peringkat pertama</strong> dalam <em>Klasterisasi Perguruan Tinggi</em> kategori Universitas Utama dari Kemendikbudristek RI.</p><p>🎯 Pencapaian ini tidak lepas dari kontribusi luar biasa ekosistem UKM dalam mengembangkan inovasi dan prestasi mahasiswa.</p></div>',
                'ukm' => $ukms->random()
            ],
            [
                'title' => 'Akreditasi Unggul Program Kemahasiswaan BAN-PT',
                'description' => '<div class="achievement-desc"><p><strong>Program kemahasiswaan universitas</strong> meraih <em>akreditasi Unggul</em> dari BAN-PT dengan skor tertinggi se-Indonesia.</p><p>🏆 Khususnya untuk bidang pengembangan soft skills melalui organisasi kemahasiswaan yang dinamis dan berprestasi tingkat nasional-internasional.</p></div>',
                'ukm' => $ukms->random()
            ],
            [
                'title' => 'Top 5 Most Innovative Universities SINTA Kemristekdikti',
                'description' => '<div class="achievement-desc"><p><strong>Ekosistem inovasi universitas</strong> masuk dalam <em>Top 5 Most Innovative Universities</em> versi SINTA Kemristekdikti tahun 2024.</p><p>📊 Dengan <strong>500+ publikasi ilmiah mahasiswa</strong>, <strong>100+ paten</strong>, dan <strong>50+ startup</strong> yang dihasilkan melalui program UKM.</p></div>',
                'ukm' => $ukms->random()
            ],
            [
                'title' => 'Penghargaan Kampus Merdeka Terbaik Indonesia',
                'description' => '<div class="achievement-desc"><p><strong>Universitas meraih penghargaan</strong> <em>Kampus Merdeka Terbaik Indonesia</em> dari Kemendikbudristek kategori implementasi program MBKM.</p><p>🎓 Tingkat partisipasi mahasiswa dalam program MBKM mencapai <strong>95%</strong> dengan dukungan penuh dari seluruh UKM.</p></div>',
                'ukm' => $ukms->random()
            ]
        ];

        foreach ($universityAchievements as $achievement) {
            $createdDate = Carbon::now()->subDays(rand(60, 200));

            Achievement::create([
                'unit_kegiatan_id' => $achievement['ukm']->id,
                'title' => $achievement['title'],
                'description' => $achievement['description'],
                'image' => 'achievements/dummy.png',
                'created_at' => $createdDate,
                'updated_at' => $createdDate->copy()->addDays(rand(1, 30)),
            ]);
        }
    }
}
