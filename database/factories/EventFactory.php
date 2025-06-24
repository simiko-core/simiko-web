<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\UnitKegiatan;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'unit_kegiatan_id' => UnitKegiatan::factory(),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'event_date' => $this->faker->dateTimeBetween('+1 week', '+1 year'),
            'payment_methods' => ['cash', 'transfer'],
            'created_at' => now(),
            'updated_at' => now(),
            // Add other fields as needed
        ];
    }
}
