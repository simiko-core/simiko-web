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
                'vision_mission' => '<div class="vision-mission">
<h3><strong>Vision:</strong></h3>
<p>Menjadi himpunan mahasiswa informatika yang unggul dalam pengembangan teknologi informasi dan komputer untuk kemajuan bangsa.</p>

<h3><strong>Mission:</strong></h3>
<p>Mengembangkan potensi mahasiswa informatika melalui kegiatan akademik, penelitian, dan pengabdian masyarakat di bidang teknologi informasi.</p>
</div>',
                'description' => '<div class="description">
<p><strong>Himpunan Mahasiswa Informatika (HMIF)</strong> adalah organisasi mahasiswa yang bergerak dalam bidang teknologi informasi dan komputer.</p>
<p>Kami menyelenggarakan berbagai kegiatan seperti:</p>
<ul>
<li>💻 Seminar teknologi</li>
<li>🛠️ Workshop pemrograman</li>
<li>🏆 Kompetisi coding</li>
<li>📱 Pengembangan aplikasi untuk masyarakat</li>
</ul>
</div>',
            ],
            'HMTE' => [
                'vision_mission' => '<div class="vision-mission">
<h3><strong>Vision:</strong></h3>
<p>Menjadi himpunan mahasiswa teknik elektro yang terdepan dalam inovasi teknologi elektro dan elektronika.</p>

<h3><strong>Mission:</strong></h3>
<p>Memfasilitasi pengembangan kompetensi mahasiswa teknik elektro melalui kegiatan akademik, riset, dan aplikasi teknologi elektro.</p>
</div>',
                'description' => '<div class="description">
<p><strong>Himpunan Mahasiswa Teknik Elektro (HMTE)</strong> adalah wadah bagi mahasiswa teknik elektro untuk mengembangkan kemampuan di bidang:</p>
<ul>
<li>⚡ Kelistrikan</li>
<li>🔌 Elektronika</li>
<li>🎛️ Sistem kontrol</li>
</ul>
<p>Kami aktif dalam <em>penelitian energi terbarukan</em> dan <strong>teknologi otomasi</strong>.</p>
</div>',
            ],
            'HMTM' => [
                'vision_mission' => '<div class="vision-mission">
<h3><strong>Vision:</strong></h3>
<p>Menjadi himpunan mahasiswa teknik mesin yang berkualitas dalam pengembangan teknologi manufaktur dan industri.</p>

<h3><strong>Mission:</strong></h3>
<p>Meningkatkan kompetensi mahasiswa teknik mesin dalam perancangan, manufaktur, dan inovasi teknologi mesin.</p>
</div>',
                'description' => '<div class="description">
<p><strong>Himpunan Mahasiswa Teknik Mesin (HMTM)</strong> fokus pada pengembangan:</p>
<ul>
<li>🏭 Teknologi manufaktur</li>
<li>🚗 Teknologi otomotif</li>
<li>⚙️ Teknologi industri</li>
</ul>
<p>Kami menyelenggarakan <strong>workshop fabrikasi</strong>, <strong>kontes robot</strong>, dan <strong>pelatihan desain mesin</strong>.</p>
</div>',
            ],
            'HMTS' => [
                'vision_mission' => '<div class="vision-mission">
<h3><strong>Vision:</strong></h3>
<p>Menjadi himpunan mahasiswa teknik sipil yang berperan aktif dalam pembangunan infrastruktur berkelanjutan.</p>

<h3><strong>Mission:</strong></h3>
<p>Mengembangkan kemampuan mahasiswa teknik sipil dalam perencanaan, desain, dan konstruksi infrastruktur yang ramah lingkungan.</p>
</div>',
                'description' => '<div class="description">
<p><strong>Himpunan Mahasiswa Teknik Sipil (HMTS)</strong> berkomitmen pada pembangunan infrastruktur yang berkelanjutan.</p>
<p>Kami aktif dalam:</p>
<ul>
<li>🧱 Penelitian material konstruksi</li>
<li>📊 Manajemen proyek</li>
<li>🌿 Teknologi bangunan hijau</li>
</ul>
</div>',
            ],
            'UKM Foto' => [
                'vision_mission' => '<div class="vision-mission">
<h3><strong>Vision:</strong></h3>
<p>Menjadi unit kegiatan mahasiswa fotografi yang kreatif dan inovatif dalam seni visual.</p>

<h3><strong>Mission:</strong></h3>
<p>Mengembangkan bakat fotografi mahasiswa dan mendokumentasikan kegiatan kampus serta masyarakat.</p>
</div>',
                'description' => '<div class="description">
<p><strong>UKM Fotografi</strong> adalah komunitas mahasiswa yang <em>passionate</em> dalam seni fotografi.</p>
<p>Kegiatan kami meliputi:</p>
<ul>
<li>📷 Pameran foto</li>
<li>📚 Workshop teknik fotografi</li>
<li>📸 Dokumentasi berbagai kegiatan kampus</li>
</ul>
</div>',
            ],
            'UKM Musik' => [
                'vision_mission' => '<div class="vision-mission">
<h3><strong>Vision:</strong></h3>
<p>Menjadi wadah kreativitas musik mahasiswa yang dapat menginspirasi dan menghibur masyarakat.</p>

<h3><strong>Mission:</strong></h3>
<p>Mengembangkan talenta musik mahasiswa melalui latihan, pertunjukan, dan apresiasi musik.</p>
</div>',
                'description' => '<div class="description">
<p><strong>UKM Musik</strong> adalah tempat berkumpulnya mahasiswa yang memiliki passion di bidang musik.</p>
<p>Kami memiliki berbagai divisi:</p>
<ul>
<li>🎸 Band</li>
<li>🎤 Paduan suara</li>
<li>🎻 Orkestra kampus</li>
</ul>
</div>',
            ],
            'UKM Sport' => [
                'vision_mission' => '<div class="vision-mission">
<h3><strong>Vision:</strong></h3>
<p>Menjadi unit kegiatan olahraga yang mencetak atlet-atlet berprestasi dan mempromosikan hidup sehat.</p>

<h3><strong>Mission:</strong></h3>
<p>Membina bakat olahraga mahasiswa dan mempromosikan gaya hidup sehat di lingkungan kampus.</p>
</div>',
                'description' => '<div class="description">
<p><strong>UKM Olahraga</strong> adalah wadah bagi mahasiswa untuk mengembangkan bakat di berbagai cabang olahraga.</p>
<p>Cabang olahraga yang kami bina:</p>
<ul>
<li>🏀 Basket</li>
<li>⚽ Futsal</li>
<li>🏐 Voli</li>
<li>🏸 Badminton</li>
<li>🏃 Atletik</li>
</ul>
</div>',
            ],
            'UKM PA' => [
                'vision_mission' => '<div class="vision-mission">
<h3><strong>Vision:</strong></h3>
<p>Menjadi komunitas pecinta alam yang peduli lingkungan dan mempromosikan ekowisata.</p>

<h3><strong>Mission:</strong></h3>
<p>Mengembangkan kesadaran lingkungan mahasiswa melalui kegiatan alam bebas dan konservasi.</p>
</div>',
                'description' => '<div class="description">
<p><strong>UKM Pecinta Alam</strong> adalah komunitas yang fokus pada kegiatan outdoor, konservasi lingkungan, dan pendidikan ekowisata.</p>
<p>Kegiatan rutin kami:</p>
<ul>
<li>🏔️ Pendakian gunung</li>
<li>🏕️ Camping</li>
<li>🌱 Aksi bersih lingkungan</li>
<li>📚 Edukasi konservasi</li>
</ul>
</div>',
            ],
            'UKM Robot' => [
                'vision_mission' => '<div class="vision-mission">
<h3><strong>Vision:</strong></h3>
<p>Menjadi pusat pengembangan robotika dan otomasi yang inovatif dan berdaya saing.</p>

<h3><strong>Mission:</strong></h3>
<p>Mengembangkan kemampuan mahasiswa dalam perancangan, pembuatan, dan pemrograman robot.</p>
</div>',
                'description' => '<div class="description">
<p><strong>UKM Robotika</strong> adalah tempat mahasiswa mengembangkan kemampuan dalam bidang:</p>
<ul>
<li>🤖 Robotika</li>
<li>🧠 Artificial Intelligence (AI)</li>
<li>⚙️ Sistem otomasi</li>
</ul>
<p>Kami <em>aktif dalam kompetisi robot</em> tingkat <strong>nasional dan internasional</strong>.</p>
</div>',
            ],
            'UKM Debat' => [
                'vision_mission' => '<div class="vision-mission">
<h3><strong>Vision:</strong></h3>
<p>Menjadi wadah pengembangan kemampuan berbicara dan berpikir kritis mahasiswa.</p>

<h3><strong>Mission:</strong></h3>
<p>Melatih kemampuan argumentasi, public speaking, dan analisis kritis mahasiswa.</p>
</div>',
                'description' => '<div class="description">
<p><strong>UKM Debat</strong> adalah komunitas mahasiswa yang fokus pada pengembangan:</p>
<ul>
<li>🎤 Kemampuan berbicara di depan umum</li>
<li>💬 Argumentasi yang kuat</li>
<li>🧐 Analisis isu-isu terkini</li>
<li>🏆 Kompetisi debat</li>
</ul>
</div>',
            ],
        ];

        foreach ($units as $unit) {
            $profileData = $profiles[$unit->alias] ?? [
                'vision_mission' => '<div class="vision-mission">
<h3><strong>Vision:</strong></h3>
<p>Menjadi unit kegiatan mahasiswa yang unggul dan berprestasi.</p>

<h3><strong>Mission:</strong></h3>
<p>Mengembangkan potensi mahasiswa melalui berbagai kegiatan positif.</p>
</div>',
                'description' => '<div class="description">
<p><strong>Unit kegiatan mahasiswa</strong> yang aktif dalam mengembangkan <em>bakat dan minat mahasiswa</em>.</p>
<p>Bergabunglah dengan kami untuk mengembangkan potensi diri!</p>
</div>',
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
