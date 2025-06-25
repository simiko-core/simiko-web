<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UnitKegiatan;

class UnitKegiatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ukms = [
            [
                'name' => 'Himpunan Mahasiswa Informatika',
                'alias' => 'HMIF',
                'category' => 'Himpunan',
                'logo' => 'logo_unit_kegiatan/hmif-logo.png',
            ],
            [
                'name' => 'Himpunan Mahasiswa Teknik Elektro',
                'alias' => 'HMTE',
                'category' => 'Himpunan',
                'logo' => 'logo_unit_kegiatan/hmte-logo.png',
            ],
            [
                'name' => 'Himpunan Mahasiswa Teknik Mesin',
                'alias' => 'HMTM',
                'category' => 'Himpunan',
                'logo' => 'logo_unit_kegiatan/hmtm-logo.png',
            ],
            [
                'name' => 'Himpunan Mahasiswa Teknik Sipil',
                'alias' => 'HMTS',
                'category' => 'Himpunan',
                'logo' => 'logo_unit_kegiatan/hmts-logo.png',
            ],
            [
                'name' => 'Unit Kegiatan Mahasiswa Fotografi',
                'alias' => 'UKM Foto',
                'category' => 'UKM Seni',
                'logo' => 'logo_unit_kegiatan/ukm-foto-logo.png',
            ],
            [
                'name' => 'Unit Kegiatan Mahasiswa Musik',
                'alias' => 'UKM Musik',
                'category' => 'UKM Seni',
                'logo' => 'logo_unit_kegiatan/ukm-musik-logo.png',
            ],
            [
                'name' => 'Unit Kegiatan Mahasiswa Olahraga',
                'alias' => 'UKM Sport',
                'category' => 'UKM Olahraga',
                'logo' => 'logo_unit_kegiatan/ukm-sport-logo.png',
            ],
            [
                'name' => 'Unit Kegiatan Mahasiswa Pecinta Alam',
                'alias' => 'UKM PA',
                'category' => 'UKM Kemasyarakatan',
                'logo' => 'logo_unit_kegiatan/ukm-pa-logo.png',
            ],
            [
                'name' => 'Unit Kegiatan Mahasiswa Robotika',
                'alias' => 'UKM Robot',
                'category' => 'UKM Teknologi',
                'logo' => 'logo_unit_kegiatan/ukm-robot-logo.png',
            ],
            [
                'name' => 'Unit Kegiatan Mahasiswa Debat',
                'alias' => 'UKM Debat',
                'category' => 'UKM Keilmuan',
                'logo' => 'logo_unit_kegiatan/ukm-debat-logo.png',
            ],
        ];

        foreach ($ukms as $ukm) {
            UnitKegiatan::create([
                'name' => $ukm['name'],
                'alias' => $ukm['alias'],
                'category' => $ukm['category'],
                'logo' => $ukm['logo'],
                'open_registration' => rand(0, 1) == 1, // Random registration status
            ]);
        }

        $this->command->info('UnitKegiatan seeder completed successfully!');
        $this->command->info('Created ' . count($ukms) . ' unit kegiatan');
    }
} 