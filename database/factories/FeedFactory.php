<?php

namespace Database\Factories;

use App\Models\Feed;
use App\Models\UnitKegiatan;
use App\Models\PaymentConfiguration;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Feed>
 */
class FeedFactory extends Factory
{
    protected $model = Feed::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['post', 'event']);
        $isPaid = $type === 'event' ? $this->faker->boolean(30) : false; // 30% chance for paid events

        return [
            'unit_kegiatan_id' => UnitKegiatan::factory(),
            'payment_configuration_id' => $isPaid ? PaymentConfiguration::factory() : null,
            'type' => $type,
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(3, true),
            'image' => 'feeds/' . $this->faker->uuid() . '.jpg',
            'event_date' => $type === 'event' ? $this->faker->dateTimeBetween('now', '+3 months') : null,
            'event_type' => $type === 'event' ? $this->faker->randomElement(['online', 'offline']) : null,
            'location' => $type === 'event' ? $this->faker->address() : null,
            'is_paid' => $isPaid,
            'max_participants' => $type === 'event' ? $this->faker->optional(0.7)->numberBetween(20, 200) : null,
            'registration_token' => $isPaid ? Str::random(32) : null,
        ];
    }

    /**
     * Indicate that the feed is a post.
     */
    public function post(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'post',
            'event_date' => null,
            'event_type' => null,
            'location' => null,
            'is_paid' => false,
            'max_participants' => null,
            'payment_configuration_id' => null,
            'registration_token' => null,
        ]);
    }

    /**
     * Indicate that the feed is a free event.
     */
    public function freeEvent(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'event',
            'event_date' => $this->faker->dateTimeBetween('now', '+3 months'),
            'event_type' => $this->faker->randomElement(['online', 'offline']),
            'location' => $this->faker->address(),
            'is_paid' => false,
            'payment_configuration_id' => null,
            'registration_token' => null,
        ]);
    }

    /**
     * Indicate that the feed is a paid event.
     */
    public function paidEvent(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'event',
            'event_date' => $this->faker->dateTimeBetween('now', '+3 months'),
            'event_type' => $this->faker->randomElement(['online', 'offline']),
            'location' => $this->faker->address(),
            'is_paid' => true,
            'payment_configuration_id' => PaymentConfiguration::factory(),
            'registration_token' => Str::random(32),
        ]);
    }
}
