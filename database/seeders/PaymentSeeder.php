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
        $configurations = $this->getPaymentConfigurations($ukm);

        foreach ($configurations as $config) {
            PaymentConfiguration::create([
                'unit_kegiatan_id' => $ukm->id,
                'name' => $config['name'],
                'amount' => $config['amount'],
                'currency' => 'IDR',
                'payment_methods' => $this->generatePaymentMethods($ukm, $config['type']),
                'custom_fields' => $this->generateCustomFields($config['type']),
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

    private function getPaymentConfigurations($ukm)
    {
        $configs = [
            'himpunan' => [
                [
                    'name' => 'Iuran Keanggotaan Semester Genap 2024',
                    'description' => 'Iuran wajib anggota semester genap untuk operasional organisasi, kegiatan rutin, dan pengembangan himpunan.',
                    'amount' => rand(75000, 150000),
                    'type' => 'membership'
                ],
                [
                    'name' => 'Workshop Web Development Fundamental',
                    'description' => 'Pelatihan pemrograman web untuk mahasiswa tingkat pemula hingga menengah. Termasuk materi HTML, CSS, JavaScript, dan framework modern.',
                    'amount' => rand(50000, 100000),
                    'type' => 'workshop'
                ],
                [
                    'name' => 'Seminar Nasional Tech Innovation 2024',
                    'description' => 'Seminar nasional teknologi terkini dengan pembicara dari industri teknologi Indonesia (Gojek, Tokopedia, Traveloka).',
                    'amount' => rand(75000, 125000),
                    'type' => 'seminar'
                ],
                [
                    'name' => 'Hackathon Smart City Jakarta 2024',
                    'description' => 'Kompetisi pengembangan aplikasi untuk solusi smart city Jakarta. Hadiah jutaan rupiah dan internship opportunity.',
                    'amount' => rand(100000, 200000),
                    'type' => 'competition'
                ]
            ],
            'ukm_seni' => [
                [
                    'name' => 'Iuran Anggota Semester Genap 2024',
                    'description' => 'Kontribusi anggota untuk kegiatan seni, latihan rutin, perlengkapan, dan pementasan semester genap.',
                    'amount' => rand(50000, 100000),
                    'type' => 'membership'
                ],
                [
                    'name' => 'Workshop Fotografi Event & Portrait',
                    'description' => 'Pelatihan teknik fotografi event dan portrait untuk dokumentasi kegiatan kampus. Include praktek lapangan.',
                    'amount' => rand(75000, 125000),
                    'type' => 'workshop'
                ],
                [
                    'name' => 'Pertunjukan Seni Budaya Nusantara 2024',
                    'description' => 'Pementasan kolaboratif menampilkan keberagaman seni tradisional Indonesia dengan sentuhan modern.',
                    'amount' => rand(35000, 75000),
                    'type' => 'event'
                ],
                [
                    'name' => 'Festival Film Pendek Mahasiswa 2024',
                    'description' => 'Kompetisi film pendek tingkat universitas dengan tema "Indonesia Muda Berkarya". Hadiah total 10 juta rupiah.',
                    'amount' => rand(50000, 100000),
                    'type' => 'competition'
                ]
            ],
            'ukm_olahraga' => [
                [
                    'name' => 'Iuran Atlet Semester Genap 2024',
                    'description' => 'Iuran untuk biaya latihan, peralatan olahraga, dan persiapan turnamen resmi tingkat regional dan nasional.',
                    'amount' => rand(100000, 200000),
                    'type' => 'membership'
                ],
                [
                    'name' => 'Training Camp Persiapan LIMA 2024',
                    'description' => 'Pemusatan latihan intensif untuk persiapan Liga Mahasiswa (LIMA) dengan pelatih profesional.',
                    'amount' => rand(200000, 350000),
                    'type' => 'training_camp'
                ],
                [
                    'name' => 'Turnamen Internal Badminton Cup 2024',
                    'description' => 'Kompetisi badminton tingkat universitas untuk seleksi atlet terbaik mewakili kampus di turnamen eksternal.',
                    'amount' => rand(75000, 125000),
                    'type' => 'tournament'
                ],
                [
                    'name' => 'Seminar Nutrisi & Mental Atlet',
                    'description' => 'Edukasi nutrisi olahraga dan kesehatan mental atlet dengan ahli gizi dan psikolog olahraga.',
                    'amount' => rand(50000, 75000),
                    'type' => 'seminar'
                ]
            ],
            'ukm_kemasyarakatan' => [
                [
                    'name' => 'Iuran Anggota & Dana Kegiatan Sosial',
                    'description' => 'Kontribusi anggota untuk kegiatan sosial kemasyarakatan, program 3T, dan bantuan bencana alam.',
                    'amount' => rand(50000, 100000),
                    'type' => 'membership'
                ],
                [
                    'name' => 'Program Pemberdayaan UMKM Digital',
                    'description' => 'Pelatihan digital marketing untuk UMKM di sekitar kampus bekerjasama dengan Kemenkominfo.',
                    'amount' => rand(25000, 50000),
                    'type' => 'workshop'
                ],
                [
                    'name' => 'Ekspedisi Konservasi Mangrove Pantai Utara',
                    'description' => 'Program konservasi lingkungan dan edukasi masyarakat pesisir tentang pentingnya ekosistem mangrove.',
                    'amount' => rand(150000, 250000),
                    'type' => 'expedition'
                ],
                [
                    'name' => 'Bakti Sosial Ramadan 1445H',
                    'description' => 'Program berbagi dengan masyarakat kurang mampu selama bulan Ramadan berupa santunan dan buka puasa bersama.',
                    'amount' => rand(75000, 125000),
                    'type' => 'event'
                ]
            ],
            'ukm_teknologi' => [
                [
                    'name' => 'Iuran Pengembangan Lab & Equipment',
                    'description' => 'Kontribusi untuk upgrade peralatan laboratorium robotika dan pembelian komponen elektronik.',
                    'amount' => rand(125000, 200000),
                    'type' => 'membership'
                ],
                [
                    'name' => 'Workshop IoT untuk Smart Farming',
                    'description' => 'Pelatihan pengembangan sistem IoT untuk modernisasi pertanian Indonesia dengan sensor dan cloud computing.',
                    'amount' => rand(100000, 175000),
                    'type' => 'workshop'
                ],
                [
                    'name' => 'Kontes Robot Indonesia Regional Jawa',
                    'description' => 'Kompetisi robotika tingkat regional untuk kualifikasi ke Kontes Robot Indonesia tingkat nasional.',
                    'amount' => rand(200000, 300000),
                    'type' => 'competition'
                ],
                [
                    'name' => 'Tech Talk: AI in Indonesian Industries',
                    'description' => 'Diskusi penerapan Artificial Intelligence di industri Indonesia dengan praktisi dari startup unicorn.',
                    'amount' => rand(50000, 100000),
                    'type' => 'seminar'
                ]
            ],
            'ukm_keagamaan' => [
                [
                    'name' => 'Iuran Kajian & Kegiatan Rohani',
                    'description' => 'Kontribusi untuk kegiatan kajian rutin, buka puasa bersama, dan program dakwah kampus.',
                    'amount' => rand(50000, 75000),
                    'type' => 'membership'
                ],
                [
                    'name' => 'Pesantren Kilat Ramadan 1445H',
                    'description' => 'Program intensif kajian Al-Quran dan Hadist selama bulan Ramadan dengan ustadz berpengalaman.',
                    'amount' => rand(75000, 125000),
                    'type' => 'event'
                ],
                [
                    'name' => 'Study Tour Jejak Sejarah Islam Nusantara',
                    'description' => 'Wisata edukasi ke situs-situs bersejarah Islam di Jawa untuk memperdalam pemahaman sejarah.',
                    'amount' => rand(200000, 350000),
                    'type' => 'study_tour'
                ],
                [
                    'name' => 'Seminar Ekonomi Islam Modern',
                    'description' => 'Diskusi perkembangan ekonomi syariah di Indonesia dan peluang karir di perbankan syariah.',
                    'amount' => rand(50000, 75000),
                    'type' => 'seminar'
                ]
            ]
        ];

        // Determine UKM category
        $category = $this->determineUkmCategory($ukm);

        // Get configurations for this category, fallback to himpunan if category not found
        $categoryConfigs = $configs[$category] ?? $configs['himpunan'];

        // Return 2-4 configurations for variety
        return array_slice($categoryConfigs, 0, rand(2, 4));
    }

    private function determineUkmCategory($ukm)
    {
        $name = strtolower($ukm->name);
        $alias = strtolower($ukm->alias ?? '');

        // Himpunan categories
        if (str_contains($name, 'himpunan') || str_contains($alias, 'hm')) {
            return 'himpunan';
        }

        // Seni categories
        if (
            str_contains($name, 'tari') || str_contains($name, 'musik') || str_contains($name, 'seni') ||
            str_contains($name, 'photo') || str_contains($name, 'sinema') || str_contains($name, 'teater') ||
            str_contains($alias, 'psm') || str_contains($alias, 'foto')
        ) {
            return 'ukm_seni';
        }

        // Olahraga categories  
        if (
            str_contains($name, 'football') || str_contains($name, 'badminton') || str_contains($name, 'basket') ||
            str_contains($name, 'futsal') || str_contains($name, 'sport') || str_contains($name, 'athletic')
        ) {
            return 'ukm_olahraga';
        }

        // Kemasyarakatan categories
        if (
            str_contains($name, 'pecinta alam') || str_contains($name, 'pmr') || str_contains($name, 'pramuka') ||
            str_contains($alias, 'pa') || str_contains($alias, 'pmr')
        ) {
            return 'ukm_kemasyarakatan';
        }

        // Teknologi categories
        if (str_contains($name, 'robot') || str_contains($name, 'teknologi') || str_contains($name, 'riset')) {
            return 'ukm_teknologi';
        }

        // Keagamaan categories
        if (
            str_contains($name, 'kerohanian') || str_contains($name, 'islam') || str_contains($name, 'kristen') ||
            str_contains($alias, 'kki') || str_contains($alias, 'kkk')
        ) {
            return 'ukm_keagamaan';
        }

        // Default to himpunan
        return 'himpunan';
    }

    private function generatePaymentMethods($ukm, $type)
    {
        $bankOptions = [
            'BCA' => [
                'accounts' => ['1234567890', '2345678901', '3456789012', '4567890123'],
                'name' => 'Bank Central Asia'
            ],
            'Mandiri' => [
                'accounts' => ['1300012345678', '1300023456789', '1300034567890'],
                'name' => 'Bank Mandiri'
            ],
            'BNI' => [
                'accounts' => ['0123456789', '0234567890', '0345678901'],
                'name' => 'Bank Negara Indonesia'
            ],
            'BRI' => [
                'accounts' => ['001201234567890', '001202345678901', '001203456789012'],
                'name' => 'Bank Rakyat Indonesia'
            ],
            'BSI' => [
                'accounts' => ['7123456789', '7234567890', '7345678901'],
                'name' => 'Bank Syariah Indonesia'
            ],
            'CIMB Niaga' => [
                'accounts' => ['800123456789', '800234567890', '800345678901'],
                'name' => 'CIMB Niaga'
            ]
        ];

        $digitalWallets = [
            'GoPay' => [
                'numbers' => ['081234567890', '082345678901', '083456789012'],
                'name' => 'GoPay by Gojek'
            ],
            'Dana' => [
                'numbers' => ['085678901234', '086789012345', '087890123456'],
                'name' => 'DANA Digital Wallet'
            ],
            'OVO' => [
                'numbers' => ['087890123456', '088901234567', '089012345678'],
                'name' => 'OVO by Grab'
            ],
            'ShopeePay' => [
                'numbers' => ['081987654321', '082876543210', '083765432109'],
                'name' => 'ShopeePay'
            ],
            'LinkAja' => [
                'numbers' => ['081122334455', '082233445566', '083344556677'],
                'name' => 'LinkAja by Telkomsel'
            ]
        ];

        // Select primary bank account
        $selectedBank = array_rand($bankOptions);
        $bankData = $bankOptions[$selectedBank];

        // Select digital wallet
        $selectedWallet = array_rand($digitalWallets);
        $walletData = $digitalWallets[$selectedWallet];

        $paymentMethods = [
            [
                'method' => "Transfer Bank {$selectedBank}",
                'account_number' => $bankData['accounts'][array_rand($bankData['accounts'])],
                'account_name' => "Bendahara {$ukm->alias} 2024",
                'bank_name' => $bankData['name'],
                'bank_code' => $this->getBankCode($selectedBank),
                'instructions' => "Transfer ke rekening {$bankData['name']} dan kirim bukti pembayaran via WhatsApp ke contact person. Pastikan nominal transfer tepat dan cantumkan nama lengkap + NIM pada keterangan.",
                'processing_time' => '1-24 jam (hari kerja)',
                'fees' => 'Biaya admin sesuai ketentuan bank'
            ],
            [
                'method' => $selectedWallet,
                'account_number' => $walletData['numbers'][array_rand($walletData['numbers'])],
                'account_name' => "Kas {$ukm->alias}",
                'wallet_name' => $walletData['name'],
                'instructions' => "Kirim pembayaran via {$walletData['name']} dan sertakan nama lengkap + NIM dalam catatan transfer. Screenshot bukti pembayaran untuk konfirmasi.",
                'processing_time' => 'Instan - 1 jam',
                'fees' => 'Gratis (sesuai promo masing-masing wallet)'
            ]
        ];

        return $paymentMethods;
    }

    private function getBankCode($bankName)
    {
        $bankCodes = [
            'BCA' => '014',
            'Mandiri' => '008',
            'BNI' => '009',
            'BRI' => '002',
            'BSI' => '451',
            'CIMB Niaga' => '022'
        ];

        return $bankCodes[$bankName] ?? '000';
    }

    private function generateCustomFields($type)
    {
        $baseFields = [
            [
                'label' => 'Nomor Induk Mahasiswa (NIM)',
                'name' => 'nim',
                'type' => 'text',
                'placeholder' => 'Contoh: 2024123456',
                'required' => true,
                'validation' => 'numeric|digits:10'
            ],
            [
                'label' => 'Nama Lengkap',
                'name' => 'full_name',
                'type' => 'text',
                'placeholder' => 'Sesuai KTM/Kartu Mahasiswa',
                'required' => true,
                'validation' => 'string|max:100'
            ],
            [
                'label' => 'Fakultas',
                'name' => 'faculty',
                'type' => 'select',
                'options' => 'Teknik, Ekonomi dan Bisnis, Hukum, Kedokteran, Pertanian, MIPA, Ilmu Sosial dan Politik, Psikologi, Farmasi',
                'required' => true
            ],
            [
                'label' => 'Program Studi',
                'name' => 'study_program',
                'type' => 'text',
                'placeholder' => 'Contoh: Teknik Informatika',
                'required' => true
            ],
            [
                'label' => 'Semester',
                'name' => 'semester',
                'type' => 'select',
                'options' => '1, 2, 3, 4, 5, 6, 7, 8, 9, 10',
                'required' => true
            ],
            [
                'label' => 'Nomor WhatsApp',
                'name' => 'whatsapp',
                'type' => 'tel',
                'placeholder' => '08123456789',
                'required' => true,
                'validation' => 'regex:/^08[0-9]{8,11}$/'
            ],
            [
                'label' => 'Email Mahasiswa',
                'name' => 'email',
                'type' => 'email',
                'placeholder' => 'nama@student.ac.id atau email aktif',
                'required' => true,
                'validation' => 'email'
            ]
        ];

        $typeSpecificFields = [
            'membership' => [
                [
                    'label' => 'Motivasi Bergabung',
                    'name' => 'motivation',
                    'type' => 'textarea',
                    'placeholder' => 'Jelaskan motivasi dan harapan bergabung dengan organisasi ini...',
                    'required' => true,
                    'validation' => 'string|min:50|max:500'
                ],
                [
                    'label' => 'Pengalaman Organisasi',
                    'name' => 'organization_experience',
                    'type' => 'textarea',
                    'placeholder' => 'Ceritakan pengalaman organisasi sebelumnya (opsional)',
                    'required' => false
                ]
            ],
            'workshop' => [
                [
                    'label' => 'Level Pengalaman',
                    'name' => 'experience_level',
                    'type' => 'select',
                    'options' => 'Pemula (Belum pernah), Menengah (Pernah belajar basic), Mahir (Sudah berpengalaman)',
                    'required' => true
                ],
                [
                    'label' => 'Ekspektasi dari Workshop',
                    'name' => 'workshop_expectation',
                    'type' => 'textarea',
                    'placeholder' => 'Apa yang ingin dipelajari dari workshop ini?',
                    'required' => true,
                    'validation' => 'string|max:300'
                ]
            ],
            'competition' => [
                [
                    'label' => 'Nama Tim',
                    'name' => 'team_name',
                    'type' => 'text',
                    'placeholder' => 'Nama tim (jika kompetisi beregu)',
                    'required' => false
                ],
                [
                    'label' => 'Anggota Tim',
                    'name' => 'team_members',
                    'type' => 'textarea',
                    'placeholder' => 'Daftar anggota tim (Nama - NIM), pisahkan dengan enter',
                    'required' => false,
                    'validation' => 'string|max:1000'
                ],
                [
                    'label' => 'Pengalaman Kompetisi',
                    'name' => 'competition_experience',
                    'type' => 'textarea',
                    'placeholder' => 'Pengalaman kompetisi sebelumnya (opsional)',
                    'required' => false
                ]
            ],
            'event' => [
                [
                    'label' => 'Kebutuhan Khusus',
                    'name' => 'special_needs',
                    'type' => 'text',
                    'placeholder' => 'Alergi makanan, kebutuhan akses, dll (opsional)',
                    'required' => false
                ],
                [
                    'label' => 'Sumber Informasi',
                    'name' => 'information_source',
                    'type' => 'select',
                    'options' => 'Instagram, WhatsApp, Website, Teman, Poster, Lainnya',
                    'required' => false
                ]
            ],
            'seminar' => [
                [
                    'label' => 'Alasan Mengikuti Seminar',
                    'name' => 'seminar_reason',
                    'type' => 'textarea',
                    'placeholder' => 'Mengapa tertarik mengikuti seminar ini?',
                    'required' => true,
                    'validation' => 'string|max:300'
                ],
                [
                    'label' => 'Bidang Minat',
                    'name' => 'interest_field',
                    'type' => 'text',
                    'placeholder' => 'Bidang/topik yang diminati',
                    'required' => false
                ]
            ]
        ];

        return array_merge($baseFields, $typeSpecificFields[$type] ?? []);
    }



    private function getMaxParticipantsByType($type)
    {
        $limits = [
            'membership' => rand(20, 50),
            'workshop' => rand(15, 35),
            'competition' => rand(10, 25),
            'seminar' => rand(50, 150),
            'event' => rand(25, 75),
            'study_group' => rand(8, 20),
            'industry_visit' => rand(20, 40),
            'exhibition' => rand(30, 60),
            'equipment' => rand(5, 15),
            'tournament' => rand(16, 32),
            'training_camp' => rand(12, 24)
        ];

        return $limits[$type] ?? rand(20, 50);
    }



    private function getTransactionCount($configName)
    {
        // Indonesian university realistic transaction patterns
        if (str_contains(strtolower($configName), 'membership') || str_contains(strtolower($configName), 'keanggotaan')) {
            return rand(15, 40); // Membership fees
        } elseif (str_contains(strtolower($configName), 'workshop') || str_contains(strtolower($configName), 'pelatihan')) {
            return rand(8, 25); // Workshops
        } elseif (str_contains(strtolower($configName), 'seminar')) {
            return rand(20, 60); // Seminars
        } elseif (str_contains(strtolower($configName), 'competition') || str_contains(strtolower($configName), 'kompetisi')) {
            return rand(5, 20); // Competitions
        } elseif (str_contains(strtolower($configName), 'event')) {
            return rand(10, 35); // General events
        }

        return rand(8, 30); // Default range
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
                'nim' => '2024' . str_pad(rand(100001, 999999), 6, '0', STR_PAD_LEFT),
                'student_id' => '2024' . str_pad(rand(100001, 999999), 6, '0', STR_PAD_LEFT),
                'full_name' => $user->name,
                'whatsapp' => '08' . rand(100000000, 999999999),
                'email' => $user->email,
                'faculty' => $this->getRandomIndonesianFaculty(),
                'study_program' => $this->getRandomStudyProgram(),
                'semester' => rand(1, 8),
                'year_of_study' => rand(1, 4) . ' Tahun',
                'experience_level' => ['Pemula (Belum pernah)', 'Menengah (Pernah belajar basic)', 'Mahir (Sudah berpengalaman)'][array_rand(['Pemula (Belum pernah)', 'Menengah (Pernah belajar basic)', 'Mahir (Sudah berpengalaman)'])],
                'workshop_expectation' => $this->getRandomWorkshopExpectation(),
                'motivation' => $this->getRandomMotivation(),
                'organization_experience' => $this->getRandomOrgExperience(),
                'has_laptop' => ['Ya, punya laptop pribadi', 'Belum punya, akan meminjam'][array_rand(['Ya, punya laptop pribadi', 'Belum punya, akan meminjam'])],
                'team_name' => 'Tim ' . ucfirst($this->getRandomIndonesianTeamName()),
                'team_members' => $this->generateTeamMembers(),
                'competition_experience' => $this->getRandomCompetitionExperience(),
                'special_needs' => rand(0, 3) === 0 ? $this->getRandomSpecialNeeds() : null,
                'information_source' => ['Instagram', 'WhatsApp', 'Website', 'Teman', 'Poster', 'Lainnya'][array_rand(['Instagram', 'WhatsApp', 'Website', 'Teman', 'Poster', 'Lainnya'])],
                'seminar_reason' => $this->getRandomSeminarReason(),
                'interest_field' => $this->getRandomInterestField(),
                'emergency_contact' => '08' . rand(100000000, 999999999),
                default => fake()->word()
            };
        }

        return $data;
    }

    private function generateTransactionNotes($status, $paymentMethod)
    {
        return match ($status) {
            'paid' => "Pembayaran berhasil dikonfirmasi via {$paymentMethod}. Terima kasih!",
            'pending' => "Menunggu konfirmasi pembayaran via {$paymentMethod}. Silakan kirim bukti pembayaran ke WhatsApp contact person.",
            'failed' => "Pembayaran gagal via {$paymentMethod}. Silakan coba lagi atau hubungi contact person.",
            'cancelled' => "Transaksi dibatalkan atas permintaan peserta",
            'expired' => "Transaksi kadaluarsa karena melebihi batas waktu pembayaran",
            default => "Status transaksi: {$status}"
        };
    }

    private function getRandomIndonesianFaculty()
    {
        $faculties = [
            'Fakultas Teknik',
            'Fakultas Ekonomi dan Bisnis',
            'Fakultas Hukum',
            'Fakultas Kedokteran',
            'Fakultas Pertanian',
            'Fakultas MIPA (Matematika dan Ilmu Pengetahuan Alam)',
            'Fakultas Ilmu Sosial dan Politik',
            'Fakultas Psikologi',
            'Fakultas Farmasi',
            'Fakultas Keguruan dan Ilmu Pendidikan'
        ];
        return $faculties[array_rand($faculties)];
    }

    private function getRandomStudyProgram()
    {
        $programs = [
            'Teknik Informatika',
            'Sistem Informasi',
            'Teknik Elektro',
            'Teknik Mesin',
            'Teknik Sipil',
            'Manajemen',
            'Akuntansi',
            'Ekonomi Pembangunan',
            'Ilmu Ekonomi',
            'Hukum',
            'Ilmu Hukum',
            'Kedokteran',
            'Kedokteran Gigi',
            'Keperawatan',
            'Agroteknologi',
            'Agribisnis',
            'Peternakan',
            'Matematika',
            'Fisika',
            'Kimia',
            'Biologi',
            'Statistika',
            'Ilmu Komunikasi',
            'Hubungan Internasional',
            'Administrasi Publik',
            'Psikologi',
            'Farmasi',
            'Pendidikan Bahasa Indonesia',
            'Pendidikan Matematika',
            'PGSD'
        ];
        return $programs[array_rand($programs)];
    }

    private function getRandomWorkshopExpectation()
    {
        $expectations = [
            'Ingin belajar skill baru yang relevan dengan industri terkini',
            'Menambah pengetahuan praktis untuk mendukung perkuliahan',
            'Mempersiapkan diri untuk dunia kerja dan karir masa depan',
            'Mengembangkan kemampuan teknis yang lebih mendalam',
            'Belajar dari praktisi profesional yang berpengalaman',
            'Mendapatkan sertifikat yang dapat menunjang CV',
            'Networking dengan sesama mahasiswa dan professionals',
            'Menerapkan teori yang sudah dipelajari di kampus'
        ];
        return $expectations[array_rand($expectations)];
    }

    private function getRandomMotivation()
    {
        $motivations = [
            'Ingin mengembangkan potensi diri dan berkontribusi untuk kemajuan organisasi serta universitas',
            'Tertarik untuk belajar berorganisasi dan mengasah kemampuan leadership serta teamwork',
            'Berharap dapat menambah pengalaman, relasi, dan soft skills melalui kegiatan-kegiatan positif',
            'Ingin mengaplikasikan ilmu yang dipelajari di perkuliahan melalui kegiatan praktis organisasi',
            'Termotivasi untuk turut serta memajukan bidang keilmuan sesuai program studi yang saya tekuni',
            'Berkomitmen untuk aktif dalam kegiatan sosial kemasyarakatan dan pengabdian kepada masyarakat'
        ];
        return $motivations[array_rand($motivations)];
    }

    private function getRandomOrgExperience()
    {
        $experiences = [
            'Pernah menjadi pengurus OSIS di SMA dengan fokus bidang akademik dan lomba',
            'Aktif di organisasi keagamaan dan kegiatan sosial di lingkungan masyarakat',
            'Pengalaman sebagai ketua kelas dan koordinator kegiatan kampus tingkat jurusan',
            'Belum memiliki pengalaman organisasi formal, namun sering terlibat dalam kepanitiaan event',
            'Aktif di UKM olahraga dan pernah menjadi captain tim dalam berbagai turnamen',
            null // Some people might not have experience
        ];
        return $experiences[array_rand($experiences)];
    }

    private function generateTeamMembers()
    {
        $memberCount = rand(2, 5);
        $members = [];

        for ($i = 0; $i < $memberCount; $i++) {
            $names = [
                'Ahmad Rizki Pratama',
                'Siti Nurhaliza Dewi',
                'Muhammad Fajar Sidiq',
                'Andi Putri Melati',
                'Bayu Setiawan',
                'Dinda Ayu Lestari',
                'Eko Prasetyo',
                'Fira Angelina',
                'Galuh Permata',
                'Hendra Gunawan',
                'Indira Sari',
                'Joko Widodo',
                'Kartika Sari',
                'Luthfi Rahman'
            ];
            $name = $names[array_rand($names)];
            $nim = '2024' . str_pad(rand(100001, 999999), 6, '0', STR_PAD_LEFT);
            $members[] = "{$name} - {$nim}";
        }

        return implode("\n", $members);
    }

    private function getRandomCompetitionExperience()
    {
        $experiences = [
            'Juara 2 Lomba Karya Tulis Ilmiah tingkat regional tahun 2023',
            'Finalis Hackathon Nasional Smart City dengan aplikasi e-government',
            'Pernah mengikuti beberapa kompetisi programming dan business case',
            'Juara 1 kompetisi debat bahasa Inggris tingkat universitas',
            'Pengalaman mengikuti olimpiade sains tingkat provinsi saat SMA',
            'Belum pernah mengikuti kompetisi formal, namun siap belajar dan berkompetisi',
            null
        ];
        return $experiences[array_rand($experiences)];
    }

    private function getRandomSpecialNeeds()
    {
        $needs = [
            'Vegetarian - tidak mengonsumsi daging',
            'Alergi seafood dan kacang-kacangan',
            'Membutuhkan akses kursi roda',
            'Memiliki gangguan pendengaran ringan',
            'Tidak dapat mengonsumsi makanan pedas'
        ];
        return $needs[array_rand($needs)];
    }

    private function getRandomSeminarReason()
    {
        $reasons = [
            'Ingin mengetahui perkembangan terbaru di bidang teknologi dan industri Indonesia',
            'Tertarik untuk menambah wawasan tentang peluang karir dan pengembangan diri',
            'Berharap dapat networking dengan profesional dan mendapat insights valuable',
            'Ingin memahami tren industri yang relevan dengan program studi saya',
            'Mencari inspirasi dan motivasi untuk mengembangkan ide bisnis atau startup'
        ];
        return $reasons[array_rand($reasons)];
    }

    private function getRandomInterestField()
    {
        $fields = [
            'Teknologi AI dan Machine Learning',
            'Digital Marketing dan E-commerce',
            'Sustainable Development dan Green Technology',
            'Fintech dan Financial Technology',
            'Data Science dan Business Analytics',
            'UI/UX Design dan Product Development',
            'Cybersecurity dan Information Security',
            'Renewable Energy dan Clean Technology'
        ];
        return $fields[array_rand($fields)];
    }

    private function getRandomIndonesianTeamName()
    {
        $teamNames = [
            'Garuda',
            'Nusantara',
            'Bhinneka',
            'Merah Putih',
            'Pancasila',
            'Wijaya',
            'Sriwijaya',
            'Majapahit',
            'Borobudur',
            'Diponegoro',
            'Gajah Mada',
            'Kartini',
            'Bung Karno',
            'Innovator',
            'CodeMaster',
            'TechStar',
            'SmartGen',
            'FutureTech',
            'NextGen'
        ];
        return $teamNames[array_rand($teamNames)];
    }

    private function getRandomFaculty()
    {
        $faculties = ['Teknik', 'Ekonomi', 'Hukum', 'Kedokteran', 'Pertanian', 'FKIP'];
        return $faculties[array_rand($faculties)];
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

    private function generateCustomFiles()
    {
        $files = [
            'student_id_card' => 'custom_files/student_cards/ktm_' . rand(1000, 9999) . '.jpg',
            'payment_proof' => 'custom_files/payment_proofs/bukti_' . rand(1000, 9999) . '.jpg',
            'cv_file' => 'custom_files/documents/cv_' . rand(1000, 9999) . '.pdf',
            'motivation_letter' => 'custom_files/documents/surat_motivasi_' . rand(1000, 9999) . '.pdf'
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
}
