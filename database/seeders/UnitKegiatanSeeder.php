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
                'logo' => 'logo_unit_kegiatan/hmif-logo.png',
            ],
            [
                'name' => 'Himpunan Mahasiswa Teknik Elektro',
                'alias' => 'HMTE',
                'logo' => 'logo_unit_kegiatan/hmte-logo.png',
            ],
            [
                'name' => 'Himpunan Mahasiswa Teknik Mesin',
                'alias' => 'HMTM',
                'logo' => 'logo_unit_kegiatan/hmtm-logo.png',
            ],
            [
                'name' => 'Himpunan Mahasiswa Teknik Sipil',
                'alias' => 'HMTS',
                'logo' => 'logo_unit_kegiatan/hmts-logo.png',
            ],
            [
                'name' => 'Unit Kegiatan Mahasiswa Fotografi',
                'alias' => 'UKM Foto',
                'logo' => 'logo_unit_kegiatan/ukm-foto-logo.png',
            ],
            [
                'name' => 'Unit Kegiatan Mahasiswa Musik',
                'alias' => 'UKM Musik',
                'logo' => 'logo_unit_kegiatan/ukm-musik-logo.png',
            ],
            [
                'name' => 'Unit Kegiatan Mahasiswa Olahraga',
                'alias' => 'UKM Sport',
                'logo' => 'logo_unit_kegiatan/ukm-sport-logo.png',
            ],
            [
                'name' => 'Unit Kegiatan Mahasiswa Pecinta Alam',
                'alias' => 'UKM PA',
                'logo' => 'logo_unit_kegiatan/ukm-pa-logo.png',
            ],
            [
                'name' => 'Unit Kegiatan Mahasiswa Robotika',
                'alias' => 'UKM Robot',
                'logo' => 'logo_unit_kegiatan/ukm-robot-logo.png',
            ],
            [
                'name' => 'Unit Kegiatan Mahasiswa Debat',
                'alias' => 'UKM Debat',
                'logo' => 'logo_unit_kegiatan/ukm-debat-logo.png',
            ],
        ];

        foreach ($ukms as $ukm) {
            UnitKegiatan::create([
                'name' => $ukm['name'],
                'alias' => $ukm['alias'],
                'logo' => $ukm['logo'],
                'open_registration' => rand(0, 1) == 1, // Random registration status
            ]);
        }

        $this->command->info('UnitKegiatan seeder completed successfully!');
        $this->command->info('Created ' . count($ukms) . ' unit kegiatan');
    }
} 