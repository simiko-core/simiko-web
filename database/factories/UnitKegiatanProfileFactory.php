<?php

namespace Database\Factories;

use App\Models\UnitKegiatanProfile;
use App\Models\UnitKegiatan;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitKegiatanProfileFactory extends Factory
{
    protected $model = UnitKegiatanProfile::class;

    public function definition(): array
    {
        return [
            'unit_kegiatan_id' => UnitKegiatan::factory(),
            'description' => $this->faker->paragraph,
            'vision' => $this->faker->sentence(8),
            'mission' => $this->faker->sentence(10),
            'period' => $this->faker->year,
            'created_at' => now(),
            'updated_at' => now(),
            // Add other fields as needed
        ];
    }
}
