<?php

namespace Database\Factories;

use App\Models\UnitKegiatan;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitKegiatanFactory extends Factory
{
    protected $model = UnitKegiatan::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company(),
            'alias' => strtoupper($this->faker->unique()->lexify('???')),
            'category' => $this->faker->randomElement(['Himpunan', 'UKM Olahraga', 'UKM Seni', 'UKM Keagamaan']),
            'logo' => ['logo_unit_kegiatan/' . $this->faker->uuid() . '.png'],
            'open_registration' => $this->faker->boolean(80),
        ];
    }
}
