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
            'vision_mission' => $this->faker->sentence(15) . ' ' . $this->faker->sentence(12),
            'period' => $this->faker->year,
            'background_photo' => null,
            'created_at' => now(),
            'updated_at' => now(),
            // Add other fields as needed
        ];
    }
}
