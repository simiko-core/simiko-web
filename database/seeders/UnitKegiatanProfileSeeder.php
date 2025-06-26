<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UnitKegiatanProfile;
use App\Models\UnitKegiatan;
use Carbon\Carbon;

class UnitKegiatanProfileSeeder extends Seeder
{
    public function run(): void
    {
        $units = UnitKegiatan::all();

        if ($units->isEmpty()) {
            $this->command->warn('No UKM found. Please run UnitKegiatanSeeder first.');
            return;
        }

        $profiles = [
            'HMIF' => [
                'vision_mission' => 'Vision: Menjadi himpunan mahasiswa informatika yang unggul dalam pengembangan teknologi informasi dan komputer untuk kemajuan bangsa.

Mission: Mengembangkan potensi mahasiswa informatika melalui kegiatan akademik, penelitian, dan pengabdian masyarakat di bidang teknologi informasi.',
                'description' => 'Himpunan Mahasiswa Informatika (HMIF) adalah organisasi mahasiswa yang bergerak dalam bidang teknologi informasi dan komputer. Kami menyelenggarakan berbagai kegiatan seperti seminar teknologi, workshop pemrograman, kompetisi coding, dan pengembangan aplikasi untuk masyarakat.',
            ],
            'HMTE' => [
                'vision_mission' => 'Vision: Menjadi himpunan mahasiswa teknik elektro yang terdepan dalam inovasi teknologi elektro dan elektronika.

Mission: Memfasilitasi pengembangan kompetensi mahasiswa teknik elektro melalui kegiatan akademik, riset, dan aplikasi teknologi elektro.',
                'description' => 'Himpunan Mahasiswa Teknik Elektro (HMTE) adalah wadah bagi mahasiswa teknik elektro untuk mengembangkan kemampuan di bidang kelistrikan, elektronika, dan sistem kontrol. Kami aktif dalam penelitian energi terbarukan dan teknologi otomasi.',
            ],
            'HMTM' => [
                'vision_mission' => 'Vision: Menjadi himpunan mahasiswa teknik mesin yang berkualitas dalam pengembangan teknologi manufaktur dan industri.

Mission: Meningkatkan kompetensi mahasiswa teknik mesin dalam perancangan, manufaktur, dan inovasi teknologi mesin.',
                'description' => 'Himpunan Mahasiswa Teknik Mesin (HMTM) fokus pada pengembangan teknologi manufaktur, otomotif, dan industri. Kami menyelenggarakan workshop fabrikasi, kontes robot, dan pelatihan desain mesin.',
            ],
            'HMTS' => [
                'vision_mission' => 'Vision: Menjadi himpunan mahasiswa teknik sipil yang berperan aktif dalam pembangunan infrastruktur berkelanjutan.

Mission: Mengembangkan kemampuan mahasiswa teknik sipil dalam perencanaan, desain, dan konstruksi infrastruktur yang ramah lingkungan.',
                'description' => 'Himpunan Mahasiswa Teknik Sipil (HMTS) berkomitmen pada pembangunan infrastruktur yang berkelanjutan. Kami aktif dalam penelitian material konstruksi, manajemen proyek, dan teknologi bangunan hijau.',
            ],
            'UKM Foto' => [
                'vision_mission' => 'Vision: Menjadi unit kegiatan mahasiswa fotografi yang kreatif dan inovatif dalam seni visual.

Mission: Mengembangkan bakat fotografi mahasiswa dan mendokumentasikan kegiatan kampus serta masyarakat.',
                'description' => 'UKM Fotografi adalah komunitas mahasiswa yang passionate dalam seni fotografi. Kami menyelenggarakan pameran foto, workshop teknik fotografi, dan dokumentasi berbagai kegiatan kampus.',
            ],
            'UKM Musik' => [
                'vision_mission' => 'Vision: Menjadi wadah kreativitas musik mahasiswa yang dapat menginspirasi dan menghibur masyarakat.

Mission: Mengembangkan talenta musik mahasiswa melalui latihan, pertunjukan, dan apresiasi musik.',
                'description' => 'UKM Musik adalah tempat berkumpulnya mahasiswa yang memiliki passion di bidang musik. Kami memiliki berbagai divisi seperti band, paduan suara, dan orkestra kampus.',
            ],
            'UKM Sport' => [
                'vision_mission' => 'Vision: Menjadi unit kegiatan olahraga yang mencetak atlet-atlet berprestasi dan mempromosikan hidup sehat.

Mission: Membina bakat olahraga mahasiswa dan mempromosikan gaya hidup sehat di lingkungan kampus.',
                'description' => 'UKM Olahraga adalah wadah bagi mahasiswa untuk mengembangkan bakat di berbagai cabang olahraga seperti basket, futsal, voli, badminton, dan atletik.',
            ],
            'UKM PA' => [
                'vision_mission' => 'Vision: Menjadi komunitas pecinta alam yang peduli lingkungan dan mempromosikan ekowisata.

Mission: Mengembangkan kesadaran lingkungan mahasiswa melalui kegiatan alam bebas dan konservasi.',
                'description' => 'UKM Pecinta Alam adalah komunitas yang fokus pada kegiatan outdoor, konservasi lingkungan, dan pendidikan ekowisata. Kami rutin mengadakan pendakian, camping, dan aksi bersih lingkungan.',
            ],
            'UKM Robot' => [
                'vision_mission' => 'Vision: Menjadi pusat pengembangan robotika dan otomasi yang inovatif dan berdaya saing.

Mission: Mengembangkan kemampuan mahasiswa dalam perancangan, pembuatan, dan pemrograman robot.',
                'description' => 'UKM Robotika adalah tempat mahasiswa mengembangkan kemampuan dalam bidang robotika, AI, dan otomasi. Kami aktif dalam kompetisi robot nasional dan internasional.',
            ],
            'UKM Debat' => [
                'vision_mission' => 'Vision: Menjadi wadah pengembangan kemampuan berbicara dan berpikir kritis mahasiswa.

Mission: Melatih kemampuan argumentasi, public speaking, dan analisis kritis mahasiswa.',
                'description' => 'UKM Debat adalah komunitas mahasiswa yang fokus pada pengembangan kemampuan berbicara di depan umum, argumentasi, dan analisis isu-isu terkini.',
            ],
        ];

        foreach ($units as $unit) {
            $profileData = $profiles[$unit->alias] ?? [
                'vision_mission' => 'Vision: Menjadi unit kegiatan mahasiswa yang unggul dan berprestasi.

Mission: Mengembangkan potensi mahasiswa melalui berbagai kegiatan positif.',
                'description' => 'Unit kegiatan mahasiswa yang aktif dalam mengembangkan bakat dan minat mahasiswa.',
            ];

            // Create profiles for different periods
            $periods = [2022, 2023, 2024];
            foreach ($periods as $period) {
                UnitKegiatanProfile::create([
                    'unit_kegiatan_id' => $unit->id,
                    'period' => $period,
                    'vision_mission' => $profileData['vision_mission'],
                    'description' => $profileData['description'],
                    'created_at' => Carbon::create($period, 1, 1),
                    'updated_at' => Carbon::create($period, 12, 31),
                ]);
            }
        }

        $this->command->info('UnitKegiatanProfile seeder completed successfully!');
        $this->command->info('Created profiles for ' . $units->count() . ' units across 3 periods');
    }
}
