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

        $this->command->info('Creating realistic Indonesian university membership registrations...');

        $statuses = ['pending', 'accepted', 'rejected'];
        $createdRegistrations = 0;

        // Create semester-based registration waves
        $createdRegistrations += $this->createSemesterRegistrations($users, $units, 'genap_2024');
        $createdRegistrations += $this->createSemesterRegistrations($users, $units, 'ganjil_2023');

        // Create priority registrations for competitive UKMs
        $createdRegistrations += $this->createPriorityRegistrations($users, $units);

        // Create open recruitment registrations
        $createdRegistrations += $this->createOpenRecruitmentRegistrations($users, $units);

        // Create recent pending registrations (ongoing recruitment)
        $createdRegistrations += $this->createRecentPendingRegistrations($users, $units);

        $this->command->info('PendaftaranAnggota seeder completed successfully!');
        $this->command->info("Created {$createdRegistrations} total membership registrations");

        $this->displayRegistrationStatistics($units);
    }

    private function createSemesterRegistrations($users, $units, $semester)
    {
        $createdCount = 0;
        $semesterInfo = $this->getSemesterInfo($semester);

        $this->command->info("Creating {$semesterInfo['name']} registration wave...");

        // Different registration patterns based on UKM category
        foreach ($units as $unit) {
            $registrationCount = $this->getRegistrationCountByCategory($unit->category, $semester);
            $selectedUsers = $users->random(min($registrationCount, $users->count()));

            foreach ($selectedUsers as $user) {
                // Avoid duplicate registrations
                if ($this->hasExistingRegistration($user->id, $unit->id)) {
                    continue;
                }

                $status = $this->determineStatusByCategory($unit->category, $semester);
                $createdAt = $this->getRegistrationDate($semesterInfo['period']);
                $updatedAt = $this->getStatusUpdateDate($createdAt, $status);

                PendaftaranAnggota::create([
                    'user_id' => $user->id,
                    'unit_kegiatan_id' => $unit->id,
                    'status' => $status,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]);

                $createdCount++;
            }
        }

        return $createdCount;
    }

    private function createPriorityRegistrations($users, $units)
    {
        $createdCount = 0;
        $competitiveUnits = $units->whereIn('category', ['Himpunan', 'UKM Teknologi', 'UKM Olahraga']);

        $this->command->info('Creating priority registrations for competitive UKMs...');

        foreach ($competitiveUnits as $unit) {
            // High-achieving students tend to apply to competitive UKMs
            $priorityUsers = $users->random(min(rand(8, 15), $users->count()));

            foreach ($priorityUsers as $user) {
                if ($this->hasExistingRegistration($user->id, $unit->id)) {
                    continue;
                }

                // Priority registrations have higher acceptance rate
                $status = $this->weightedRandomStatus(['accepted' => 70, 'pending' => 20, 'rejected' => 10]);
                $createdAt = Carbon::now()->subDays(rand(45, 90)); // Early registration period
                $updatedAt = $this->getStatusUpdateDate($createdAt, $status);

                PendaftaranAnggota::create([
                    'user_id' => $user->id,
                    'unit_kegiatan_id' => $unit->id,
                    'status' => $status,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]);

                $createdCount++;
            }
        }

        return $createdCount;
    }

    private function createOpenRecruitmentRegistrations($users, $units)
    {
        $createdCount = 0;
        $openUnits = $units->whereIn('category', ['UKM Seni', 'UKM Kemasyarakatan', 'UKM Keagamaan']);

        $this->command->info('Creating open recruitment registrations...');

        foreach ($openUnits as $unit) {
            $registrationCount = rand(12, 25); // Open recruitment gets more applications
            $selectedUsers = $users->random(min($registrationCount, $users->count()));

            foreach ($selectedUsers as $user) {
                if ($this->hasExistingRegistration($user->id, $unit->id)) {
                    continue;
                }

                // Open recruitment has high acceptance rate
                $status = $this->weightedRandomStatus(['accepted' => 80, 'pending' => 15, 'rejected' => 5]);
                $createdAt = Carbon::now()->subDays(rand(30, 75));
                $updatedAt = $this->getStatusUpdateDate($createdAt, $status);

                PendaftaranAnggota::create([
                    'user_id' => $user->id,
                    'unit_kegiatan_id' => $unit->id,
                    'status' => $status,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]);

                $createdCount++;
            }
        }

        return $createdCount;
    }

    private function createRecentPendingRegistrations($users, $units)
    {
        $createdCount = 0;
        $this->command->info('Creating recent pending registrations (ongoing recruitment)...');

        // Recent applications that are still being processed
        $recentUsers = $users->random(min(rand(15, 30), $users->count()));

        foreach ($recentUsers as $user) {
            // Each user might apply to 1-2 additional UKMs
            $applicationCount = rand(1, 2);
            $availableUnits = $units->filter(function ($unit) use ($user) {
                return !$this->hasExistingRegistration($user->id, $unit->id);
            });

            if ($availableUnits->count() < $applicationCount) {
                $applicationCount = $availableUnits->count();
            }

            $selectedUnits = $availableUnits->random($applicationCount);

            foreach ($selectedUnits as $unit) {
                $createdAt = Carbon::now()->subDays(rand(1, 14)); // Very recent

                PendaftaranAnggota::create([
                    'user_id' => $user->id,
                    'unit_kegiatan_id' => $unit->id,
                    'status' => 'pending',
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                $createdCount++;
            }
        }

        return $createdCount;
    }

    private function getSemesterInfo($semester)
    {
        return match ($semester) {
            'genap_2024' => [
                'name' => 'Semester Genap 2024',
                'period' => ['start' => 150, 'end' => 90] // Days ago
            ],
            'ganjil_2023' => [
                'name' => 'Semester Ganjil 2023/2024',
                'period' => ['start' => 300, 'end' => 200]
            ],
            default => [
                'name' => 'Regular Period',
                'period' => ['start' => 90, 'end' => 30]
            ]
        };
    }

    private function getRegistrationCountByCategory($category, $semester)
    {
        // Different UKM categories have different appeal and capacity
        $baseCount = match ($category) {
            'Himpunan' => rand(10, 18), // High interest, competitive
            'UKM Seni' => rand(12, 20), // Popular, creative appeal
            'UKM Olahraga' => rand(8, 15), // Physical requirements limit applicants
            'UKM Teknologi' => rand(6, 12), // Specialized, smaller groups
            'UKM Kemasyarakatan' => rand(15, 25), // High social appeal
            'UKM Keagamaan' => rand(8, 16), // Steady but moderate interest
            'UKM Kewirausahaan' => rand(10, 18), // Growing interest
            default => rand(8, 15)
        };

        // Semester variations (genap usually has more new registrations)
        if ($semester === 'genap_2024') {
            $baseCount = intval($baseCount * 1.2); // 20% increase for new semester
        }

        return $baseCount;
    }

    private function determineStatusByCategory($category, $semester)
    {
        // Different categories have different acceptance patterns
        return match ($category) {
            'Himpunan' => $this->weightedRandomStatus(['accepted' => 60, 'pending' => 25, 'rejected' => 15]),
            'UKM Seni' => $this->weightedRandomStatus(['accepted' => 75, 'pending' => 20, 'rejected' => 5]),
            'UKM Olahraga' => $this->weightedRandomStatus(['accepted' => 55, 'pending' => 30, 'rejected' => 15]),
            'UKM Teknologi' => $this->weightedRandomStatus(['accepted' => 65, 'pending' => 25, 'rejected' => 10]),
            'UKM Kemasyarakatan' => $this->weightedRandomStatus(['accepted' => 85, 'pending' => 10, 'rejected' => 5]),
            'UKM Keagamaan' => $this->weightedRandomStatus(['accepted' => 80, 'pending' => 15, 'rejected' => 5]),
            default => $this->weightedRandomStatus(['accepted' => 70, 'pending' => 20, 'rejected' => 10])
        };
    }

    private function weightedRandomStatus($weights)
    {
        $total = array_sum($weights);
        $random = rand(1, $total);

        $current = 0;
        foreach ($weights as $status => $weight) {
            $current += $weight;
            if ($random <= $current) {
                return $status;
            }
        }

        return array_key_first($weights); // Fallback
    }

    private function getRegistrationDate($period)
    {
        return Carbon::now()->subDays(rand($period['end'], $period['start']));
    }

    private function getStatusUpdateDate($createdAt, $status)
    {
        if ($status === 'pending') {
            return $createdAt; // No update yet for pending
        }

        // Status updates typically happen 3-14 days after application
        return $createdAt->copy()->addDays(rand(3, 14));
    }

    private function hasExistingRegistration($userId, $unitId)
    {
        return PendaftaranAnggota::where('user_id', $userId)
            ->where('unit_kegiatan_id', $unitId)
            ->exists();
    }

    private function displayRegistrationStatistics($units)
    {
        $this->command->info('');
        $this->command->info('📊 Statistik Pendaftaran Keanggotaan UKM:');

        // Overall statistics
        $statuses = ['pending', 'accepted', 'rejected'];
        foreach ($statuses as $status) {
            $count = PendaftaranAnggota::where('status', $status)->count();
            $icon = match ($status) {
                'pending' => '⏳',
                'accepted' => '✅',
                'rejected' => '❌'
            };
            $label = match ($status) {
                'pending' => 'Menunggu Verifikasi',
                'accepted' => 'Diterima',
                'rejected' => 'Ditolak'
            };
            $this->command->info("$icon $label: $count pendaftaran");
        }

        $this->command->info('');
        $this->command->info('📈 Distribusi per Kategori UKM:');

        // Category-wise statistics
        $categories = $units->groupBy('category');
        foreach ($categories as $category => $categoryUnits) {
            $totalRegistrations = PendaftaranAnggota::whereIn('unit_kegiatan_id', $categoryUnits->pluck('id'))->count();
            $acceptedCount = PendaftaranAnggota::whereIn('unit_kegiatan_id', $categoryUnits->pluck('id'))
                ->where('status', 'accepted')->count();
            $acceptanceRate = $totalRegistrations > 0 ? round(($acceptedCount / $totalRegistrations) * 100, 1) : 0;

            $this->command->info("🎯 $category: $totalRegistrations pendaftaran ($acceptanceRate% diterima)");
        }

        $this->command->info('');
        $this->command->info('⚡ Top 5 UKM Paling Diminati:');

        // Most popular UKMs
        $popularUkms = PendaftaranAnggota::selectRaw('unit_kegiatan_id, count(*) as registration_count')
            ->groupBy('unit_kegiatan_id')
            ->orderByDesc('registration_count')
            ->limit(5)
            ->get();

        foreach ($popularUkms as $index => $registration) {
            $unit = $units->find($registration->unit_kegiatan_id);
            $rank = $index + 1;
            $this->command->info("$rank. {$unit->alias} - {$registration->registration_count} pendaftar");
        }
    }
}
