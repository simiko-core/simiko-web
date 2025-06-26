<?php

namespace Database\Seeders;

use App\Models\PaymentConfiguration;
use App\Models\PaymentTransaction;
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
        $transactionCounter = 1;

        foreach ($ukms as $ukm) {
            // Create payment configurations for each UKM
            $configurations = [
                [
                    'name' => 'Event Registration Fee',
                    'description' => 'Standard registration fee for events and workshops',
                    'amount' => 50000,
                    'currency' => 'IDR',
                    'is_active' => true,
                    'payment_methods' => [
                        [
                            'method' => 'Bank Transfer BCA',
                            'account_number' => '1234567890',
                            'account_name' => 'Bendahara ' . $ukm->alias,
                            'bank_name' => 'Bank Central Asia (BCA)',
                            'instructions' => 'Transfer to BCA account and send proof of payment'
                        ],
                        [
                            'method' => 'Dana',
                            'account_number' => '081234567890',
                            'account_name' => 'Bendahara ' . $ukm->alias,
                            'instructions' => 'Send payment via Dana and include your name in the message'
                        ],
                        [
                            'method' => 'GoPay',
                            'account_number' => '085678901234',
                            'account_name' => 'Bendahara ' . $ukm->alias,
                            'instructions' => 'Send payment via GoPay and include your name in the message'
                        ]
                    ],
                    'custom_fields' => [
                        [
                            'label' => 'Student ID',
                            'name' => 'student_id',
                            'type' => 'text',
                            'placeholder' => 'Enter your student ID',
                            'required' => true
                        ],
                        [
                            'label' => 'Faculty',
                            'name' => 'faculty',
                            'type' => 'select',
                            'options' => 'Fakultas Teknik, Fakultas Ekonomi, Fakultas Hukum, Fakultas Kedokteran, Fakultas Pertanian',
                            'required' => true
                        ],
                        [
                            'label' => 'Phone Number',
                            'name' => 'phone',
                            'type' => 'tel',
                            'placeholder' => '081234567890',
                            'required' => true
                        ]
                    ],
                    'settings' => [
                        'due_date' => Carbon::now()->addDays(7)->format('Y-m-d'),
                        'max_participants' => 100,
                        'terms_conditions' => 'Payment is non-refundable. Please ensure all information is correct before proceeding.',
                        'notes' => 'Standard event registration payment configuration'
                    ]
                ],
                [
                    'name' => 'Workshop Payment',
                    'description' => 'Payment for specialized workshops and training sessions',
                    'amount' => 75000,
                    'currency' => 'IDR',
                    'is_active' => true,
                    'payment_methods' => [
                        [
                            'method' => 'Bank Transfer Mandiri',
                            'account_number' => '9876543210',
                            'account_name' => 'Kas ' . $ukm->alias,
                            'bank_name' => 'Bank Mandiri',
                            'instructions' => 'Transfer to Mandiri account and send proof of payment'
                        ],
                        [
                            'method' => 'OVO',
                            'account_number' => '087890123456',
                            'account_name' => 'Kas ' . $ukm->alias,
                            'instructions' => 'Send payment via OVO and include your name in the message'
                        ]
                    ],
                    'custom_fields' => [
                        [
                            'label' => 'Student ID',
                            'name' => 'student_id',
                            'type' => 'text',
                            'placeholder' => 'Enter your student ID',
                            'required' => true
                        ],
                        [
                            'label' => 'Workshop Preference',
                            'name' => 'workshop_preference',
                            'type' => 'select',
                            'options' => 'Programming, Design, Business, Leadership, Technical Skills',
                            'required' => true
                        ],
                        [
                            'label' => 'Experience Level',
                            'name' => 'experience_level',
                            'type' => 'radio',
                            'options' => 'Beginner, Intermediate, Advanced',
                            'required' => true
                        ]
                    ],
                    'settings' => [
                        'due_date' => Carbon::now()->addDays(14)->format('Y-m-d'),
                        'max_participants' => 50,
                        'terms_conditions' => 'Workshop materials will be provided. Please bring your own laptop if required.',
                        'notes' => 'Workshop payment with skill level assessment'
                    ]
                ],
                [
                    'name' => 'Membership Fee',
                    'description' => 'Annual membership fee for UKM members',
                    'amount' => 25000,
                    'currency' => 'IDR',
                    'is_active' => true,
                    'payment_methods' => [
                        [
                            'method' => 'Bank Transfer BNI',
                            'account_number' => '5555666677',
                            'account_name' => 'Sekretaris ' . $ukm->alias,
                            'bank_name' => 'Bank Negara Indonesia (BNI)',
                            'instructions' => 'Transfer to BNI account and send proof of payment'
                        ],
                        [
                            'method' => 'ShopeePay',
                            'account_number' => '089012345678',
                            'account_name' => 'Sekretaris ' . $ukm->alias,
                            'instructions' => 'Send payment via ShopeePay and include your name in the message'
                        ]
                    ],
                    'custom_fields' => [
                        [
                            'label' => 'Student ID',
                            'name' => 'student_id',
                            'type' => 'text',
                            'placeholder' => 'Enter your student ID',
                            'required' => true
                        ],
                        [
                            'label' => 'Year of Study',
                            'name' => 'year_of_study',
                            'type' => 'select',
                            'options' => '1st Year, 2nd Year, 3rd Year, 4th Year, 5th Year',
                            'required' => true
                        ],
                        [
                            'label' => 'Join Activities',
                            'name' => 'join_activities',
                            'type' => 'checkbox',
                            'required' => false
                        ]
                    ],
                    'settings' => [
                        'due_date' => Carbon::now()->addDays(30)->format('Y-m-d'),
                        'max_participants' => 200,
                        'terms_conditions' => 'Membership is valid for one academic year. Benefits include access to all UKM activities.',
                        'notes' => 'Annual membership payment'
                    ]
                ]
            ];

            foreach ($configurations as $config) {
                $paymentConfig = PaymentConfiguration::create([
                    'unit_kegiatan_id' => $ukm->id,
                    'name' => $config['name'],
                    'description' => $config['description'],
                    'amount' => $config['amount'],
                    'currency' => $config['currency'],
                    'is_active' => $config['is_active'],
                    'payment_methods' => $config['payment_methods'],
                    'custom_fields' => $config['custom_fields'],
                    'settings' => $config['settings'],
                ]);

                // Create some sample transactions for each configuration
                $transactionCount = rand(3, 8);
                $usersForTransactions = $users->random(min($transactionCount, $users->count()));

                foreach ($usersForTransactions as $user) {
                    $status = ['pending', 'paid', 'failed'][array_rand(['pending', 'paid', 'failed'])];
                    $paymentMethod = $config['payment_methods'][array_rand($config['payment_methods'])]['method'];
                    
                    $transaction = PaymentTransaction::create([
                        'unit_kegiatan_id' => $ukm->id,
                        'user_id' => $user->id,
                        'payment_configuration_id' => $paymentConfig->id,
                        'feed_id' => null, // Not linked to specific event
                        'transaction_id' => 'TXN-' . $ukm->alias . '-' . time() . '-' . rand(1000, 9999),
                        'amount' => $config['amount'],
                        'currency' => $config['currency'],
                        'status' => $status,
                        'payment_method' => $paymentMethod,
                        'payment_details' => [
                            'account_number' => $config['payment_methods'][array_rand($config['payment_methods'])]['account_number'],
                            'account_name' => $config['payment_methods'][array_rand($config['payment_methods'])]['account_name'],
                        ],
                        'custom_data' => [
                            'student_id' => '2024' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                            'faculty' => ['Fakultas Teknik', 'Fakultas Ekonomi', 'Fakultas Hukum'][array_rand(['Fakultas Teknik', 'Fakultas Ekonomi', 'Fakultas Hukum'])],
                            'phone' => '08' . rand(100000000, 999999999),
                        ],
                        'notes' => $status === 'paid' ? 'Payment confirmed via ' . $paymentMethod : null,
                        'paid_at' => $status === 'paid' ? Carbon::now()->subDays(rand(1, 30)) : null,
                        'expires_at' => $status === 'pending' ? Carbon::now()->addDays(7) : null,
                        'created_at' => Carbon::now()->subDays(rand(1, 60)),
                        'updated_at' => Carbon::now()->subDays(rand(1, 60)),
                    ]);
                }
            }
        }

        // Create some transactions linked to events
        $paidEvents = Feed::where('type', 'event')
            ->where('is_paid', true)
            ->get();

        foreach ($paidEvents as $event) {
            $eventUsers = $users->random(rand(2, 5));
            
            foreach ($eventUsers as $user) {
                $status = ['pending', 'paid'][array_rand(['pending', 'paid'])];
                
                PaymentTransaction::create([
                    'unit_kegiatan_id' => $event->unit_kegiatan_id,
                    'user_id' => $user->id,
                    'payment_configuration_id' => PaymentConfiguration::where('unit_kegiatan_id', $event->unit_kegiatan_id)->first()->id,
                    'feed_id' => $event->id,
                    'transaction_id' => 'TXN-EVENT-' . time() . '-' . rand(1000, 9999),
                    'amount' => $event->price,
                    'currency' => 'IDR',
                    'status' => $status,
                    'payment_method' => $event->payment_methods[array_rand($event->payment_methods)]['method'] ?? 'Bank Transfer',
                    'payment_details' => $event->payment_methods[array_rand($event->payment_methods)] ?? [],
                    'custom_data' => [
                        'student_id' => '2024' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                        'phone' => '08' . rand(100000000, 999999999),
                    ],
                    'notes' => $status === 'paid' ? 'Event registration payment confirmed' : 'Event registration pending',
                    'paid_at' => $status === 'paid' ? Carbon::now()->subDays(rand(1, 7)) : null,
                    'expires_at' => $event->event_date,
                    'created_at' => Carbon::now()->subDays(rand(1, 14)),
                    'updated_at' => Carbon::now()->subDays(rand(1, 14)),
                ]);
            }
        }

        // Create sample payment configurations with file upload fields
        $configurations = [
            [
                'unit_kegiatan_id' => 1, // HMIF
                'name' => 'Workshop Registration with Documents',
                'description' => 'Registration for programming workshop with required document uploads',
                'amount' => 75000,
                'currency' => 'IDR',
                'is_active' => true,
                'payment_methods' => [
                    [
                        'method' => 'Bank Transfer BCA',
                        'account_number' => '1234567890',
                        'account_name' => 'HMIF UNS',
                        'bank_name' => 'Bank Central Asia',
                    ],
                    [
                        'method' => 'Dana',
                        'account_number' => '08123456789',
                        'account_name' => 'HMIF UNS',
                    ],
                ],
                'custom_fields' => [
                    [
                        'label' => 'Student ID',
                        'name' => 'student_id',
                        'type' => 'text',
                        'placeholder' => 'Enter your student ID',
                        'required' => true,
                    ],
                    [
                        'label' => 'Programming Experience',
                        'name' => 'programming_experience',
                        'type' => 'select',
                        'placeholder' => 'Select your experience level',
                        'required' => true,
                        'options' => 'Beginner, Intermediate, Advanced',
                    ],
                    [
                        'label' => 'Portfolio/CV',
                        'name' => 'portfolio',
                        'type' => 'file',
                        'required' => true,
                        'file_types' => 'pdf,doc,docx',
                        'max_file_size' => 5,
                    ],
                    [
                        'label' => 'Motivation Letter',
                        'name' => 'motivation_letter',
                        'type' => 'file',
                        'required' => true,
                        'file_types' => 'pdf,doc,docx',
                        'max_file_size' => 3,
                    ],
                ],
                'settings' => [
                    'due_date' => now()->addDays(7),
                    'max_participants' => 30,
                    'terms_conditions' => 'Participants must submit all required documents. No refunds after registration.',
                ],
            ],
            [
                'unit_kegiatan_id' => 2, // HMTE
                'name' => 'Competition Registration with Project Files',
                'description' => 'Registration for engineering competition with project submission',
                'amount' => 100000,
                'currency' => 'IDR',
                'is_active' => true,
                'payment_methods' => [
                    [
                        'method' => 'Bank Transfer Mandiri',
                        'account_number' => '0987654321',
                        'account_name' => 'HMTE UNS',
                        'bank_name' => 'Bank Mandiri',
                    ],
                ],
                'custom_fields' => [
                    [
                        'label' => 'Team Name',
                        'name' => 'team_name',
                        'type' => 'text',
                        'placeholder' => 'Enter your team name',
                        'required' => true,
                    ],
                    [
                        'label' => 'Team Members',
                        'name' => 'team_members',
                        'type' => 'textarea',
                        'placeholder' => 'List all team members with their student IDs',
                        'required' => true,
                    ],
                    [
                        'label' => 'Project Proposal',
                        'name' => 'project_proposal',
                        'type' => 'file',
                        'required' => true,
                        'file_types' => 'pdf',
                        'max_file_size' => 10,
                    ],
                    [
                        'label' => 'Project Images',
                        'name' => 'project_images',
                        'type' => 'file',
                        'required' => false,
                        'file_types' => 'jpg,jpeg,png',
                        'max_file_size' => 5,
                    ],
                ],
                'settings' => [
                    'due_date' => now()->addDays(14),
                    'max_participants' => 20,
                    'terms_conditions' => 'Project proposals must be original work. Teams can have 2-4 members.',
                ],
            ],
            [
                'unit_kegiatan_id' => 3, // UKM Foto
                'name' => 'Photography Workshop with Sample Work',
                'description' => 'Registration for photography workshop with portfolio submission',
                'amount' => 50000,
                'currency' => 'IDR',
                'is_active' => true,
                'payment_methods' => [
                    [
                        'method' => 'GoPay',
                        'account_number' => '08123456789',
                        'account_name' => 'UKM Foto UNS',
                    ],
                    [
                        'method' => 'OVO',
                        'account_number' => '08123456789',
                        'account_name' => 'UKM Foto UNS',
                    ],
                ],
                'custom_fields' => [
                    [
                        'label' => 'Camera Type',
                        'name' => 'camera_type',
                        'type' => 'text',
                        'placeholder' => 'e.g., DSLR, Mirrorless, Smartphone',
                        'required' => true,
                    ],
                    [
                        'label' => 'Experience Level',
                        'name' => 'experience_level',
                        'type' => 'radio',
                        'required' => true,
                        'options' => 'Beginner, Intermediate, Advanced',
                    ],
                    [
                        'label' => 'Portfolio (3-5 photos)',
                        'name' => 'portfolio_photos',
                        'type' => 'file',
                        'required' => true,
                        'file_types' => 'jpg,jpeg,png',
                        'max_file_size' => 15,
                    ],
                ],
                'settings' => [
                    'due_date' => now()->addDays(5),
                    'max_participants' => 25,
                    'terms_conditions' => 'Portfolio should contain 3-5 best photos. All skill levels welcome.',
                ],
            ],
        ];

        foreach ($configurations as $config) {
            PaymentConfiguration::create($config);
        }

        // Link existing paid events with payment configurations
        $this->linkEventsToPaymentConfigurations();
    }

    private function linkEventsToPaymentConfigurations()
    {
        // Get all UKMs
        $ukms = UnitKegiatan::all();
        
        foreach ($ukms as $ukm) {
            // Get active payment configurations for this UKM
            $configurations = PaymentConfiguration::where('unit_kegiatan_id', $ukm->id)
                ->where('is_active', true)
                ->get();
            
            if ($configurations->isEmpty()) continue;
            
            // Get paid events for this UKM that don't have payment configurations
            $paidEvents = Feed::where('unit_kegiatan_id', $ukm->id)
                ->where('type', 'event')
                ->where('is_paid', true)
                ->whereNull('payment_configuration_id')
                ->get();
            
            foreach ($paidEvents as $event) {
                // Assign a random payment configuration
                $randomConfig = $configurations->random();
                $event->update([
                    'payment_configuration_id' => $randomConfig->id,
                    'price' => $randomConfig->amount, // Use the configuration amount
                ]);
            }
        }
    }
} 