<?php

namespace Database\Factories;

use App\Models\AnonymousEventRegistration;
use App\Models\Feed;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AnonymousEventRegistration>
 */
class AnonymousEventRegistrationFactory extends Factory
{
    protected $model = AnonymousEventRegistration::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'feed_id' => Feed::factory()->paidEvent(),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'custom_data' => [
                'student_id' => $this->faker->numerify('##########'),
                'faculty' => $this->faker->randomElement(['Engineering', 'Science', 'Business', 'Arts']),
                'year' => $this->faker->numberBetween(1, 4),
            ],
            'custom_files' => [
                'student_id_card' => 'custom_files/student_id_card/' . $this->faker->uuid() . '.jpg',
            ],
        ];
    }

    /**
     * Indicate that the registration has no custom files.
     */
    public function withoutFiles(): static
    {
        return $this->state(fn(array $attributes) => [
            'custom_files' => [],
        ]);
    }

    /**
     * Indicate that the registration has minimal custom data.
     */
    public function minimal(): static
    {
        return $this->state(fn(array $attributes) => [
            'custom_data' => [
                'student_id' => $this->faker->numerify('##########'),
            ],
            'custom_files' => [],
        ]);
    }
}
