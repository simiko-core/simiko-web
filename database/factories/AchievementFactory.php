<?php

namespace Database\Factories;

use App\Models\Achievement;
use App\Models\UnitKegiatan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Achievement>
 */
class AchievementFactory extends Factory
{
    protected $model = Achievement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'unit_kegiatan_id' => UnitKegiatan::factory(),
            'title' => $this->faker->sentence(4),
            'image' => 'achievements/' . $this->faker->uuid() . '.jpg',
            'description' => $this->faker->paragraphs(2, true),
        ];
    }
}
