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
            'HMTI' => [
                'vision_mission' => '<div class="vision-mission">
<h3><strong>Visi:</strong></h3>
<p>Menjadi himpunan mahasiswa teknik informatika yang unggul, inovatif, dan berintegritas dalam mengembangkan teknologi informasi untuk kemajuan bangsa Indonesia.</p>

<h3><strong>Misi:</strong></h3>
<ol>
<li>Mengembangkan kompetensi mahasiswa teknik informatika yang berkarakter dan berakhlak mulia</li>
<li>Menjadi wadah aktualisasi diri mahasiswa dalam bidang teknologi informasi</li>
<li>Membangun ekosistem inovasi teknologi yang berkelanjutan</li>
<li>Berkontribusi dalam pembangunan digital Indonesia</li>
</ol>
</div>',
                'description' => '<div class="description">
<p><strong>Himpunan Mahasiswa Teknik Informatika (HMTI)</strong> adalah organisasi mahasiswa yang bergerak dalam pengembangan ilmu pengetahuan dan teknologi informasi.</p>
<p>🎯 <strong>Program Unggulan:</strong></p>
<ul>
<li>💻 Workshop teknologi terkini (AI, Machine Learning, Cloud Computing)</li>
<li>🏆 Kompetisi programming tingkat nasional</li>
<li>🚀 Inkubator startup teknologi mahasiswa</li>
<li>🤝 Industry partnership dan job fair</li>
<li>📚 Mentoring akademik dan karir</li>
</ul>
<p><em>Bersama HMTI, wujudkan Indonesia Digital yang maju dan berdaya saing!</em></p>
</div>',
            ],
            'HMTE' => [
                'vision_mission' => '<div class="vision-mission">
<h3><strong>Visi:</strong></h3>
<p>Menjadi himpunan mahasiswa teknik elektro yang terdepan dalam inovasi teknologi kelistrikan dan elektronika untuk mendukung ketahanan energi nasional.</p>

<h3><strong>Misi:</strong></h3>
<ol>
<li>Meningkatkan kompetensi mahasiswa dalam bidang teknologi elektro dan elektronika</li>
<li>Mengembangkan penelitian dan inovasi teknologi energi terbarukan</li>
<li>Membangun kerjasama dengan industri dan lembaga penelitian</li>
<li>Mencetak lulusan yang siap berkontribusi bagi pembangunan infrastruktur Indonesia</li>
</ol>
</div>',
                'description' => '<div class="description">
<p><strong>Himpunan Mahasiswa Teknik Elektro (HMTE)</strong> adalah wadah pengembangan ilmu dan teknologi kelistrikan untuk mahasiswa teknik elektro.</p>
<p>⚡ <strong>Fokus Kegiatan:</strong></p>
<ul>
<li>🔌 Penelitian sistem kelistrikan dan energi terbarukan</li>
<li>🎛️ Workshop elektronika dan sistem kontrol</li>
<li>🏭 Kunjungan industri ke PLTU, PLTA, dan perusahaan teknologi</li>
<li>⚙️ Pelatihan teknologi otomasi industri</li>
<li>🌱 Pengembangan teknologi ramah lingkungan</li>
</ul>
<p><em>Membangun Indonesia dengan teknologi elektro yang berkelanjutan!</em></p>
</div>',
            ],
            'HMSI' => [
                'vision_mission' => '<div class="vision-mission">
<h3><strong>Visi:</strong></h3>
<p>Menjadi himpunan mahasiswa sistem informasi yang profesional dalam mengintegrasikan teknologi informasi untuk transformasi digital Indonesia.</p>

<h3><strong>Misi:</strong></h3>
<ol>
<li>Mengembangkan kompetensi mahasiswa dalam analisis dan perancangan sistem informasi</li>
<li>Memfasilitasi pembelajaran teknologi informasi terkini</li>
<li>Membangun jiwa kewirausahaan digital mahasiswa</li>
<li>Berkontribusi dalam digitalisasi UMKM dan sektor publik</li>
</ol>
</div>',
                'description' => '<div class="description">
<p><strong>Himpunan Mahasiswa Sistem Informasi (HMSI)</strong> fokus pada pengembangan sistem informasi yang mendukung transformasi digital Indonesia.</p>
<p>📊 <strong>Program Utama:</strong></p>
<ul>
<li>💼 Business process automation workshop</li>
<li>📱 Mobile app development bootcamp</li>
<li>🏪 Digitalisasi UMKM untuk masyarakat</li>
<li>🎯 UI/UX design competition</li>
<li>📈 Data analytics dan business intelligence training</li>
</ul>
<p><em>Wujudkan Indonesia yang cerdas melalui sistem informasi yang inovatif!</em></p>
</div>',
            ],
            'HMTM' => [
                'vision_mission' => '<div class="vision-mission">
<h3><strong>Visi:</strong></h3>
<p>Menjadi himpunan mahasiswa teknik mesin yang unggul dalam teknologi manufaktur dan industri untuk mendukung kemandirian teknologi nasional.</p>

<h3><strong>Misi:</strong></h3>
<ol>
<li>Mengembangkan kompetensi mahasiswa dalam teknologi manufaktur dan industri</li>
<li>Menumbuhkan jiwa inovasi dalam bidang permesinan</li>
<li>Membangun kerjasama dengan industri manufaktur nasional</li>
<li>Mencetak insinyur mesin yang berkontribusi bagi kemandirian industri Indonesia</li>
</ol>
</div>',
                'description' => '<div class="description">
<p><strong>Himpunan Mahasiswa Teknik Mesin (HMTM)</strong> berkomitmen mengembangkan teknologi manufaktur Indonesia.</p>
<p>⚙️ <strong>Kegiatan Unggulan:</strong></p>
<ul>
<li>🏭 Workshop teknologi manufaktur dan produksi</li>
<li>🚗 Kompetisi desain dan fabrikasi kendaraan</li>
<li>🔧 Pelatihan CNC dan CAD/CAM</li>
<li>🏆 Kontes robot dan mekatronika</li>
<li>🌐 Industrial engineering seminar</li>
</ul>
<p><em>Bangun industri Indonesia yang mandiri dan berdaya saing global!</em></p>
</div>',
            ],
            'HMTS' => [
                'vision_mission' => '<div class="vision-mission">
<h3><strong>Visi:</strong></h3>
<p>Menjadi himpunan mahasiswa teknik sipil yang berperan aktif dalam pembangunan infrastruktur berkelanjutan dan penanggulangan bencana di Indonesia.</p>

<h3><strong>Misi:</strong></h3>
<ol>
<li>Mengembangkan kompetensi mahasiswa dalam perencanaan dan konstruksi infrastruktur</li>
<li>Mempromosikan teknologi bangunan ramah lingkungan</li>
<li>Berpartisipasi dalam mitigasi dan penanggulangan bencana</li>
<li>Mewujudkan infrastruktur Indonesia yang berkualitas dan berkelanjutan</li>
</ol>
</div>',
                'description' => '<div class="description">
<p><strong>Himpunan Mahasiswa Teknik Sipil (HMTS)</strong> berkomitmen membangun infrastruktur Indonesia yang berkelanjutan.</p>
<p>🏗️ <strong>Program Kerja:</strong></p>
<ul>
<li>🏢 Workshop teknologi bangunan hijau dan smart building</li>
<li>🌊 Penelitian mitigasi bencana tsunami dan gempa</li>
<li>🛣️ Studi kasus infrastruktur jalan dan jembatan</li>
<li>📐 Pelatihan software engineering (AutoCAD, SAP, ETABS)</li>
<li>🌱 Pengembangan material konstruksi ramah lingkungan</li>
</ul>
<p><em>Membangun Indonesia dengan infrastruktur yang kuat dan berkelanjutan!</em></p>
</div>',
            ],
            'HME' => [
                'vision_mission' => '<div class="vision-mission">
<h3><strong>Visi:</strong></h3>
<p>Menjadi himpunan mahasiswa ekonomi yang berperan dalam pengembangan ekonomi kerakyatan dan pemberdayaan UMKM Indonesia.</p>

<h3><strong>Misi:</strong></h3>
<ol>
<li>Mengembangkan pemahaman ekonomi yang berlandaskan nilai-nilai Pancasila</li>
<li>Memfasilitasi pengembangan kewirausahaan mahasiswa</li>
<li>Berkontribusi dalam pemberdayaan ekonomi masyarakat</li>
<li>Mewujudkan ekonomi Indonesia yang adil dan sejahtera</li>
</ol>
</div>',
                'description' => '<div class="description">
<p><strong>Himpunan Mahasiswa Ekonomi (HME)</strong> berkomitmen mengembangkan ekonomi kerakyatan Indonesia.</p>
<p>💰 <strong>Program Strategis:</strong></p>
<ul>
<li>📊 Workshop analisis ekonomi dan investasi</li>
<li>🏪 Program pemberdayaan UMKM</li>
<li>💡 Business plan competition</li>
<li>📈 Seminar ekonomi digital dan fintech</li>
<li>🤝 Kerjasama dengan koperasi dan BUMDes</li>
</ul>
<p><em>Wujudkan ekonomi Indonesia yang berkeadilan sosial!</em></p>
</div>',
            ],
            'UKM Tari' => [
                'vision_mission' => '<div class="vision-mission">
<h3><strong>Visi:</strong></h3>
<p>Menjadi wadah pelestarian dan pengembangan seni tari Nusantara yang mencerminkan kekayaan budaya Indonesia.</p>

<h3><strong>Misi:</strong></h3>
<ol>
<li>Melestarikan tari tradisional Nusantara di kalangan mahasiswa</li>
<li>Mengembangkan kreativitas seni tari kontemporer</li>
<li>Memperkenalkan budaya Indonesia melalui pertunjukan seni</li>
<li>Membangun apresiasi seni budaya yang tinggi</li>
</ol>
</div>',
                'description' => '<div class="description">
<p><strong>UKM Seni Tari Nusantara</strong> adalah komunitas pelestari dan pengembang seni tari Indonesia.</p>
<p>💃 <strong>Repertoar Tari:</strong></p>
<ul>
<li>🌺 Tari tradisional (Saman, Kecak, Jaipong, Tor-tor)</li>
<li>🎭 Tari kreasi baru dengan sentuhan modern</li>
<li>🌍 Tari internasional untuk pertukaran budaya</li>
<li>📚 Workshop sejarah dan filosofi tari Nusantara</li>
<li>🎪 Pentas seni dalam acara-acara kampus dan nasional</li>
</ul>
<p><em>Bhinneka Tunggal Ika dalam harmoni gerak tari!</em></p>
</div>',
            ],
            'UKM PSM' => [
                'vision_mission' => '<div class="vision-mission">
<h3><strong>Visi:</strong></h3>
<p>Menjadi paduan suara mahasiswa terdepan yang menyuarakan keindahan musik Indonesia dan dunia dengan harmoni yang sempurna.</p>

<h3><strong>Misi:</strong></h3>
<ol>
<li>Mengembangkan talenta vokal mahasiswa dengan teknik yang profesional</li>
<li>Melestarikan lagu-lagu daerah dan nasional Indonesia</li>
<li>Membangun karakter disiplin dan kerjasama tim</li>
<li>Mengharumkan nama universitas melalui prestasi musik</li>
</ol>
</div>',
                'description' => '<div class="description">
<p><strong>UKM Paduan Suara Mahasiswa (PSM)</strong> adalah ensemble vokal yang mengusung musik Indonesia dan internasional.</p>
<p>🎵 <strong>Kegiatan Rutin:</strong></p>
<ul>
<li>🎤 Latihan vokal dan teknik bernyanyi profesional</li>
<li>🎼 Aransemen lagu daerah dan lagu nasional</li>
<li>🏆 Kompetisi paduan suara tingkat nasional</li>
<li>🎪 Konser tahunan dan pertunjukan di acara kemerdekaan</li>
<li>🌏 Pertukaran budaya dengan paduan suara internasional</li>
</ul>
<p><em>Indonesia Raya bergema dalam harmoni suara emas!</em></p>
</div>',
            ],
            'UKM Teater' => [
                'vision_mission' => '<div class="vision-mission">
<h3><strong>Visi:</strong></h3>
<p>Menjadi teater mahasiswa yang menginspirasi melalui karya seni pertunjukan yang bermakna dan berkarakter Indonesia.</p>

<h3><strong>Misi:</strong></h3>
<ol>
<li>Mengembangkan seni peran dan ekspresi mahasiswa</li>
<li>Menyampaikan pesan moral dan sosial melalui pertunjukan</li>
<li>Melestarikan cerita rakyat dan nilai budaya Indonesia</li>
<li>Membangun kepercayaan diri dan kemampuan public speaking</li>
</ol>
</div>',
                'description' => '<div class="description">
<p><strong>UKM Teater</strong> adalah wadah kreativitas seni peran yang menggugah dan menginspirasi.</p>
<p>🎭 <strong>Karya Unggulan:</strong></p>
<ul>
<li>📚 Drama adaptasi sastra Indonesia klasik</li>
<li>🎪 Teater rakyat dan cerita folklor Nusantara</li>
<li>🎬 Drama musikal dengan tema kontemporer</li>
<li>📝 Workshop penulisan naskah dan sutradara</li>
<li>🏆 Festival teater mahasiswa tingkat nasional</li>
</ul>
<p><em>Hidup adalah panggung, dan kita semua adalah pemeran utamanya!</em></p>
</div>',
            ],
            'UKM Foto' => [
                'vision_mission' => '<div class="vision-mission">
<h3><strong>Visi:</strong></h3>
<p>Menjadi komunitas fotografi mahasiswa yang mendokumentasikan keindahan Indonesia dan menceritakan kisah inspiratif melalui lensa.</p>

<h3><strong>Misi:</strong></h3>
<ol>
<li>Mengembangkan kemampuan fotografi dengan teknik profesional</li>
<li>Mendokumentasikan kekayaan alam dan budaya Indonesia</li>
<li>Menyuarakan isu sosial melalui foto jurnalistik</li>
<li>Membangun apresiasi seni visual di kalangan mahasiswa</li>
</ol>
</div>',
                'description' => '<div class="description">
<p><strong>UKM Fotografi</strong> adalah komunitas visual storyteller yang mengabadikan momen berharga Indonesia.</p>
<p>📷 <strong>Spesialisasi:</strong></p>
<ul>
<li>🌄 Landscape photography wisata Indonesia</li>
<li>👥 Portrait dan human interest photography</li>
<li>📰 Jurnalistik dan dokumenter sosial</li>
<li>🎨 Fine art dan street photography</li>
<li>📚 Workshop editing dan teknik fotografi</li>
</ul>
<p><em>Satu frame, seribu cerita tentang Indonesia!</em></p>
</div>',
            ],
            'UKM Sinema' => [
                'vision_mission' => '<div class="vision-mission">
<h3><strong>Visi:</strong></h3>
<p>Menjadi komunitas sinematografi yang menghasilkan karya film berkualitas dan berkarakter Indonesia.</p>

<h3><strong>Misi:</strong></h3>
<ol>
<li>Mengembangkan kemampuan produksi film dan video mahasiswa</li>
<li>Memproduksi karya sinema yang mencerminkan nilai budaya Indonesia</li>
<li>Membangun industri kreatif perfilman di tingkat kampus</li>
<li>Menyuarakan aspirasi generasi muda melalui medium visual</li>
</ol>
</div>',
                'description' => '<div class="description">
<p><strong>UKM Sinematografi</strong> adalah rumah bagi para pembuat film muda Indonesia.</p>
<p>🎬 <strong>Karya dan Kegiatan:</strong></p>
<ul>
<li>🎞️ Produksi film pendek dengan tema lokal</li>
<li>📹 Dokumenter sejarah dan budaya Indonesia</li>
<li>🎪 Workshop cinematography dan editing</li>
<li>🏆 Festival film mahasiswa dan kompetisi video</li>
<li>📺 Konten kreatif untuk media sosial kampus</li>
</ul>
<p><em>Ceritakan Indonesia melalui layar perak!</em></p>
</div>',
            ],
            'UKM Football' => [
                'vision_mission' => '<div class="vision-mission">
<h3><strong>Visi:</strong></h3>
<p>Menjadi tim sepak bola mahasiswa yang berprestasi, sportif, dan menjunjung tinggi fair play dalam setiap pertandingan.</p>

<h3><strong>Misi:</strong></h3>
<ol>
<li>Mengembangkan skill dan teknik sepak bola mahasiswa</li>
<li>Membangun mental juara dengan sportivitas tinggi</li>
<li>Mempromosikan hidup sehat melalui olahraga</li>
<li>Mengharumkan nama universitas di kompetisi sepak bola</li>
</ol>
</div>',
                'description' => '<div class="description">
<p><strong>UKM Sepak Bola</strong> adalah kebanggaan kampus dalam arena sepak bola mahasiswa.</p>
<p>⚽ <strong>Program Pelatihan:</strong></p>
<ul>
<li>🥅 Technical training (shooting, passing, dribbling)</li>
<li>💪 Physical conditioning dan stamina building</li>
<li>🧠 Tactical awareness dan game strategy</li>
<li>🏆 Kompetisi LIMA Football dan turnamen antar kampus</li>
<li>🤝 Friendly match dengan klub profesional</li>
</ul>
<p><em>Satu tim, satu jiwa, satu tujuan: juara!</em></p>
</div>',
            ],
            'UKM Badminton' => [
                'vision_mission' => '<div class="vision-mission">
<h3><strong>Visi:</strong></h3>
<p>Menjadi tim bulu tangkis mahasiswa terbaik yang meneruskan tradisi keemasan bulu tangkis Indonesia.</p>

<h3><strong>Misi:</strong></h3>
<ol>
<li>Mengembangkan teknik dan strategi bulu tangkis tingkat kompetitif</li>
<li>Membina mental dan fisik atlet bulu tangkis</li>
<li>Melestarikan prestasi bulu tangkis sebagai olahraga kebanggaan Indonesia</li>
<li>Mencetak atlet bulu tangkis yang berkualitas nasional</li>
</ol>
</div>',
                'description' => '<div class="description">
<p><strong>UKM Bulu Tangkis</strong> melanjutkan tradisi emas bulu tangkis Indonesia di tingkat mahasiswa.</p>
<p>🏸 <strong>Program Unggulan:</strong></p>
<ul>
<li>🎯 Technical coaching dengan pelatih berlisensi</li>
<li>⚡ Speed dan agility training</li>
<li>🧘 Mental training dan sports psychology</li>
<li>🏆 Kompetisi LIMA Badminton dan kejuaraan daerah</li>
<li>👥 Sparring dengan atlet dan klub nasional</li>
</ul>
<p><em>Merah Putih berkibar di setiap smash kemenangan!</em></p>
</div>',
            ],
        ];

        foreach ($units as $unit) {
            $profileData = $profiles[$unit->alias] ?? [
                'vision_mission' => '<div class="vision-mission">
<h3><strong>Visi:</strong></h3>
<p>Menjadi organisasi mahasiswa yang unggul, berintegritas, dan berkontribusi positif bagi kemajuan Indonesia.</p>

<h3><strong>Misi:</strong></h3>
<ol>
<li>Mengembangkan potensi mahasiswa sesuai bidang keahlian</li>
<li>Membangun karakter kepemimpinan yang berakhlak mulia</li>
<li>Berkontribusi nyata untuk masyarakat dan bangsa</li>
<li>Mewujudkan Indonesia yang maju dan sejahtera</li>
</ol>
</div>',
                'description' => '<div class="description">
<p><strong>' . $unit->name . '</strong> adalah organisasi mahasiswa yang berkomitmen mengembangkan <em>potensi dan karakter mahasiswa</em>.</p>
<p>🎯 <strong>Program Kerja:</strong></p>
<ul>
<li>📚 Pengembangan akademik dan soft skills</li>
<li>🤝 Kegiatan sosial kemasyarakatan</li>
<li>🏆 Kompetisi dan lomba prestasi</li>
<li>💡 Workshop dan seminar inspiratif</li>
</ul>
<p><em>Bergabunglah untuk menjadi bagian perubahan positif Indonesia!</em></p>
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
                    'background_photo' => $this->getBackgroundPhoto($unit->category, $unit->alias),
                    'created_at' => Carbon::create($period, 1, 1),
                    'updated_at' => Carbon::create($period, 12, 31),
                ]);
            }
        }

        $this->command->info('UnitKegiatanProfile seeder completed successfully!');
        $this->command->info('Created profiles for ' . $units->count() . ' units across 3 periods with authentic Indonesian university content');
    }

    /**
     * Get background photo based on UKM category and alias
     */
    private function getBackgroundPhoto($category, $alias)
    {
        $backgroundPhotos = [
            'Himpunan' => [
                'backgrounds/tech-campus-1.jpg',
                'backgrounds/computer-lab-1.jpg',
                'backgrounds/engineering-1.jpg',
                'backgrounds/innovation-center-1.jpg',
                'backgrounds/lecture-hall-1.jpg'
            ],
            'UKM Seni' => [
                'backgrounds/art-gallery-1.jpg',
                'backgrounds/music-studio-1.jpg',
                'backgrounds/creative-space-1.jpg',
                'backgrounds/performance-hall-1.jpg',
                'backgrounds/cultural-center-1.jpg'
            ],
            'UKM Olahraga' => [
                'backgrounds/sports-field-1.jpg',
                'backgrounds/gymnasium-1.jpg',
                'backgrounds/fitness-center-1.jpg',
                'backgrounds/athletics-track-1.jpg',
                'backgrounds/sports-complex-1.jpg'
            ],
            'UKM Kemasyarakatan' => [
                'backgrounds/community-service-1.jpg',
                'backgrounds/volunteer-activity-1.jpg',
                'backgrounds/social-work-1.jpg',
                'backgrounds/outreach-program-1.jpg'
            ],
            'UKM Keagamaan' => [
                'backgrounds/mosque-campus-1.jpg',
                'backgrounds/prayer-room-1.jpg',
                'backgrounds/religious-activity-1.jpg',
                'backgrounds/spiritual-gathering-1.jpg'
            ],
            'UKM Teknologi' => [
                'backgrounds/tech-lab-1.jpg',
                'backgrounds/robotics-lab-1.jpg',
                'backgrounds/innovation-hub-1.jpg',
                'backgrounds/maker-space-1.jpg'
            ],
            'UKM Keilmuan' => [
                'backgrounds/research-center-1.jpg',
                'backgrounds/academic-discussion-1.jpg',
                'backgrounds/seminar-room-1.jpg',
                'backgrounds/library-study-1.jpg'
            ],
            'UKM Kewirausahaan' => [
                'backgrounds/business-incubator-1.jpg',
                'backgrounds/startup-space-1.jpg',
                'backgrounds/entrepreneurship-hub-1.jpg',
                'backgrounds/business-meeting-1.jpg'
            ],
            'UKM Media' => [
                'backgrounds/media-studio-1.jpg',
                'backgrounds/broadcasting-room-1.jpg',
                'backgrounds/journalism-office-1.jpg',
                'backgrounds/newsroom-1.jpg'
            ],
            'default' => [
                'backgrounds/campus-view-1.jpg',
                'backgrounds/student-center-1.jpg',
                'backgrounds/university-hall-1.jpg',
                'backgrounds/campus-garden-1.jpg'
            ]
        ];

        $categoryPhotos = $backgroundPhotos[$category] ?? $backgroundPhotos['default'];

        // Return null 25% of the time to simulate some profiles without background photos
        if (rand(0, 3) === 0) {
            return null;
        }

        return $categoryPhotos[array_rand($categoryPhotos)];
    }
}
