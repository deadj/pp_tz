<?php

namespace Tests\Feature;

use App\Models\Story;
use App\Models\Tag;
use App\Repositories\StoryList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Override;
use Tests\TestCase;

class MainTest extends TestCase
{
    use RefreshDatabase;

    private StoryList $storyList;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->storyList = $this->app->make(StoryList::class);

        Cache::flush();
        DB::enableQueryLog();        
    }

    public function test_repeat_query(): void
    {
        Story::factory()->count(15)->create();

        $storiesFromDB = $this->storyList->getMostPopularStories(5);

        DB::flushQueryLog();

        $storiesFromCache = $this->storyList->getMostPopularStories(5);
        $queriesCount = count(DB::getQueryLog());

        $this->assertEquals($storiesFromDB->pluck('id'), $storiesFromCache->pluck('id'));
        $this->assertEquals(0, $queriesCount);
    }

    public function test_different_keys(): void
    {
        Story::factory()->count(15)->create();

        $stories5 = $this->storyList->getMostPopularStories(5);
        $stories10 = $this->storyList->getMostPopularStories(10);

        $this->assertCount(5, $stories5);
        $this->assertCount(10, $stories10);
        $this->assertNotEquals($stories5->pluck('id'), $stories10->pluck('id'));
    }

    public function test_cache_invalidation(): void
    {
        $firstTag = Tag::factory()->create(['name' => 'First Tag', 'slug' => 'first_tag']);
        $story = Story::factory()->create();
        $story->tags()->attach($firstTag->id);

        Tag::factory()->create(['name' => 'Second Tag', 'slug' => 'second_tag']);

        $this->storyList->getStoriesForTag('first_tag');
        $storiesBySecondTag = $this->storyList->getStoriesForTag('second_tag');

        DB::flushQueryLog();

        $story->update(['title' => 'Updated Title']);

        $this->storyList->getMostPopularStories(10);
        $this->storyList->getStoriesForTag('first_tag');
        $queriesCount = count(DB::getQueryLog());

        $this->assertGreaterThan(0, $queriesCount);

        DB::flushQueryLog();

        $storiesBySecondTagAfterUpdate = $this->storyList->getStoriesForTag('second_tag');
        $queriesCount = count(DB::getQueryLog());

        $this->assertEquals(0, $queriesCount);
        $this->assertEquals($storiesBySecondTag->pluck('id'), $storiesBySecondTagAfterUpdate->pluck('id'));

        DB::flushQueryLog();
        $story->tags()->detach(); 
        $story->delete();

        $this->storyList->getMostPopularStories(10);
        $this->storyList->getStoriesForTag('first_tag');
        $queriesCount = count(DB::getQueryLog());

        $this->assertGreaterThan(0, $queriesCount);
    }
}
