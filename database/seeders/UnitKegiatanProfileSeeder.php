<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UnitKegiatanProfile;
use App\Models\UnitKegiatan;

class UnitKegiatanProfileSeeder extends Seeder
{
    public function run(): void
    {
        $units = UnitKegiatan::all();
        foreach ($units as $unit) {
            UnitKegiatanProfile::factory()->create([
                'unit_kegiatan_id' => $unit->id,
            ]);
        }
    }
}
