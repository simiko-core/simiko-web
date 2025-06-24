<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PendaftaranAnggota;
use App\Models\User;
use App\Models\UnitKegiatan;
use Carbon\Carbon;

class PendaftaranAnggotaSeeder extends Seeder
{
    public function run(): void
    {
        // Get regular users (exclude super admin and UKM admins)
        $users = User::whereDoesntHave('admin')->get();
        $units = UnitKegiatan::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('No regular users found. Please run UserSeeder first.');
            return;
        }
        
        if ($units->isEmpty()) {
            $this->command->warn('No UKM found. Please run UnitKegiatanSeeder first.');
            return;
        }

        $statuses = ['pending', 'accepted', 'rejected'];
        $createdRegistrations = 0;

        foreach ($users as $user) {
            // Each user can register to 1-3 UKMs
            $numRegistrations = rand(1, 3);
            $selectedUnits = $units->random($numRegistrations);

            foreach ($selectedUnits as $unit) {
                // Avoid duplicate registrations
                $existingRegistration = PendaftaranAnggota::where('user_id', $user->id)
                    ->where('unit_kegiatan_id', $unit->id)
                    ->first();
                
                if (!$existingRegistration) {
                    $status = $statuses[array_rand($statuses)];
                    $createdAt = Carbon::now()->subDays(rand(1, 90));
                    
                    PendaftaranAnggota::create([
                        'user_id' => $user->id,
                        'unit_kegiatan_id' => $unit->id,
                        'status' => $status,
                        'created_at' => $createdAt,
                        'updated_at' => $status === 'pending' ? $createdAt : $createdAt->addDays(rand(1, 7)),
                    ]);
                    
                    $createdRegistrations++;
                }
            }
        }

        // Create some additional pending registrations for recent dates
        $recentUsers = $users->random(min(10, $users->count()));
        foreach ($recentUsers as $user) {
            $availableUnits = $units->filter(function ($unit) use ($user) {
                return !PendaftaranAnggota::where('user_id', $user->id)
                    ->where('unit_kegiatan_id', $unit->id)
                    ->exists();
            });

            if ($availableUnits->isNotEmpty()) {
                $unit = $availableUnits->random();
                $createdAt = Carbon::now()->subDays(rand(1, 7));
                
                PendaftaranAnggota::create([
                    'user_id' => $user->id,
                    'unit_kegiatan_id' => $unit->id,
                    'status' => 'pending',
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
                
                $createdRegistrations++;
            }
        }

        $this->command->info('PendaftaranAnggota seeder completed successfully!');
        $this->command->info('Created ' . $createdRegistrations . ' registrations');
        
        // Show status distribution
        foreach ($statuses as $status) {
            $count = PendaftaranAnggota::where('status', $status)->count();
            $this->command->info('- ' . ucfirst($status) . ': ' . $count . ' registrations');
        }
    }
}
