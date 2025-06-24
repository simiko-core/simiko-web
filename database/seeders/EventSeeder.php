<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\UnitKegiatan;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $units = UnitKegiatan::all();
        foreach ($units as $unit) {
            Event::factory()->count(2)->create([
                'unit_kegiatan_id' => $unit->id,
            ]);
        }
    }
}
