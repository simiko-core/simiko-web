<?php

namespace Database\Factories;

use App\Models\ActivityGallery;
use App\Models\UnitKegiatan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityGallery>
 */
class ActivityGalleryFactory extends Factory
{
    protected $model = ActivityGallery::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'unit_kegiatan_id' => UnitKegiatan::factory(),
            'image' => 'activity_galleries/' . $this->faker->uuid() . '.jpg',
            'caption' => $this->faker->optional()->sentence(),
        ];
    }
}
