<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $fillable = ['name', 'slug', 'is_trending'];

    protected function casts(): array
    {
        return ['is_trending' => 'boolean'];
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }
}
