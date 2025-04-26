<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitKegiatan extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\UnitKegiatan::create([
            'name' => 'Himpunan Mahasiswa Informatika',
            'description' => 'Himpunan Mahasiswa Informatika adalah organisasi mahasiswa yang berfokus pada pengembangan ilmu komputer dan teknologi informasi.',
            'logo' => 'https://example.com/logo-himatif.png',
        ]);

        \App\Models\UnitKegiatan::create([
            'name' => 'Himpunan Mahasiswa Teknik Elektro',
            'description' => 'Himpunan Mahasiswa Teknik Elektro adalah organisasi mahasiswa yang berfokus pada pengembangan ilmu teknik elektro.',
            'logo' => 'https://example.com/logo-himatel.png',
        ]);

        \App\Models\UnitKegiatan::create([
            'name' => 'Himpunan Mahasiswa Teknik Mesin',
            'description' => 'Himpunan Mahasiswa Teknik Mesin adalah organisasi mahasiswa yang berfokus pada pengembangan ilmu teknik mesin.',
            'logo' => 'https://example.com/logo-himatelmesin.png',
        ]);

        \App\Models\UnitKegiatan::create([
            'name' => 'Himpunan Mahasiswa Teknik Sipil',
            'description' => 'Himpunan Mahasiswa Teknik Sipil adalah organisasi mahasiswa yang berfokus pada pengembangan ilmu teknik sipil.',
            'logo' => 'https://example.com/logo-himatelsipil.png',
        ]);
    }
}
