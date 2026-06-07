<?php

namespace App\Models;

use App\Services\NepaliDateService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Article extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'author_id', 'category_id', 'title', 'slug', 'excerpt', 'content',
        'featured_image', 'featured_image_caption', 'video_url',
        'is_breaking', 'breaking_expires_at', 'is_trending', 'is_featured',
        'status', 'published_at', 'scheduled_at',
        'seo_title', 'seo_description', 'seo_keywords',
        'views', 'reading_time',
        'published_date_np', 'published_date_np_en',
    ];

    protected function casts(): array
    {
        return [
            'is_breaking'         => 'boolean',
            'is_trending'         => 'boolean',
            'is_featured'         => 'boolean',
            'published_at'        => 'datetime',
            'scheduled_at'        => 'datetime',
            'breaking_expires_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
            // Estimate reading time (avg 200 words/min)
            if (empty($article->reading_time) && $article->content) {
                $wordCount = str_word_count(strip_tags($article->content));
                $article->reading_time = max(1, (int) ceil($wordCount / 200));
            }
            // Set Nepali publish date
            static::fillNepaliDate($article);
        });
        static::updating(function ($article) {
            if ($article->isDirty('content')) {
                $wordCount = str_word_count(strip_tags($article->content));
                $article->reading_time = max(1, (int) ceil($wordCount / 200));
            }
            // Recalculate Nepali date if published_at changes
            if ($article->isDirty('published_at') || $article->isDirty('status')) {
                static::fillNepaliDate($article);
            }
        });
    }

    protected static function fillNepaliDate($article): void
    {
        $date = $article->published_at
            ? \Carbon\Carbon::parse($article->published_at)->toDateString()
            : now()->toDateString();

        $np = NepaliDateService::convert($date);
        $article->published_date_np    = $np['np'];
        $article->published_date_np_en = $np['np_en'];
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeBreaking($query)
    {
        return $query->where('is_breaking', true)
            ->where(function ($q) {
                $q->whereNull('breaking_expires_at')
                    ->orWhere('breaking_expires_at', '>', now());
            });
    }

    // Relationships
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
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

    public function gallery(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'article_media')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function pageViews(): HasMany
    {
        return $this->hasMany(PageView::class);
    }
}
