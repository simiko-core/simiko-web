<?php

namespace Database\Seeders;

use App\Models\PaymentConfiguration;
use App\Models\PaymentTransaction;
use App\Models\AnonymousEventRegistration;
use App\Models\UnitKegiatan;
use App\Models\User;
use App\Models\Feed;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $ukms = UnitKegiatan::all();
        $users = User::where('id', '>', 1)->get(); // Exclude super admin

        if ($ukms->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No UKMs or Users found. Please run required seeders first.');
            return;
        }

        $this->command->info('Creating comprehensive payment configurations and transactions...');

        foreach ($ukms as $ukm) {
            $this->command->info("Processing payments for {$ukm->name} ({$ukm->alias})...");

            // Create diverse payment configurations based on UKM category
            $this->createPaymentConfigurations($ukm);

            // Create realistic transaction patterns
            $this->createTransactionPatterns($ukm, $users);
        }

        // Create event-linked transactions for paid events
        $this->createEventTransactions($users);

        // Create batch payment scenarios
        $this->createBatchPaymentScenarios($ukms, $users);

        // Create expired and failed transaction scenarios
        $this->createVariousTransactionStates($ukms, $users);

        $this->command->info('Payment seeder completed successfully!');
        $this->command->info('Total Payment Configurations: ' . PaymentConfiguration::count());
        $this->command->info('Total Payment Transactions: ' . PaymentTransaction::count());
    }

    private function createPaymentConfigurations($ukm)
    {
        $configurations = $this->getConfigurationTemplates($ukm);

        foreach ($configurations as $config) {
            PaymentConfiguration::create([
                'unit_kegiatan_id' => $ukm->id,
                'name' => $config['name'],
                'description' => $config['description'],
                'amount' => $config['amount'],
                'currency' => 'IDR',
                'payment_methods' => $this->generatePaymentMethods($ukm, $config['type']),
                'custom_fields' => $this->generateCustomFields($config['type']),
                'settings' => $this->generateSettings($config),
            ]);
        }
    }

    private function createTransactionPatterns($ukm, $users)
    {
        $configurations = PaymentConfiguration::where('unit_kegiatan_id', $ukm->id)->get();

        foreach ($configurations as $config) {
            $transactionCount = $this->getTransactionCount($config->name);
            $selectedUsers = $users->random(min($transactionCount, $users->count()));

            foreach ($selectedUsers as $user) {
                $this->createTransaction($ukm, $user, $config);
            }
        }
    }

    private function createEventTransactions($users)
    {
        $paidEvents = Feed::where('type', 'event')
            ->where('is_paid', true)
            ->whereNotNull('payment_configuration_id')
            ->get();

        foreach ($paidEvents as $event) {
            $registrationCount = rand(3, 15); // Realistic event registration numbers
            $eventUsers = $users->random(min($registrationCount, $users->count()));

            foreach ($eventUsers as $user) {
                $this->createEventTransaction($event, $user);
            }
        }
    }

    private function createBatchPaymentScenarios($ukms, $users)
    {
        // Simulate bulk membership payments during registration periods
        $membershipConfigs = PaymentConfiguration::whereIn('unit_kegiatan_id', $ukms->pluck('id'))
            ->where('name', 'LIKE', '%membership%')
            ->orWhere('name', 'LIKE', '%keanggotaan%')
            ->get();

        foreach ($membershipConfigs as $config) {
            $batchSize = rand(10, 25); // Simulate bulk registrations
            $batchUsers = $users->random(min($batchSize, $users->count()));
            $batchDate = Carbon::now()->subDays(rand(30, 60)); // Historical batch

            foreach ($batchUsers as $user) {
                $this->createBatchTransaction($config, $user, $batchDate);
            }
        }
    }

    private function createVariousTransactionStates($ukms, $users)
    {
        // Create some expired transactions
        $this->createExpiredTransactions($ukms, $users, 15);

        // Create failed transactions
        $this->createFailedTransactions($ukms, $users, 10);

        // Create cancelled transactions
        $this->createCancelledTransactions($ukms, $users, 8);

        // Create recent pending transactions
        $this->createRecentPendingTransactions($ukms, $users, 12);
    }

    private function createTransaction($ukm, $user, $config, $specificDate = null, $status = null)
    {
        $statuses = ['pending', 'paid', 'failed'];
        $weights = [30, 60, 10]; // 60% paid, 30% pending, 10% failed
        $selectedStatus = $status ?? $this->weightedRandom($statuses, $weights);

        $createdAt = $specificDate ?? $this->getRandomTransactionDate();
        $paymentMethod = $this->selectPaymentMethod($config->payment_methods);

        // Create anonymous event registration for this transaction
        $eventFeed = $this->getOrCreateEventFeed($ukm);

        $anonymousRegistration = AnonymousEventRegistration::create([
            'feed_id' => $eventFeed->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '08' . rand(100000000, 999999999),
            'custom_data' => $this->generateCustomData($config->custom_fields, $user),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        $transaction = [
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $config->id,
            'anonymous_registration_id' => $anonymousRegistration->id,
            'feed_id' => $eventFeed->id,
            'transaction_id' => $this->generateTransactionId($ukm->alias),
            'amount' => $config->amount,
            'currency' => $config->currency,
            'status' => $selectedStatus,
            'payment_method' => $paymentMethod['method'],
            'payment_details' => $paymentMethod,
            'custom_data' => $this->generateCustomData($config->custom_fields, $user),
            'notes' => $this->generateTransactionNotes($selectedStatus, $paymentMethod['method']),
            'created_at' => $createdAt,
            'updated_at' => Carbon::parse($createdAt)->addHours(rand(1, 48)),
        ];

        // Set payment-specific fields based on status
        if ($selectedStatus === 'paid') {
            $transaction['paid_at'] = Carbon::parse($createdAt)->addHours(rand(1, 72));
        } elseif ($selectedStatus === 'pending') {
            $transaction['expires_at'] = Carbon::parse($createdAt)->addDays(rand(3, 14));
        }

        // Add custom files for some transactions
        if (rand(0, 4) === 0) { // 20% chance of having custom files
            $transaction['custom_files'] = $this->generateCustomFiles();
        }

        return PaymentTransaction::create($transaction);
    }

    private function createEventTransaction($event, $user)
    {
        $config = $event->paymentConfiguration;
        $statuses = ['pending', 'paid'];
        $weights = [25, 75]; // Events typically have higher payment rates

        $status = $this->weightedRandom($statuses, $weights);
        $createdAt = Carbon::parse($event->created_at)->addDays(rand(1, 7));

        $transaction = $this->createTransaction(
            $event->unitKegiatan,
            $user,
            $config,
            $createdAt,
            $status
        );

        $transaction->update([
            'feed_id' => $event->id,
            'expires_at' => Carbon::parse($event->event_date)->subDays(1),
            'notes' => "Event registration for: {$event->title}",
        ]);
    }

    private function createBatchTransaction($config, $user, $batchDate)
    {
        $createdAt = $batchDate->copy()->addHours(rand(0, 48));

        return $this->createTransaction(
            $config->unitKegiatan,
            $user,
            $config,
            $createdAt,
            'paid' // Batch registrations are typically completed
        );
    }

    private function createExpiredTransactions($ukms, $users, $count)
    {
        for ($i = 0; $i < $count; $i++) {
            $ukm = $ukms->random();
            $config = PaymentConfiguration::where('unit_kegiatan_id', $ukm->id)->first();
            if (!$config) continue;

            $user = $users->random();
            $expiredDate = Carbon::now()->subDays(rand(7, 30));

            // Create anonymous event registration
            $eventFeed = $this->getOrCreateEventFeed($ukm);

            $anonymousRegistration = AnonymousEventRegistration::create([
                'feed_id' => $eventFeed->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => '08' . rand(100000000, 999999999),
                'custom_data' => $this->generateCustomData($config->custom_fields, $user),
                'created_at' => $expiredDate->copy()->subDays(rand(7, 14)),
                'updated_at' => $expiredDate,
            ]);

            PaymentTransaction::create([
                'unit_kegiatan_id' => $ukm->id,
                'payment_configuration_id' => $config->id,
                'anonymous_registration_id' => $anonymousRegistration->id,
                'transaction_id' => $this->generateTransactionId($ukm->alias),
                'amount' => $config->amount,
                'currency' => 'IDR',
                'status' => 'expired',
                'payment_method' => null,
                'custom_data' => $this->generateCustomData($config->custom_fields, $user),
                'notes' => 'Transaction expired due to timeout',
                'expires_at' => $expiredDate,
                'created_at' => $expiredDate->copy()->subDays(rand(7, 14)),
                'updated_at' => $expiredDate,
            ]);
        }
    }

    private function createFailedTransactions($ukms, $users, $count)
    {
        $failureReasons = [
            'Insufficient funds',
            'Payment gateway error',
            'Invalid payment details',
            'Network timeout',
            'Bank server maintenance'
        ];

        for ($i = 0; $i < $count; $i++) {
            $ukm = $ukms->random();
            $config = PaymentConfiguration::where('unit_kegiatan_id', $ukm->id)->first();
            if (!$config) continue;

            $user = $users->random();
            $failedDate = Carbon::now()->subDays(rand(1, 30));

            // Create anonymous event registration
            $eventFeed = $this->getOrCreateEventFeed($ukm);

            $anonymousRegistration = AnonymousEventRegistration::create([
                'feed_id' => $eventFeed->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => '08' . rand(100000000, 999999999),
                'custom_data' => $this->generateCustomData($config->custom_fields, $user),
                'created_at' => $failedDate,
                'updated_at' => $failedDate->copy()->addMinutes(rand(5, 30)),
            ]);

            PaymentTransaction::create([
                'unit_kegiatan_id' => $ukm->id,
                'payment_configuration_id' => $config->id,
                'anonymous_registration_id' => $anonymousRegistration->id,
                'transaction_id' => $this->generateTransactionId($ukm->alias),
                'amount' => $config->amount,
                'currency' => 'IDR',
                'status' => 'failed',
                'payment_method' => $this->selectPaymentMethod($config->payment_methods)['method'],
                'custom_data' => $this->generateCustomData($config->custom_fields, $user),
                'notes' => 'Payment failed: ' . $failureReasons[array_rand($failureReasons)],
                'created_at' => $failedDate,
                'updated_at' => $failedDate->copy()->addMinutes(rand(5, 30)),
            ]);
        }
    }

    private function createCancelledTransactions($ukms, $users, $count)
    {
        $cancellationReasons = [
            'User cancelled registration',
            'Event cancelled by organizer',
            'Duplicate registration detected',
            'User request for cancellation'
        ];

        for ($i = 0; $i < $count; $i++) {
            $ukm = $ukms->random();
            $config = PaymentConfiguration::where('unit_kegiatan_id', $ukm->id)->first();
            if (!$config) continue;

            $user = $users->random();
            $cancelledDate = Carbon::now()->subDays(rand(1, 21));

            // Create anonymous event registration
            $eventFeed = $this->getOrCreateEventFeed($ukm);

            $anonymousRegistration = AnonymousEventRegistration::create([
                'feed_id' => $eventFeed->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => '08' . rand(100000000, 999999999),
                'custom_data' => $this->generateCustomData($config->custom_fields, $user),
                'created_at' => $cancelledDate,
                'updated_at' => $cancelledDate->copy()->addHours(rand(1, 24)),
            ]);

            PaymentTransaction::create([
                'unit_kegiatan_id' => $ukm->id,
                'payment_configuration_id' => $config->id,
                'anonymous_registration_id' => $anonymousRegistration->id,
                'transaction_id' => $this->generateTransactionId($ukm->alias),
                'amount' => $config->amount,
                'currency' => 'IDR',
                'status' => 'cancelled',
                'payment_method' => null,
                'custom_data' => $this->generateCustomData($config->custom_fields, $user),
                'notes' => $cancellationReasons[array_rand($cancellationReasons)],
                'created_at' => $cancelledDate,
                'updated_at' => $cancelledDate->copy()->addHours(rand(1, 24)),
            ]);
        }
    }

    private function createRecentPendingTransactions($ukms, $users, $count)
    {
        for ($i = 0; $i < $count; $i++) {
            $ukm = $ukms->random();
            $config = PaymentConfiguration::where('unit_kegiatan_id', $ukm->id)->first();
            if (!$config) continue;

            $user = $users->random();
            $recentDate = Carbon::now()->subHours(rand(1, 72));

            // Create anonymous event registration
            $eventFeed = $this->getOrCreateEventFeed($ukm);

            $anonymousRegistration = AnonymousEventRegistration::create([
                'feed_id' => $eventFeed->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => '08' . rand(100000000, 999999999),
                'custom_data' => $this->generateCustomData($config->custom_fields, $user),
                'created_at' => $recentDate,
                'updated_at' => $recentDate,
            ]);

            PaymentTransaction::create([
                'unit_kegiatan_id' => $ukm->id,
                'payment_configuration_id' => $config->id,
                'anonymous_registration_id' => $anonymousRegistration->id,
                'transaction_id' => $this->generateTransactionId($ukm->alias),
                'amount' => $config->amount,
                'currency' => 'IDR',
                'status' => 'pending',
                'payment_method' => null,
                'custom_data' => $this->generateCustomData($config->custom_fields, $user),
                'notes' => 'Waiting for payment confirmation',
                'expires_at' => Carbon::now()->addDays(rand(3, 7)),
                'created_at' => $recentDate,
                'updated_at' => $recentDate,
            ]);
        }
    }

    // Helper methods

    private function getConfigurationTemplates($ukm)
    {
        $baseConfigurations = [
            [
                'name' => 'Membership Fee 2024/2025',
                'description' => 'Biaya keanggotaan tahunan untuk mendapatkan akses penuh ke semua kegiatan',
                'amount' => 25000,
                'type' => 'membership'
            ],
            [
                'name' => 'Event Registration Fee',
                'description' => 'Biaya pendaftaran standar untuk mengikuti event dan workshop',
                'amount' => 50000,
                'type' => 'event'
            ],
            [
                'name' => 'Workshop Premium Package',
                'description' => 'Paket workshop intensif dengan sertifikat dan materi lengkap',
                'amount' => 100000,
                'type' => 'workshop'
            ]
        ];

        // Add category-specific configurations
        $categoryConfigs = $this->getCategorySpecificConfigs($ukm->category);

        return array_merge($baseConfigurations, $categoryConfigs);
    }

    private function getCategorySpecificConfigs($category)
    {
        $configs = [
            'Himpunan' => [
                [
                    'name' => 'Study Group Registration',
                    'description' => 'Biaya pendaftaran study group untuk persiapan ujian dan kompetisi',
                    'amount' => 75000,
                    'type' => 'study_group'
                ],
                [
                    'name' => 'Industry Visit Program',
                    'description' => 'Program kunjungan industri untuk melihat aplikasi teknologi nyata',
                    'amount' => 150000,
                    'type' => 'industry_visit'
                ]
            ],
            'UKM Seni' => [
                [
                    'name' => 'Art Exhibition Participation',
                    'description' => 'Biaya partisipasi dalam pameran seni dan showcase karya',
                    'amount' => 60000,
                    'type' => 'exhibition'
                ],
                [
                    'name' => 'Equipment Rental Fee',
                    'description' => 'Sewa peralatan profesional untuk project dan pertunjukan',
                    'amount' => 40000,
                    'type' => 'equipment'
                ]
            ],
            'UKM Olahraga' => [
                [
                    'name' => 'Tournament Registration',
                    'description' => 'Biaya pendaftaran turnamen dan kompetisi olahraga',
                    'amount' => 80000,
                    'type' => 'tournament'
                ],
                [
                    'name' => 'Training Camp Fee',
                    'description' => 'Biaya pelatihan intensif dengan pelatih profesional',
                    'amount' => 200000,
                    'type' => 'training_camp'
                ]
            ]
        ];

        return $configs[$category] ?? [];
    }

    private function generatePaymentMethods($ukm, $type)
    {
        $bankOptions = [
            'BCA' => ['1234567890', '2345678901', '3456789012'],
            'Mandiri' => ['9876543210', '8765432109', '7654321098'],
            'BNI' => ['5555666677', '6666777788', '7777888899'],
            'BRI' => ['4444555566', '5555666677', '6666777788']
        ];

        $digitalWallets = [
            'Dana',
            'GoPay',
            'OVO',
            'ShopeePay',
            'LinkAja'
        ];

        $methods = [];

        // Always include at least one bank transfer
        $selectedBank = array_rand($bankOptions);
        $methods[] = [
            'method' => "Bank Transfer {$selectedBank}",
            'account_number' => $bankOptions[$selectedBank][array_rand($bankOptions[$selectedBank])],
            'account_name' => "Bendahara {$ukm->alias}",
            'bank_name' => "Bank {$selectedBank}",
            'instructions' => "Transfer ke rekening {$selectedBank} dan kirim bukti pembayaran via WhatsApp"
        ];

        // Add 1-2 digital wallets
        $walletCount = rand(1, 2);
        $selectedWallets = array_rand(array_flip($digitalWallets), $walletCount);
        if (!is_array($selectedWallets)) $selectedWallets = [$selectedWallets];

        foreach ($selectedWallets as $wallet) {
            $methods[] = [
                'method' => $wallet,
                'account_number' => '08' . rand(100000000, 999999999),
                'account_name' => "Kas {$ukm->alias}",
                'instructions' => "Kirim pembayaran via {$wallet} dan sertakan nama lengkap dalam keterangan"
            ];
        }

        // Add cash option for smaller amounts
        if ($type === 'membership' || rand(0, 2) === 0) {
            $methods[] = [
                'method' => 'Cash',
                'account_number' => null,
                'account_name' => "Sekretariat {$ukm->alias}",
                'instructions' => "Pembayaran tunai di sekretariat pada jam operasional (09:00-16:00)"
            ];
        }

        return $methods;
    }

    private function generateCustomFields($type)
    {
        $baseFields = [
            [
                'label' => 'Student ID',
                'name' => 'student_id',
                'type' => 'text',
                'placeholder' => 'Masukkan NIM',
                'required' => true
            ],
            [
                'label' => 'Full Name',
                'name' => 'full_name',
                'type' => 'text',
                'placeholder' => 'Nama lengkap sesuai KTM',
                'required' => true
            ],
            [
                'label' => 'WhatsApp Number',
                'name' => 'whatsapp',
                'type' => 'tel',
                'placeholder' => '08123456789',
                'required' => true
            ]
        ];

        $typeSpecificFields = [
            'workshop' => [
                [
                    'label' => 'Experience Level',
                    'name' => 'experience_level',
                    'type' => 'select',
                    'options' => 'Beginner, Intermediate, Advanced',
                    'required' => true
                ],
                [
                    'label' => 'Laptop Availability',
                    'name' => 'has_laptop',
                    'type' => 'radio',
                    'options' => 'Yes, No',
                    'required' => true
                ]
            ],
            'tournament' => [
                [
                    'label' => 'Team Name',
                    'name' => 'team_name',
                    'type' => 'text',
                    'placeholder' => 'Nama tim (jika ada)',
                    'required' => false
                ],
                [
                    'label' => 'Emergency Contact',
                    'name' => 'emergency_contact',
                    'type' => 'tel',
                    'placeholder' => 'Nomor kontak darurat',
                    'required' => true
                ]
            ],
            'membership' => [
                [
                    'label' => 'Faculty',
                    'name' => 'faculty',
                    'type' => 'select',
                    'options' => 'Teknik, Ekonomi, Hukum, Kedokteran, Pertanian, FKIP',
                    'required' => true
                ],
                [
                    'label' => 'Year of Study',
                    'name' => 'year_of_study',
                    'type' => 'select',
                    'options' => '1st Year, 2nd Year, 3rd Year, 4th Year, 5th Year',
                    'required' => true
                ]
            ]
        ];

        return array_merge($baseFields, $typeSpecificFields[$type] ?? []);
    }

    private function generateSettings($config)
    {
        $daysToAdd = match ($config['type']) {
            'membership' => rand(30, 60),
            'event' => rand(7, 21),
            'workshop' => rand(14, 30),
            'tournament' => rand(21, 45),
            default => rand(7, 30)
        };

        $maxParticipants = match ($config['type']) {
            'membership' => rand(200, 500),
            'workshop' => rand(20, 50),
            'tournament' => rand(16, 32),
            'study_group' => rand(15, 25),
            default => rand(30, 100)
        };

        return [
            'due_date' => Carbon::now()->addDays($daysToAdd)->format('Y-m-d'),
            'max_participants' => $maxParticipants,
            'terms_conditions' => "Pembayaran tidak dapat dikembalikan. Peserta yang tidak hadir tanpa pemberitahuan tidak berhak atas refund.",
            'auto_confirm' => $config['type'] === 'membership',
            'send_reminder' => true,
            'reminder_days' => [7, 3, 1], // Send reminders 7, 3, and 1 day before due date
            'notes' => "Payment configuration for {$config['type']} - {$config['name']}"
        ];
    }

    private function getTransactionCount($configName)
    {
        // Different configurations have different popularity
        if (str_contains(strtolower($configName), 'membership')) {
            return rand(15, 35); // Membership is popular
        } elseif (str_contains(strtolower($configName), 'workshop')) {
            return rand(8, 20);
        } elseif (str_contains(strtolower($configName), 'event')) {
            return rand(10, 25);
        }

        return rand(5, 15);
    }

    private function weightedRandom($values, $weights)
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);

        $currentWeight = 0;
        foreach ($values as $i => $value) {
            $currentWeight += $weights[$i];
            if ($random <= $currentWeight) {
                return $value;
            }
        }

        return $values[0]; // Fallback
    }

    private function selectPaymentMethod($methods)
    {
        return $methods[array_rand($methods)];
    }

    private function generateTransactionId($ukmAlias)
    {
        // Use microtime for better uniqueness
        $timestamp = str_replace('.', '', microtime(true));
        $randomSuffix = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);

        return 'TXN-' . $ukmAlias . '-' . $timestamp . '-' . $randomSuffix;
    }

    private function generateCustomData($customFields, $user)
    {
        $data = [];

        foreach ($customFields as $field) {
            $data[$field['name']] = match ($field['name']) {
                'student_id' => '2024' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'full_name' => $user->name,
                'whatsapp' => '08' . rand(100000000, 999999999),
                'faculty' => ['Teknik', 'Ekonomi', 'Hukum', 'Kedokteran', 'Pertanian'][array_rand(['Teknik', 'Ekonomi', 'Hukum', 'Kedokteran', 'Pertanian'])],
                'year_of_study' => ['1st Year', '2nd Year', '3rd Year', '4th Year'][array_rand(['1st Year', '2nd Year', '3rd Year', '4th Year'])],
                'experience_level' => ['Beginner', 'Intermediate', 'Advanced'][array_rand(['Beginner', 'Intermediate', 'Advanced'])],
                'has_laptop' => ['Yes', 'No'][array_rand(['Yes', 'No'])],
                'team_name' => 'Team ' . ucfirst(fake()->word()),
                'emergency_contact' => '08' . rand(100000000, 999999999),
                default => fake()->word()
            };
        }

        return $data;
    }

    private function generateTransactionNotes($status, $paymentMethod)
    {
        return match ($status) {
            'paid' => "Payment confirmed via {$paymentMethod}. Thank you!",
            'pending' => "Waiting for payment confirmation via {$paymentMethod}",
            'failed' => "Payment failed via {$paymentMethod}. Please try again.",
            'cancelled' => "Transaction cancelled by user request",
            'expired' => "Transaction expired due to timeout",
            default => "Transaction {$status}"
        };
    }

    private function generateCustomFiles()
    {
        $files = [
            'student_id_card' => 'custom_files/student_cards/ktm_' . rand(1000, 9999) . '.jpg',
            'payment_proof' => 'custom_files/payment_proofs/proof_' . rand(1000, 9999) . '.jpg',
        ];

        // Randomly include some files
        $selectedFiles = [];
        foreach ($files as $key => $path) {
            if (rand(0, 1)) {
                $selectedFiles[$key] = $path;
            }
        }

        return empty($selectedFiles) ? null : $selectedFiles;
    }

    private function getRandomTransactionDate()
    {
        return Carbon::now()->subDays(rand(1, 90));
    }

    private function getOrCreateEventFeed($ukm)
    {
        // Get a random event feed from this UKM for the registration
        $eventFeed = Feed::where('unit_kegiatan_id', $ukm->id)
            ->where('type', 'event')
            ->inRandomOrder()
            ->first();

        // If no event found, create a generic event feed for this UKM
        if (!$eventFeed) {
            $eventFeed = Feed::create([
                'unit_kegiatan_id' => $ukm->id,
                'type' => 'event',
                'title' => 'General Registration Event',
                'content' => 'General event for payment registrations',
                'event_date' => Carbon::now()->addDays(30),
                'event_type' => 'offline',
                'location' => 'Campus',
                'is_paid' => true,
                'max_participants' => 50,
            ]);
        }

        return $eventFeed;
    }

    private function getRandomFaculty()
    {
        $faculties = ['Teknik', 'Ekonomi', 'Hukum', 'Kedokteran', 'Pertanian', 'FKIP'];
        return $faculties[array_rand($faculties)];
    }
}
