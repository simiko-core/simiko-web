<?php

namespace Database\Factories;

use App\Models\PaymentConfiguration;
use App\Models\UnitKegiatan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentConfiguration>
 */
class PaymentConfigurationFactory extends Factory
{
    protected $model = PaymentConfiguration::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'unit_kegiatan_id' => UnitKegiatan::factory(),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'amount' => $this->faker->numberBetween(25000, 100000),
            'currency' => 'IDR',
            'payment_methods' => [
                [
                    'method' => 'Bank Transfer BCA',
                    'account_number' => $this->faker->numerify('##########'),
                    'account_name' => $this->faker->company(),
                    'bank_name' => 'Bank Central Asia',
                ],
                [
                    'method' => 'Dana',
                    'account_number' => $this->faker->phoneNumber(),
                    'account_name' => $this->faker->company(),
                ]
            ],
            'custom_fields' => [
                [
                    'label' => 'Student ID',
                    'name' => 'student-id',
                    'type' => 'text',
                    'placeholder' => 'Enter your student ID',
                    'required' => true,
                ],
                [
                    'label' => 'Phone Number',
                    'name' => 'phone-number',
                    'type' => 'tel',
                    'placeholder' => 'Enter your phone number',
                    'required' => true,
                ]
            ],
            'settings' => [],
        ];
    }
}
