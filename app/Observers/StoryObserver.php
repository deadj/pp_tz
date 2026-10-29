<?php

namespace App\Observers;

use App\Models\Story;
use App\Services\StoryCache;

class StoryObserver
{
    public function __construct(
        private StoryCache $cache
    ) {}

    /**
     * Handle the Story "created" event.
     */
    public function created(Story $story): void
    {
        $this->clearCache($story->tags()->pluck('slug')->all());
    }

    /**
     * Handle the Story "updated" event.
     */
    public function updated(Story $story): void
    {
        $this->clearCache($story->tags()->pluck('slug')->all());
    }

    /**
     * Handle the Story "deleted" event.
     */
    public function deleted(Story $story): void
    {
        $this->clearCache($story->tags()->pluck('slug')->all());
    }

    /**
     * Handle the Story "restored" event.
     */
    public function restored(Story $story): void
    {
        //
    }

    /**
     * Handle the Story "force deleted" event.
     */
    public function forceDeleted(Story $story): void
    {
        //
    }

    private function clearCache(array $tags): void
    {
        $this->cache->clearPopular();
        $this->cache->clearTags($tags);
    }
}
