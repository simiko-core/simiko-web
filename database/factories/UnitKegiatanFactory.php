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
            'name' => $this->faker->company,
            'logo' => $this->faker->imageUrl(200, 200, 'business', true, 'UKM'),
            // Add other fields as needed
        ];
    }
}
