<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Story;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Story>
 */
class StoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'title' => fake()->unique()->words(3, true),
            'hits' => random_int(0, 10000),
            'published_at' => now(),
        ];
    }
}
