<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            UnitKegiatan::class,
            ShieldSeeder::class,
            UserSeeder::class,
           
            PostSeeder::class,
            BannerSeeder::class,
            EventSeeder::class,
            EventCategorySeeder::class,
            PendaftaranAnggotaSeeder::class,
            UnitKegiatanProfileSeeder::class,
        ]);

        

        // Create a super admin user & register shield to admin panel
       
    }
}
