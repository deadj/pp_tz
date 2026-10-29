<?php

namespace Database\Factories;

use App\Models\Story;
use App\Models\Vote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vote>
 */
class VoteFactory extends Factory
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
            'vote' => random_int(0, 1) === 0 ? -1 : 1,
        ];
    }
}
