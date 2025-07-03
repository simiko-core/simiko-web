<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\User;
use App\Models\UnitKegiatan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    protected $model = Admin::class;

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
        ];
    }
}
