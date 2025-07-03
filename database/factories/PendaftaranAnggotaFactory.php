<?php

namespace Database\Factories;

use App\Models\PendaftaranAnggota;
use App\Models\User;
use App\Models\UnitKegiatan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PendaftaranAnggota>
 */
class PendaftaranAnggotaFactory extends Factory
{
    protected $model = PendaftaranAnggota::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'unit_kegiatan_id' => UnitKegiatan::factory(),
            'status' => $this->faker->randomElement(['pending', 'accepted', 'rejected']),
        ];
    }

    /**
     * Indicate that the registration is pending.
     */
    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the registration is accepted.
     */
    public function accepted(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'accepted',
        ]);
    }

    /**
     * Indicate that the registration is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'rejected',
        ]);
    }
}
