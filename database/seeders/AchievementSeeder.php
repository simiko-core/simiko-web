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
                    'description' => "Tim {$alias} berhasil meraih juara pertama dalam kompetisi programming tingkat nasional dengan mengalahkan 150+ tim dari seluruh Indonesia. Prestasi ini membuktikan keunggulan kemampuan coding dan problem solving anggota.",
                    'type' => 'competition',
                    'level' => 'national'
                ],
                [
                    'title' => 'Best Innovation Award - Tech Startup Competition',
                    'description' => "Ide inovasi dari {$alias} berhasil meraih penghargaan Best Innovation Award dalam kompetisi startup teknologi. Solusi yang dikembangkan mendapat apresiasi tinggi dari panel juri yang terdiri dari praktisi industri.",
                    'type' => 'innovation',
                    'level' => 'regional'
                ],
                [
                    'title' => 'Sertifikasi ISO 9001:2015 untuk Program Studi',
                    'description' => "Kontribusi aktif {$alias} dalam membantu program studi meraih sertifikasi ISO 9001:2015 untuk sistem manajemen mutu. Pencapaian ini meningkatkan kredibilitas dan standar pendidikan.",
                    'type' => 'certification',
                    'level' => 'institutional'
                ],
                [
                    'title' => 'Hackathon Champion - Smart City Solutions',
                    'description' => "Tim {$alias} menjadi juara dalam hackathon Smart City Solutions dengan mengembangkan aplikasi inovatif untuk mengatasi permasalahan transportasi urban. Solusi ini telah diimplementasikan di beberapa kota.",
                    'type' => 'competition',
                    'level' => 'national'
                ],
                [
                    'title' => 'Outstanding Academic Achievement 2024',
                    'description' => "{$alias} meraih penghargaan Outstanding Academic Achievement karena IPK rata-rata anggota yang konsisten di atas 3.5 dan tingkat kelulusan tepat waktu mencapai 95%.",
                    'type' => 'academic',
                    'level' => 'university'
                ]
            ],
            'UKM Seni' => [
                [
                    'title' => 'Juara 1 Festival Fotografi Nasional "Indonesia Heritage"',
                    'description' => "Karya fotografi anggota {$alias} meraih juara pertama dalam kategori Heritage Photography di festival tingkat nasional. Foto yang menampilkan keindahan budaya lokal mendapat apresiasi tinggi dari juri internasional.",
                    'type' => 'competition',
                    'level' => 'national'
                ],
                [
                    'title' => 'Pameran Tunggal di Galeri Nasional Indonesia',
                    'description' => "{$alias} mendapat kehormatan menggelar pameran tunggal di Galeri Nasional Indonesia dengan tema 'Youth Expression'. Pameran ini menampilkan 50+ karya terbaik dari anggota selama 3 tahun terakhir.",
                    'type' => 'exhibition',
                    'level' => 'national'
                ],
                [
                    'title' => 'Best Performance Award - Festival Seni Mahasiswa',
                    'description' => "Pertunjukan musik dan tari kolaboratif {$alias} meraih Best Performance Award di Festival Seni Mahasiswa tingkat Jawa. Penampilan yang memadukan seni tradisional dan kontemporer mendapat standing ovation.",
                    'type' => 'performance',
                    'level' => 'regional'
                ],
                [
                    'title' => 'Artist Residency Program - Cultural Exchange',
                    'description' => "Tiga anggota {$alias} terpilih mengikuti Artist Residency Program di Malaysia dalam program pertukaran budaya ASEAN. Program ini membuka wawasan dan jaringan internasional.",
                    'type' => 'exchange',
                    'level' => 'international'
                ],
                [
                    'title' => 'Community Art Project Recognition',
                    'description' => "Proyek seni komunitas {$alias} mendapat pengakuan dari Kementerian Pendidikan dan Kebudayaan karena berhasil merevitalisasi seni tradisional di 5 desa melalui workshop dan pemberdayaan.",
                    'type' => 'community',
                    'level' => 'national'
                ]
            ],
            'UKM Olahraga' => [
                [
                    'title' => 'Juara 1 LIMA Basketball Divisi Utama 2024',
                    'description' => "Tim basket {$alias} berhasil meraih juara pertama di Liga Mahasiswa (LIMA) Basketball Divisi Utama 2024. Perjalanan menuju juara tidaklah mudah dengan mengalahkan 32 tim terbaik se-Indonesia.",
                    'type' => 'competition',
                    'level' => 'national'
                ],
                [
                    'title' => 'Atlet Terbaik Pekan Olahraga Mahasiswa Nasional',
                    'description' => "Salah satu anggota {$alias} meraih predikat Atlet Terbaik dalam Pekan Olahraga Mahasiswa Nasional cabang atletik. Prestasi ini sekaligus membukakan jalan menuju seleksi tim nasional.",
                    'type' => 'individual',
                    'level' => 'national'
                ],
                [
                    'title' => 'Fair Play Award - Turnamen Futsal Regional',
                    'description' => "Tim futsal {$alias} meraih Fair Play Award dalam turnamen regional karena sportivitas tinggi dan tidak pernah mendapat kartu merah selama kompetisi. Prestasi yang membanggakan di luar aspek teknis.",
                    'type' => 'sportsmanship',
                    'level' => 'regional'
                ],
                [
                    'title' => 'Sports Development Program Excellence',
                    'description' => "Program pembinaan olahraga {$alias} untuk anak-anak di sekitar kampus mendapat apresiasi dari KONI Daerah. Program ini telah melahirkan 10+ atlet muda berbakat.",
                    'type' => 'development',
                    'level' => 'regional'
                ],
                [
                    'title' => 'Marathon Finisher Achievement',
                    'description' => "100% anggota {$alias} berhasil menyelesaikan Jakarta Marathon 2024. Pencapaian kolektif ini menunjukkan dedikasi tinggi terhadap kebugaran dan disiplin latihan yang konsisten.",
                    'type' => 'endurance',
                    'level' => 'national'
                ]
            ],
            'UKM Teknologi' => [
                [
                    'title' => 'Winner Robot Competition International 2024',
                    'description' => "Robot buatan {$alias} meraih juara pertama dalam kompetisi robotika internasional dengan kategori autonomous navigation. Robot ini mampu menyelesaikan misi kompleks dengan akurasi 98%.",
                    'type' => 'competition',
                    'level' => 'international'
                ],
                [
                    'title' => 'Patent Granted - Smart Irrigation System',
                    'description' => "Inovasi Smart Irrigation System yang dikembangkan {$alias} berhasil mendapat paten dari Direktorat Jenderal Kekayaan Intelektual. Sistem ini telah diimplementasikan di 20+ lahan pertanian.",
                    'type' => 'patent',
                    'level' => 'national'
                ],
                [
                    'title' => 'Startup Incubation Program Graduate',
                    'description' => "Startup yang dibentuk anggota {$alias} berhasil lulus dari program inkubasi Techstars dan mendapat pendanaan seed round senilai $100K untuk pengembangan aplikasi AI-powered.",
                    'type' => 'startup',
                    'level' => 'international'
                ],
                [
                    'title' => 'IoT Innovation Challenge Champion',
                    'description' => "Solusi IoT untuk smart home yang dikembangkan {$alias} memenangkan IoT Innovation Challenge tingkat ASEAN. Produk ini telah dikomersialkan dan dijual di 3 negara.",
                    'type' => 'innovation',
                    'level' => 'international'
                ],
                [
                    'title' => 'Open Source Contributor Recognition',
                    'description' => "Anggota {$alias} diakui sebagai contributor aktif dalam proyek open source Linux kernel dan mendapat undangan khusus ke Linux Foundation Conference 2024.",
                    'type' => 'contribution',
                    'level' => 'international'
                ]
            ],
            'UKM Kemasyarakatan' => [
                [
                    'title' => 'Outstanding Community Service Award 2024',
                    'description' => "Program pemberdayaan masyarakat {$alias} di 5 desa terpencil mendapat penghargaan Outstanding Community Service Award dari Kemendesa. Program ini telah meningkatkan kesejahteraan 500+ keluarga.",
                    'type' => 'service',
                    'level' => 'national'
                ],
                [
                    'title' => 'Environmental Conservation Recognition',
                    'description' => "Inisiatif konservasi lingkungan {$alias} berhasil menanam 10,000 pohon dan membersihkan 50+ sungai mendapat pengakuan dari Kementerian Lingkungan Hidup dan Kehutanan.",
                    'type' => 'environment',
                    'level' => 'national'
                ],
                [
                    'title' => 'Education for All Program Excellence',
                    'description' => "Program 'Education for All' {$alias} berhasil memberikan pendidikan gratis kepada 200+ anak kurang mampu dan mendapat dukungan funding dari UNICEF Indonesia.",
                    'type' => 'education',
                    'level' => 'national'
                ],
                [
                    'title' => 'Disaster Relief Volunteer Recognition',
                    'description' => "Tim relawan {$alias} mendapat apresiasi khusus dari BNPB karena respons cepat dan bantuan efektif saat bencana banjir di Jawa Tengah, membantu evakuasi 1000+ warga.",
                    'type' => 'disaster_relief',
                    'level' => 'national'
                ]
            ]
        ];

        return $achievements[$category] ?? [
            [
                'title' => 'Excellence in Student Leadership 2024',
                'description' => "Anggota {$alias} menunjukkan kepemimpinan luar biasa dalam berbagai kegiatan kemahasiswaan dan mendapat pengakuan dari universitas.",
                'type' => 'leadership',
                'level' => 'university'
            ],
            [
                'title' => 'Active Participation Award',
                'description' => "{$alias} mendapat penghargaan Active Participation Award karena konsistensi tinggi dalam mengikuti kegiatan kemahasiswaan selama 2 tahun berturut-turut.",
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
                'description' => "Proyek kolaborasi antar UKM yang dipimpin {$ukm->alias} berhasil menghasilkan inovasi terobosan dan mendapat funding dari Kemristek/BRIN untuk pengembangan lanjutan.",
                'type' => 'collaboration'
            ],
            [
                'title' => 'Cross-Faculty Innovation Award',
                'description' => "Tim gabungan yang melibatkan {$ukm->alias} berhasil meraih Cross-Faculty Innovation Award dengan mengembangkan solusi interdisipliner untuk smart campus.",
                'type' => 'cross_faculty'
            ],
            [
                'title' => 'Student Exchange Program Leadership',
                'description' => "{$ukm->alias} berperan sebagai koordinator utama program pertukaran mahasiswa dengan 5 universitas di Asia Tenggara, memfasilitasi 50+ mahasiswa.",
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
                'description' => 'Universitas meraih penghargaan Excellence in Student Activities dari Kemendikbudristek berkat kontribusi luar biasa dari seluruh UKM dalam mengembangkan soft skills mahasiswa.',
                'ukm' => $ukms->random()
            ],
            [
                'title' => 'Accreditation A for Student Development Programs',
                'description' => 'Program pengembangan mahasiswa universitas meraih akreditasi A dari BAN-PT, dengan apresiasi khusus untuk ekosistem UKM yang dinamis dan berprestasi.',
                'ukm' => $ukms->random()
            ],
            [
                'title' => 'Top 10 Most Active Student Organizations Indonesia',
                'description' => 'Ekosistem UKM universitas masuk dalam top 10 Most Active Student Organizations di Indonesia versi Kemendikbudristek, dengan 200+ kegiatan berkualitas per tahun.',
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
