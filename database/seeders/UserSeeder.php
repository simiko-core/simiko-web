<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

class UserSeeder extends Seeder
{
    public function run(): void
    {
    
        // Create a specific user with admin role
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'superadmin@gmail.com',
            'password' => bcrypt('superadmin'),
        ]);

        Artisan::call('shield:super-admin');
        Artisan::call('shield:install admin');

        // Create specific users for each unit kegiatan
        $hmif = User::factory()->create([
            'name' => 'HMIF Admin',
            'email' => 'hmif@gmail.com',
            'password' => bcrypt('hmif'),
        ]);
        $hmif->assignRole('admin_ukm');

        $hmte = User::factory()->create([
            'name' => 'HMTE Admin',
            'email' => 'hmte@gmail.com',
            'password' => bcrypt('hmte'),
        ]);
        $hmte->assignRole('admin_ukm');

        $hmtm = User::factory()->create([
            'name' => 'HMTM Admin',
            'email' => 'hmtm@gmail.com',
            'password' => bcrypt('hmtm'),
        ]);
        $hmtm->assignRole('admin_ukm');

        $hmts = User::factory()->create([
            'name' => 'HMTS Admin',
            'email' => 'hmts@gmail.com',
            'password' => bcrypt('hmts'),
        ]);
        $hmts->assignRole('admin_ukm');
    }
}
