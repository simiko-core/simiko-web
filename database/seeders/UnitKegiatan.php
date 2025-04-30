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
            'logo' => 'https://example.com/logo-himatif.png',
        ]);

        \App\Models\UnitKegiatan::create([
            'name' => 'Himpunan Mahasiswa Teknik Elektro',
            'logo' => 'https://example.com/logo-himatel.png',
        ]);

        \App\Models\UnitKegiatan::create([
            'name' => 'Himpunan Mahasiswa Teknik Mesin',
            'logo' => 'https://example.com/logo-himatelmesin.png',
        ]);

        \App\Models\UnitKegiatan::create([
            'name' => 'Himpunan Mahasiswa Teknik Sipil',
            'logo' => 'https://example.com/logo-himatelsipil.png',
        ]);
    }
}
