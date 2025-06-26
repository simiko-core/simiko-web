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
                    'image' => $this->generateAchievementImagePath($achievement['type'], $ukm->alias),
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
        $totalCreated += 3;

        $this->command->info('Achievement seeder completed successfully!');
        $this->command->info("Created {$totalCreated} achievements across {$ukms->count()} UKMs");
    }

    private function getAchievementCount($category)
    {
        return match ($category) {
            'Himpunan' => rand(3, 5), // Academic organizations tend to have more formal achievements
            'UKM Seni' => rand(4, 6), // Arts groups showcase more creative achievements
            'UKM Olahraga' => rand(3, 5), // Sports focus on competition achievements
            'UKM Teknologi' => rand(3, 5), // Tech groups have innovation achievements
            'UKM Kemasyarakatan' => rand(2, 4), // Community service varies
            default => rand(2, 4),
        };
    }

    private function getAchievementsByCategory($category, $alias)
    {
        $achievements = [
            'Himpunan' => [
                [
                    'title' => 'Juara 1 Kompetisi Programming Nasional 2024',
                    'description' => "<div class='achievement-desc'><p><strong>Tim {$alias} berhasil meraih juara pertama</strong> dalam kompetisi programming tingkat nasional dengan mengalahkan <strong>150+ tim</strong> dari seluruh Indonesia.</p><p>🏆 Prestasi ini membuktikan keunggulan kemampuan <em>coding</em> dan <em>problem solving</em> anggota.</p></div>",
                    'type' => 'competition',
                    'level' => 'national'
                ],
                [
                    'title' => 'Best Innovation Award - Tech Startup Competition',
                    'description' => "<div class='achievement-desc'><p><strong>Ide inovasi dari {$alias} berhasil meraih</strong> penghargaan <em>Best Innovation Award</em> dalam kompetisi startup teknologi.</p><p>💡 Solusi yang dikembangkan mendapat apresiasi tinggi dari panel juri yang terdiri dari praktisi industri.</p></div>",
                    'type' => 'innovation',
                    'level' => 'regional'
                ],
                [
                    'title' => 'Sertifikasi ISO 9001:2015 untuk Program Studi',
                    'description' => "<div class='achievement-desc'><p><strong>Kontribusi aktif {$alias}</strong> dalam membantu program studi meraih sertifikasi <strong>ISO 9001:2015</strong> untuk sistem manajemen mutu.</p><p>📈 Pencapaian ini meningkatkan kredibilitas dan standar pendidikan.</p></div>",
                    'type' => 'certification',
                    'level' => 'institutional'
                ],
                [
                    'title' => 'Hackathon Champion - Smart City Solutions',
                    'description' => "<div class='achievement-desc'><p><strong>Tim {$alias} menjadi juara</strong> dalam hackathon <em>Smart City Solutions</em> dengan mengembangkan aplikasi inovatif untuk mengatasi permasalahan transportasi urban.</p><p>🏙️ Solusi ini telah diimplementasikan di beberapa kota.</p></div>",
                    'type' => 'competition',
                    'level' => 'national'
                ],
                [
                    'title' => 'Outstanding Academic Achievement 2024',
                    'description' => "<div class='achievement-desc'><p><strong>{$alias} meraih penghargaan</strong> <em>Outstanding Academic Achievement</em> karena:</p><ul><li>📚 IPK rata-rata anggota konsisten di atas 3.5</li><li>🎓 Tingkat kelulusan tepat waktu mencapai 95%</li></ul></div>",
                    'type' => 'academic',
                    'level' => 'university'
                ]
            ],
            'UKM Seni' => [
                [
                    'title' => 'Juara 1 Festival Fotografi Nasional "Indonesia Heritage"',
                    'description' => "<div class='achievement-desc'><p><strong>Karya fotografi anggota {$alias} meraih juara pertama</strong> dalam kategori <em>Heritage Photography</em> di festival tingkat nasional.</p><p>📷 Foto yang menampilkan keindahan budaya lokal mendapat apresiasi tinggi dari juri internasional.</p></div>",
                    'type' => 'competition',
                    'level' => 'national'
                ],
                [
                    'title' => 'Pameran Tunggal di Galeri Nasional Indonesia',
                    'description' => "<div class='achievement-desc'><p><strong>{$alias} mendapat kehormatan</strong> menggelar pameran tunggal di <em>Galeri Nasional Indonesia</em> dengan tema 'Youth Expression'.</p><p>🎨 Pameran ini menampilkan <strong>50+ karya terbaik</strong> dari anggota selama 3 tahun terakhir.</p></div>",
                    'type' => 'exhibition',
                    'level' => 'national'
                ],
                [
                    'title' => 'Best Performance Award - Festival Seni Mahasiswa',
                    'description' => "<div class='achievement-desc'><p><strong>Pertunjukan musik dan tari kolaboratif {$alias}</strong> meraih <em>Best Performance Award</em> di Festival Seni Mahasiswa tingkat Jawa.</p><p>🎭 Penampilan yang memadukan seni tradisional dan kontemporer mendapat <strong>standing ovation</strong>.</p></div>",
                    'type' => 'performance',
                    'level' => 'regional'
                ],
                [
                    'title' => 'Artist Residency Program - Cultural Exchange',
                    'description' => "<div class='achievement-desc'><p><strong>Tiga anggota {$alias} terpilih</strong> mengikuti <em>Artist Residency Program</em> di Malaysia dalam program pertukaran budaya ASEAN.</p><p>🌏 Program ini membuka wawasan dan jaringan internasional.</p></div>",
                    'type' => 'exchange',
                    'level' => 'international'
                ],
                [
                    'title' => 'Community Art Project Recognition',
                    'description' => "<div class='achievement-desc'><p><strong>Proyek seni komunitas {$alias}</strong> mendapat pengakuan dari <em>Kementerian Pendidikan dan Kebudayaan</em>.</p><p>🏛️ Berhasil merevitalisasi seni tradisional di <strong>5 desa</strong> melalui workshop dan pemberdayaan.</p></div>",
                    'type' => 'community',
                    'level' => 'national'
                ]
            ],
            'UKM Olahraga' => [
                [
                    'title' => 'Juara 1 LIMA Basketball Divisi Utama 2024',
                    'description' => "<div class='achievement-desc'><p><strong>Tim basket {$alias} berhasil meraih juara pertama</strong> di <em>Liga Mahasiswa (LIMA) Basketball Divisi Utama 2024</em>.</p><p>🏀 Perjalanan menuju juara tidaklah mudah dengan mengalahkan <strong>32 tim terbaik</strong> se-Indonesia.</p></div>",
                    'type' => 'competition',
                    'level' => 'national'
                ],
                [
                    'title' => 'Atlet Terbaik Pekan Olahraga Mahasiswa Nasional',
                    'description' => "<div class='achievement-desc'><p><strong>Salah satu anggota {$alias} meraih predikat</strong> <em>Atlet Terbaik</em> dalam Pekan Olahraga Mahasiswa Nasional cabang atletik.</p><p>🥇 Prestasi ini sekaligus membukakan jalan menuju seleksi tim nasional.</p></div>",
                    'type' => 'individual',
                    'level' => 'national'
                ],
                [
                    'title' => 'Fair Play Award - Turnamen Futsal Regional',
                    'description' => "<div class='achievement-desc'><p><strong>Tim futsal {$alias} meraih</strong> <em>Fair Play Award</em> dalam turnamen regional karena sportivitas tinggi.</p><p>⚽ Tidak pernah mendapat kartu merah selama kompetisi - prestasi yang membanggakan di luar aspek teknis!</p></div>",
                    'type' => 'sportsmanship',
                    'level' => 'regional'
                ],
                [
                    'title' => 'Sports Development Program Excellence',
                    'description' => "<div class='achievement-desc'><p><strong>Program pembinaan olahraga {$alias}</strong> untuk anak-anak di sekitar kampus mendapat apresiasi dari <em>KONI Daerah</em>.</p><p>👶 Program ini telah melahirkan <strong>10+ atlet muda berbakat</strong>.</p></div>",
                    'type' => 'development',
                    'level' => 'regional'
                ],
                [
                    'title' => 'Marathon Finisher Achievement',
                    'description' => "<div class='achievement-desc'><p><strong>100% anggota {$alias}</strong> berhasil menyelesaikan <em>Jakarta Marathon 2024</em>.</p><p>🏃‍♂️ Pencapaian kolektif ini menunjukkan dedikasi tinggi terhadap kebugaran dan disiplin latihan yang konsisten.</p></div>",
                    'type' => 'endurance',
                    'level' => 'national'
                ]
            ],
            'UKM Teknologi' => [
                [
                    'title' => 'Winner Robot Competition International 2024',
                    'description' => "<div class='achievement-desc'><p><strong>Robot buatan {$alias} meraih juara pertama</strong> dalam kompetisi robotika internasional dengan kategori <em>autonomous navigation</em>.</p><p>🤖 Robot ini mampu menyelesaikan misi kompleks dengan akurasi <strong>98%</strong>!</p></div>",
                    'type' => 'competition',
                    'level' => 'international'
                ],
                [
                    'title' => 'Patent Granted - Smart Irrigation System',
                    'description' => "<div class='achievement-desc'><p><strong>Inovasi Smart Irrigation System</strong> yang dikembangkan {$alias} berhasil mendapat paten dari <em>Direktorat Jenderal Kekayaan Intelektual</em>.</p><p>🌱 Sistem ini telah diimplementasikan di <strong>20+ lahan pertanian</strong>.</p></div>",
                    'type' => 'patent',
                    'level' => 'national'
                ],
                [
                    'title' => 'Startup Incubation Program Graduate',
                    'description' => "<div class='achievement-desc'><p><strong>Startup yang dibentuk anggota {$alias}</strong> berhasil lulus dari program inkubasi <em>Techstars</em>.</p><p>💰 Mendapat pendanaan seed round senilai <strong>$100K</strong> untuk pengembangan aplikasi AI-powered.</p></div>",
                    'type' => 'startup',
                    'level' => 'international'
                ],
                [
                    'title' => 'IoT Innovation Challenge Champion',
                    'description' => "<div class='achievement-desc'><p><strong>Solusi IoT untuk smart home</strong> yang dikembangkan {$alias} memenangkan <em>IoT Innovation Challenge</em> tingkat ASEAN.</p><p>🏠 Produk ini telah dikomersialkan dan dijual di <strong>3 negara</strong>.</p></div>",
                    'type' => 'innovation',
                    'level' => 'international'
                ],
                [
                    'title' => 'Open Source Contributor Recognition',
                    'description' => "<div class='achievement-desc'><p><strong>Anggota {$alias} diakui</strong> sebagai contributor aktif dalam proyek open source <em>Linux kernel</em>.</p><p>🐧 Mendapat undangan khusus ke <strong>Linux Foundation Conference 2024</strong>.</p></div>",
                    'type' => 'contribution',
                    'level' => 'international'
                ]
            ],
            'UKM Kemasyarakatan' => [
                [
                    'title' => 'Outstanding Community Service Award 2024',
                    'description' => "<div class='achievement-desc'><p><strong>Program pemberdayaan masyarakat {$alias}</strong> di 5 desa terpencil mendapat penghargaan <em>Outstanding Community Service Award</em> dari Kemendesa.</p><p>👥 Program ini telah meningkatkan kesejahteraan <strong>500+ keluarga</strong>.</p></div>",
                    'type' => 'service',
                    'level' => 'national'
                ],
                [
                    'title' => 'Environmental Conservation Recognition',
                    'description' => "<div class='achievement-desc'><p><strong>Inisiatif konservasi lingkungan {$alias}</strong> berhasil menanam <strong>10,000 pohon</strong> dan membersihkan <strong>50+ sungai</strong>.</p><p>🌳 Mendapat pengakuan dari <em>Kementerian Lingkungan Hidup dan Kehutanan</em>.</p></div>",
                    'type' => 'environment',
                    'level' => 'national'
                ],
                [
                    'title' => 'Education for All Program Excellence',
                    'description' => "<div class='achievement-desc'><p><strong>Program 'Education for All' {$alias}</strong> berhasil memberikan pendidikan gratis kepada <strong>200+ anak kurang mampu</strong>.</p><p>📚 Mendapat dukungan funding dari <em>UNICEF Indonesia</em>.</p></div>",
                    'type' => 'education',
                    'level' => 'national'
                ],
                [
                    'title' => 'Disaster Relief Volunteer Recognition',
                    'description' => "<div class='achievement-desc'><p><strong>Tim relawan {$alias}</strong> mendapat apresiasi khusus dari <em>BNPB</em> karena respons cepat dan bantuan efektif.</p><p>🚨 Membantu evakuasi <strong>1000+ warga</strong> saat bencana banjir di Jawa Tengah.</p></div>",
                    'type' => 'disaster_relief',
                    'level' => 'national'
                ]
            ]
        ];

        return $achievements[$category] ?? [
            [
                'title' => 'Excellence in Student Leadership 2024',
                'description' => "<div class='achievement-desc'><p><strong>Anggota {$alias} menunjukkan kepemimpinan luar biasa</strong> dalam berbagai kegiatan kemahasiswaan.</p><p>👑 Mendapat pengakuan dari universitas atas dedikasi dan kontribusinya.</p></div>",
                'type' => 'leadership',
                'level' => 'university'
            ],
            [
                'title' => 'Active Participation Award',
                'description' => "<div class='achievement-desc'><p><strong>{$alias} mendapat penghargaan</strong> <em>Active Participation Award</em> karena konsistensi tinggi.</p><p>⭐ Mengikuti kegiatan kemahasiswaan selama <strong>2 tahun berturut-turut</strong>.</p></div>",
                'type' => 'participation',
                'level' => 'university'
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
        $typePrefix = match ($type) {
            'competition' => 'trophy',
            'innovation' => 'innovation',
            'exhibition' => 'exhibition',
            'performance' => 'performance',
            'certification' => 'certificate',
            'patent' => 'patent',
            'startup' => 'startup',
            'service' => 'service',
            'environment' => 'environment',
            default => 'achievement',
        };

        return "achievements/{$typePrefix}-{$alias}-" . rand(1, 3) . '.jpg';
    }

    private function createCollaborativeAchievement($ukm)
    {
        $collaborativeAchievements = [
            [
                'title' => 'Inter-UKM Collaboration Project Success',
                'description' => "<div class='achievement-desc'><p><strong>Proyek kolaborasi antar UKM</strong> yang dipimpin {$ukm->alias} berhasil menghasilkan inovasi terobosan.</p><p>💰 Mendapat funding dari <em>Kemristek/BRIN</em> untuk pengembangan lanjutan.</p></div>",
                'type' => 'collaboration'
            ],
            [
                'title' => 'Cross-Faculty Innovation Award',
                'description' => "<div class='achievement-desc'><p><strong>Tim gabungan yang melibatkan {$ukm->alias}</strong> berhasil meraih <em>Cross-Faculty Innovation Award</em>.</p><p>🎓 Mengembangkan solusi interdisipliner untuk smart campus.</p></div>",
                'type' => 'cross_faculty'
            ],
            [
                'title' => 'Student Exchange Program Leadership',
                'description' => "<div class='achievement-desc'><p><strong>{$ukm->alias} berperan sebagai koordinator utama</strong> program pertukaran mahasiswa dengan <strong>5 universitas</strong> di Asia Tenggara.</p><p>🌏 Memfasilitasi <strong>50+ mahasiswa</strong>.</p></div>",
                'type' => 'exchange_leadership'
            ]
        ];

        $achievement = $collaborativeAchievements[array_rand($collaborativeAchievements)];
        $createdDate = Carbon::now()->subDays(rand(30, 120));

        Achievement::create([
            'unit_kegiatan_id' => $ukm->id,
            'title' => $achievement['title'],
            'description' => $achievement['description'],
            'image' => "achievements/collaborative-{$ukm->alias}-" . rand(1, 2) . '.jpg',
            'created_at' => $createdDate,
            'updated_at' => $createdDate->copy()->addDays(rand(1, 14)),
        ]);
    }

    private function createUniversityAchievements($ukms)
    {
        $universityAchievements = [
            [
                'title' => 'University Excellence in Student Activities 2024',
                'description' => '<div class="achievement-desc"><p><strong>Universitas meraih penghargaan</strong> <em>Excellence in Student Activities</em> dari Kemendikbudristek.</p><p>🎯 Berkat kontribusi luar biasa dari seluruh UKM dalam mengembangkan soft skills mahasiswa.</p></div>',
                'ukm' => $ukms->random()
            ],
            [
                'title' => 'Accreditation A for Student Development Programs',
                'description' => '<div class="achievement-desc"><p><strong>Program pengembangan mahasiswa universitas</strong> meraih <em>akreditasi A</em> dari BAN-PT.</p><p>🏆 Dengan apresiasi khusus untuk ekosistem UKM yang dinamis dan berprestasi.</p></div>',
                'ukm' => $ukms->random()
            ],
            [
                'title' => 'Top 10 Most Active Student Organizations Indonesia',
                'description' => '<div class="achievement-desc"><p><strong>Ekosistem UKM universitas</strong> masuk dalam <em>top 10 Most Active Student Organizations</em> di Indonesia versi Kemendikbudristek.</p><p>📊 Dengan <strong>200+ kegiatan berkualitas</strong> per tahun.</p></div>',
                'ukm' => $ukms->random()
            ]
        ];

        foreach ($universityAchievements as $achievement) {
            $createdDate = Carbon::now()->subDays(rand(60, 200));

            Achievement::create([
                'unit_kegiatan_id' => $achievement['ukm']->id,
                'title' => $achievement['title'],
                'description' => $achievement['description'],
                'image' => 'achievements/university-wide-' . rand(1, 3) . '.jpg',
                'created_at' => $createdDate,
                'updated_at' => $createdDate->copy()->addDays(rand(1, 30)),
            ]);
        }
    }
}
