<?php

namespace Database\Factories;

use App\Models\PaymentTransaction;
use App\Models\UnitKegiatan;
use App\Models\AnonymousEventRegistration;
use App\Models\PaymentConfiguration;
use App\Models\Feed;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentTransaction>
 */
class PaymentTransactionFactory extends Factory
{
    protected $model = PaymentTransaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['pending', 'paid', 'failed', 'cancelled']);

        return [
            'unit_kegiatan_id' => UnitKegiatan::factory(),
            'anonymous_registration_id' => AnonymousEventRegistration::factory(),
            'payment_configuration_id' => PaymentConfiguration::factory(),
            'feed_id' => Feed::factory()->paidEvent(),
            'transaction_id' => 'TXN-' . strtoupper($this->faker->lexify('???')) . '-' . time() . '-' . $this->faker->numberBetween(1000, 9999),
            'amount' => $this->faker->numberBetween(25000, 100000),
            'currency' => 'IDR',
            'status' => $status,
            'payment_method' => $this->faker->randomElement(['Bank Transfer BCA', 'Dana', 'GoPay', 'OVO']),
            'payment_details' => [
                'account_number' => $this->faker->numerify('##########'),
                'account_name' => $this->faker->company(),
            ],
            'custom_data' => [
                'student_id' => $this->faker->numerify('##########'),
                'phone_number' => $this->faker->phoneNumber(),
            ],
            'custom_files' => [],
            'notes' => $this->faker->optional()->sentence(),
            'paid_at' => $status === 'paid' ? $this->faker->dateTimeBetween('-1 month', 'now') : null,
            'expires_at' => $this->faker->dateTimeBetween('now', '+1 week'),
            'proof_of_payment' => $status === 'paid' || $this->faker->boolean(30) ? 'payment_proofs/' . $this->faker->uuid() . '.jpg' : null,
        ];
    }

    /**
     * Indicate that the transaction is pending.
     */
    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'pending',
            'paid_at' => null,
        ]);
    }

    /**
     * Indicate that the transaction is paid.
     */
    public function paid(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'paid',
            'paid_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'proof_of_payment' => 'payment_proofs/' . $this->faker->uuid() . '.jpg',
        ]);
    }

    /**
     * Indicate that the transaction has failed.
     */
    public function failed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'failed',
            'paid_at' => null,
            'notes' => 'Payment failed due to insufficient funds',
        ]);
    }
}
