<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\UnitKegiatan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'unit_kegiatan_id' => UnitKegiatan::factory(),
            'title' => $this->faker->sentence(6),
            'content' => $this->faker->paragraph(4),
            'created_at' => now(),
            'updated_at' => now(),
            // Add other fields as needed
        ];
    }
}
