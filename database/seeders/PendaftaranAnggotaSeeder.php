<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PendaftaranAnggota;
use App\Models\User;
use App\Models\UnitKegiatan;

class PendaftaranAnggotaSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::inRandomOrder()->take(10)->get();
        $units = UnitKegiatan::inRandomOrder()->take(5)->get();
        foreach ($users as $user) {
            PendaftaranAnggota::create([
                'user_id' => $user->id,
                'unit_kegiatan_id' => $units->random()->id,
            ]);
        }
    }
}
