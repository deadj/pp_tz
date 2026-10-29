<?php

namespace App\Repositories;

use App\Models\Story;
use App\Services\StoryCache;
use Illuminate\Database\Eloquent\Collection;

class StoryList
{
    public function __construct(
        protected StoryCache $cache,
    ) {}

    public function getMostPopularStories(int $number = 10): Collection
    {
        return $this->cache->getMostPopularStories(
            $number, 
            fn () => Story::published()
                ->withCount(['votes as likes_count' => fn($q) => $q->where('vote', 1)])
                ->with(['category', 'thumbnail', 'tags'])
                ->orderByDesc('likes_count')
                ->orderByDesc('hits')
                ->take($number)
                ->get()
        );
    }

    public function getStoriesForTag(string $tagSlug): Collection
    {
        return $this->cache->getStoriesForTag(
            $tagSlug, 
            fn () => Story::published()
                ->whereHas('tags', fn($q) => $q->where('slug', $tagSlug))
                ->with(['category', 'thumbnail'])
                ->orderByDesc('published_at')
                ->get()
        );
    }
}