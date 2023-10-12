<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\News>
 */
class NewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => ucwords(fake()->words(rand(3, 6), true)),
            'body'  => fake()->text(),
            'thumbnail'  => fake()->imageUrl(),
            'completed' => fake()->randomElement([true, false]),
            'visible' => fake()->randomElement([true, false]),

            'user_id' => fake()->numberBetween(1,5)
        ];
    }
}
