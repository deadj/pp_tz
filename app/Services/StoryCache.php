<?php

namespace App\Services;

use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class StoryCache
{
    public const POPULAR_TTL = 3600;
    public const TAG_TTL = 21600;

    protected const STORIES_POPULAR_KEY = 'stories:popular';
    protected const STORIES_TAG_KEY = 'stories:tag';

    public function getMostPopularStories(int $number, Closure $loader): Collection
    {
        return Cache::tags([static::STORIES_POPULAR_KEY])->remember(
            static::STORIES_POPULAR_KEY . ':' . $number, 
            static::POPULAR_TTL, 
            $loader
        );
    }

    public function getStoriesForTag(string $tagSlug, Closure $loader): Collection
    {
        return Cache::tags([$this->getTagKey($tagSlug)])->remember(
            $this->getTagKey($tagSlug), 
            static::TAG_TTL, $loader
        );
    }

    public function clearPopular(): void
    {
        Cache::tags([static::STORIES_POPULAR_KEY])->flush();
    }

    public function clearTags(iterable $tagSlugs): void
    {
        foreach ($tagSlugs as $slug) {
            Cache::tags([$this->getTagKey($slug)])->flush();
        }
    }

    protected function getTagKey(string $tagSlug): string
    {
        return static::STORIES_TAG_KEY . ':' . $tagSlug;
    }
}