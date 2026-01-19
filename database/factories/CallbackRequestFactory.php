<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CallbackRequest>
 */
class CallbackRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->numerify('+7##########'),
            'comment' => fake()->optional()->sentence(),
            'page_url' => fake()->url(),
            'utm' => [
                'utm_source' => fake()->randomElement(['google', 'yandex', 'direct', null]),
                'utm_medium' => fake()->randomElement(['cpc', 'organic', 'referral', null]),
                'utm_campaign' => fake()->optional()->word(),
            ],
            'status' => 'new',
        ];
    }

    /**
     * Обработанная заявка
     */
    public function processed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processed',
        ]);
    }

    /**
     * Спам заявка
     */
    public function spam(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'spam',
        ]);
    }
}
