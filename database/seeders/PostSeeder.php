<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\UnitKegiatan;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $units = UnitKegiatan::all();
        foreach ($units as $unit) {
            Post::factory()->count(3)->create([
                'unit_kegiatan_id' => $unit->id,
            ]);
        }
    }
}
