<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageView extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'article_id', 'ip_address', 'user_agent', 'device', 'viewed_date',
    ];

    protected function casts(): array
    {
        return ['viewed_date' => 'date'];
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
