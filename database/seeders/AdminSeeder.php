<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\User;
use App\Models\UnitKegiatan;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create 5 admins, each linked to a user and a unit kegiatan
        $users = User::inRandomOrder()->take(5)->get();
        $units = UnitKegiatan::inRandomOrder()->take(5)->get();
        foreach (range(0, 4) as $i) {
            Admin::create([
                'user_id' => $users[$i]->id ?? User::factory()->create()->id,
                'unit_kegiatan_id' => $units[$i]->id ?? UnitKegiatan::factory()->create()->id,
            ]);
        }
    }
}
