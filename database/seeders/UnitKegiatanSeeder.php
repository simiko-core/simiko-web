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
            // Himpunan Mahasiswa (Academic Student Organizations)
            [
                'name' => 'Himpunan Mahasiswa Teknik Informatika',
                'alias' => 'HMTI',
                'category' => 'Himpunan',
                'logo' => 'logo_unit_kegiatan/hmti-logo.png',
            ],
            [
                'name' => 'Himpunan Mahasiswa Teknik Elektro',
                'alias' => 'HMTE',
                'category' => 'Himpunan',
                'logo' => 'logo_unit_kegiatan/hmte-logo.png',
            ],
            [
                'name' => 'Himpunan Mahasiswa Sistem Informasi',
                'alias' => 'HMSI',
                'category' => 'Himpunan',
                'logo' => 'logo_unit_kegiatan/hmsi-logo.png',
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
                'name' => 'Himpunan Mahasiswa Ekonomi',
                'alias' => 'HME',
                'category' => 'Himpunan',
                'logo' => 'logo_unit_kegiatan/hme-logo.png',
            ],

            // UKM Seni & Budaya
            [
                'name' => 'Unit Kegiatan Mahasiswa Seni Tari Nusantara',
                'alias' => 'UKM Tari',
                'category' => 'UKM Seni',
                'logo' => 'logo_unit_kegiatan/ukm-tari-logo.png',
            ],
            [
                'name' => 'Unit Kegiatan Mahasiswa Paduan Suara Mahasiswa',
                'alias' => 'UKM PSM',
                'category' => 'UKM Seni',
                'logo' => 'logo_unit_kegiatan/ukm-psm-logo.png',
            ],
            [
                'name' => 'Unit Kegiatan Mahasiswa Teater',
                'alias' => 'UKM Teater',
                'category' => 'UKM Seni',
                'logo' => 'logo_unit_kegiatan/ukm-teater-logo.png',
            ],
            [
                'name' => 'Unit Kegiatan Mahasiswa Fotografi',
                'alias' => 'UKM Foto',
                'category' => 'UKM Seni',
                'logo' => 'logo_unit_kegiatan/ukm-foto-logo.png',
            ],
            [
                'name' => 'Unit Kegiatan Mahasiswa Sinematografi',
                'alias' => 'UKM Sinema',
                'category' => 'UKM Seni',
                'logo' => 'logo_unit_kegiatan/ukm-sinema-logo.png',
            ],

            // UKM Olahraga
            [
                'name' => 'Unit Kegiatan Mahasiswa Sepak Bola',
                'alias' => 'UKM Football',
                'category' => 'UKM Olahraga',
                'logo' => 'logo_unit_kegiatan/ukm-football-logo.png',
            ],
            [
                'name' => 'Unit Kegiatan Mahasiswa Bulu Tangkis',
                'alias' => 'UKM Badminton',
                'category' => 'UKM Olahraga',
                'logo' => 'logo_unit_kegiatan/ukm-badminton-logo.png',
            ],
            [
                'name' => 'Unit Kegiatan Mahasiswa Bola Basket',
                'alias' => 'UKM Basket',
                'category' => 'UKM Olahraga',
                'logo' => 'logo_unit_kegiatan/ukm-basket-logo.png',
            ],
            [
                'name' => 'Unit Kegiatan Mahasiswa Futsal',
                'alias' => 'UKM Futsal',
                'category' => 'UKM Olahraga',
                'logo' => 'logo_unit_kegiatan/ukm-futsal-logo.png',
            ],

            // UKM Kemasyarakatan
            [
                'name' => 'Unit Kegiatan Mahasiswa Pecinta Alam',
                'alias' => 'UKM PA',
                'category' => 'UKM Kemasyarakatan',
                'logo' => 'logo_unit_kegiatan/ukm-pa-logo.png',
            ],
            [
                'name' => 'Unit Kegiatan Mahasiswa Palang Merah Remaja',
                'alias' => 'UKM PMR',
                'category' => 'UKM Kemasyarakatan',
                'logo' => 'logo_unit_kegiatan/ukm-pmr-logo.png',
            ],
            [
                'name' => 'Unit Kegiatan Mahasiswa Pramuka',
                'alias' => 'UKM Pramuka',
                'category' => 'UKM Kemasyarakatan',
                'logo' => 'logo_unit_kegiatan/ukm-pramuka-logo.png',
            ],

            // UKM Keagamaan
            [
                'name' => 'Unit Kegiatan Mahasiswa Kerohanian Islam',
                'alias' => 'UKM KKI',
                'category' => 'UKM Keagamaan',
                'logo' => 'logo_unit_kegiatan/ukm-kki-logo.png',
            ],
            [
                'name' => 'Unit Kegiatan Mahasiswa Kerohanian Kristen',
                'alias' => 'UKM KKK',
                'category' => 'UKM Keagamaan',
                'logo' => 'logo_unit_kegiatan/ukm-kkk-logo.png',
            ],

            // UKM Teknologi & Keilmuan
            [
                'name' => 'Unit Kegiatan Mahasiswa Robotika',
                'alias' => 'UKM Robot',
                'category' => 'UKM Teknologi',
                'logo' => 'logo_unit_kegiatan/ukm-robot-logo.png',
            ],
            [
                'name' => 'Unit Kegiatan Mahasiswa Riset',
                'alias' => 'UKM Riset',
                'category' => 'UKM Keilmuan',
                'logo' => 'logo_unit_kegiatan/ukm-riset-logo.png',
            ],
            [
                'name' => 'Unit Kegiatan Mahasiswa Debat Bahasa Inggris',
                'alias' => 'UKM EDS',
                'category' => 'UKM Keilmuan',
                'logo' => 'logo_unit_kegiatan/ukm-eds-logo.png',
            ],

            // UKM Kewirausahaan & Media
            [
                'name' => 'Unit Kegiatan Mahasiswa Kewirausahaan',
                'alias' => 'UKM Wirausaha',
                'category' => 'UKM Kewirausahaan',
                'logo' => 'logo_unit_kegiatan/ukm-wirausaha-logo.png',
            ],
            [
                'name' => 'Unit Kegiatan Mahasiswa Pers Mahasiswa',
                'alias' => 'UKM Press',
                'category' => 'UKM Media',
                'logo' => 'logo_unit_kegiatan/ukm-press-logo.png',
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
        $this->command->info('Created ' . count($ukms) . ' unit kegiatan with authentic Indonesian university organization names');

        // Show category distribution
        $categories = collect($ukms)->groupBy('category');
        $this->command->info('Category distribution:');
        foreach ($categories as $category => $items) {
            $this->command->info("- {$category}: " . $items->count() . ' organizations');
        }
    }
}
