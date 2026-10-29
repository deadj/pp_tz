<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Story;
use App\Models\Tag;
use App\Models\Thumbnail;
use App\Models\Vote;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = Category::factory()->count(5)->create();
        $tags = Tag::factory()->count(10)->create();

        Story::factory()->count(50)->recycle($categories)->create()->each(function (Story $story) use ($tags) {
            $story->tags()->attach($tags->random(random_int(1, 3))->pluck('id')->all());

            Thumbnail::factory()->create(['story_id' => $story->id]);

            Vote::factory()
                ->count(random_int(0, 10))
                ->create([
                    'story_id' => $story->id,
                ]);
        });        
    }
}
