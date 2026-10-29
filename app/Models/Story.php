<?php

namespace App\Models;

use App\Observers\StoryObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[ObservedBy([StoryObserver::class])]
class Story extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'category_id',
        'title',
        'hits',
        'published_at',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function thumbnail(): HasOne
    {
        return $this->hasOne(Thumbnail::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'story_tag');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }    

    #[Scope]
    protected function Published(Builder $query): void
    {
        $query->where('published_at', '<=', now());
    }
}
