<?php

namespace Database\Factories;

use App\Models\Story;
use App\Models\Thumbnail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Thumbnail>
 */
class ThumbnailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'story_id' => Story::factory(),
            'path' => 'thumbnaile/' . fake()->unique()->uuid() . '.jpg',
        ];
    }
}
