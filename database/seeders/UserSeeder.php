<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;
use App\Models\UnitKegiatan;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create super admin user
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('superadmin'),
            'phone' => '081234567890',
            'photo' => null,
            'email_verified_at' => now(),
        ]);

        // Setup roles and permissions
        Artisan::call('shield:super-admin', ['--user' => $superAdmin->id]);
        Artisan::call('shield:install admin');

        // Get all UKMs to create admin users for each
        $ukms = UnitKegiatan::all();

        if ($ukms->isNotEmpty()) {
            foreach ($ukms as $index => $ukm) {
                // Create admin user for each UKM
                $adminUser = User::create([
                    'name' => $ukm->alias . ' Admin',
                    'email' => strtolower($ukm->alias) . '@gmail.com',
                    'password' => Hash::make(strtolower($ukm->alias)),
                    'phone' => '0812345678' . str_pad($index + 10, 2, '0', STR_PAD_LEFT),
                    'photo' => null,
                    'email_verified_at' => now(),
                ]);

                // Assign admin_ukm role
                $adminUser->assignRole('admin_ukm');

                // Create admin record
                Admin::create([
                    'user_id' => $adminUser->id,
                    'unit_kegiatan_id' => $ukm->id,
                ]);
            }
        }

        // Create regular users for testing
        $userNames = [
            'Ahmad Rizki Rahman',
            'Siti Nurhaliza',
            'Budi Santoso',
            'Dewi Sartika',
            'Fajar Nugroho',
            'Indira Putri',
            'Joko Widodo',
            'Kartika Sari',
            'Luthfi Hakim',
            'Maya Angelica',
            'Nurul Hidayah',
            'Oscar Pratama',
            'Putri Ananda',
            'Qori Abdullah',
            'Rina Puspitasari',
            'Saiful Bahri',
            'Tania Melinda',
            'Umar Syahputra',
            'Vina Amelia',
            'Wulan Dari'
        ];

        $domains = ['gmail.com', 'yahoo.com', 'outlook.com', 'student.univ.ac.id'];

        foreach ($userNames as $index => $name) {
            $firstName = explode(' ', $name)[0];
            $lastName = explode(' ', $name)[1] ?? '';
            $email = strtolower($firstName . '.' . $lastName) . '@' . $domains[array_rand($domains)];
            
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password123'),
                'phone' => '0856789012' . str_pad($index, 2, '0', STR_PAD_LEFT),
                'photo' => null,
                'email_verified_at' => rand(0, 1) ? now() : null, // Some verified, some not
            ]);
        }

        $this->command->info('User seeder completed successfully!');
        $this->command->info('Created 1 super admin, ' . $ukms->count() . ' UKM admins, and ' . count($userNames) . ' regular users');
    }
}
