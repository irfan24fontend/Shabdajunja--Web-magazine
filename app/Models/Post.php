<?php

namespace App\Models;

use App\Models\Concerns\HasUnicodeSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Post extends Model
{
    use SoftDeletes, HasUnicodeSlug;

    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'excerpt', 'body',
        'featured_image', 'status', 'published_at', 'views', 'reading_time',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected function slugSource(): string
    {
        return 'title';
    }

    protected static function booted(): void
    {
        static::saving(function (Post $post) {
            // Reading time: 200 shobdo = 1 minute (Bangla-friendly)
            $text = trim(strip_tags($post->body ?? ''));
            $words = $text === '' ? 0 : count(preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY));
            $post->reading_time = max(1, (int) ceil($words / 200));

            // Excerpt khali thakle body theke nibe
            if (empty($post->excerpt)) {
                $post->excerpt = Str::limit($text, 200);
            }
        });
    }

    /* ---------- Relations ---------- */

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /* ---------- Scopes ---------- */

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', 'published')
                 ->where('published_at', '<=', now());
    }

    public function scopePopular(Builder $q): Builder
    {
        return $q->orderByDesc('views');
    }

    /* ---------- Helpers ---------- */

    public function getImageUrlAttribute(): ?string
    {
        return $this->featured_image ? asset('storage/' . $this->featured_image) : null;
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function getSafeBodyAttribute(): string
{
    return \Mews\Purifier\Facades\Purifier::clean($this->body, [
        'HTML.Allowed' => 'div,p,br,strong,em,del,a[href],ul,ol,li,blockquote,pre,h1,h2,h3',
    ]);
}

public function getBnDateAttribute(): string
{
    return ($this->published_at ?? $this->created_at)->copy()->locale('bn')->translatedFormat('j F, Y');
}
}