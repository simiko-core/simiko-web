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

        // Create regular users with realistic Indonesian names and patterns
        $userNames = [
            'Ahmad Fauzi Ramadhan',
            'Siti Nurhidayah Putri',
            'Budi Prabowo Santoso',
            'Dewi Kartika Sari',
            'Rizky Fajar Nugroho',
            'Maya Indira Kusuma',
            'Dimas Aldi Pratama',
            'Putri Ananda Wijaya',
            'Luthfi Hakim Al-Farisi',
            'Rina Puspita Maharani',
            'Bayu Satria Wibowo',
            'Citra Nur Azizah',
            'Eko Prasetyo Utomo',
            'Fitri Rahmawati',
            'Gilang Ramadhan',
            'Hana Safitri',
            'Irfan Maulana',
            'Jihan Nurmalasari',
            'Kevin Adiputra',
            'Laras Pramesti',
            'Muhammad Iqbal Firdaus',
            'Nadia Khairunnisa',
            'Omar Syahputra',
            'Putri Maharani',
            'Qonita Rahma',
            'Reza Firmansyah',
            'Shinta Dewi Lestari',
            'Taufik Hidayat',
            'Uswatun Hasanah',
            'Vina Amelia Sari',
            'Wahyu Kristianto',
            'Xenia Putri Cantika',
            'Yoga Pratama',
            'Zahra Aulia Rahman',
            'Arif Budiman',
            'Bella Octaviani',
            'Cahyo Nugroho',
            'Diana Puspitasari',
            'Eko Saputro',
            'Fanny Wijayanti'
        ];

        // More diverse email domains including university emails
        $domains = [
            'gmail.com',
            'yahoo.com',
            'outlook.com',
            'hotmail.com',
            'student.univ.ac.id',
            'mahasiswa.ac.id',
            'students.ac.id',
            'mail.student.ac.id'
        ];

        // More realistic Indonesian phone prefixes
        $phoneProviders = [
            '0812',
            '0813',
            '0821',
            '0822',
            '0823', // Telkomsel
            '0814',
            '0815',
            '0816',
            '0855',
            '0856',
            '0857',
            '0858', // Indosat
            '0817',
            '0818',
            '0819',
            '0859',
            '0877',
            '0878', // XL
            '0838',
            '0831',
            '0832',
            '0833', // Axis
            '0895',
            '0896',
            '0897',
            '0898',
            '0899' // Three
        ];

        foreach ($userNames as $index => $name) {
            // Create more realistic email from name
            $nameParts = explode(' ', $name);
            $firstName = strtolower($nameParts[0]);
            $lastName = strtolower(end($nameParts));

            // Various email patterns
            $emailPatterns = [
                $firstName . '.' . $lastName,
                $firstName . $lastName,
                $firstName . '.' . substr($lastName, 0, 1),
                $firstName . rand(10, 99),
                substr($firstName, 0, 3) . '.' . $lastName
            ];

            $emailPattern = $emailPatterns[array_rand($emailPatterns)];
            $domain = $domains[array_rand($domains)];
            $email = $emailPattern . '@' . $domain;

            // Generate realistic Indonesian phone number
            $phoneProvider = $phoneProviders[array_rand($phoneProviders)];
            $phoneNumber = $phoneProvider . rand(10000000, 99999999);

            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password123'),
                'phone' => $phoneNumber,
                'photo' => null,
                'email_verified_at' => rand(0, 1) ? now() : null, // Some verified, some not
            ]);
        }

        $this->command->info('User seeder completed successfully!');
        $this->command->info('Created 1 super admin, ' . $ukms->count() . ' UKM admins, and ' . count($userNames) . ' regular users');
    }
}
