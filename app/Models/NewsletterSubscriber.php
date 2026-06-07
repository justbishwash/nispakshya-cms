<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    protected $fillable = ['email', 'name', 'is_active', 'verified_at'];

    protected function casts(): array
    {
        return [
            'is_active'   => 'boolean',
            'verified_at' => 'datetime',
        ];
    }
}
