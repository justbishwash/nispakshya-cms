<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    protected $fillable = [
        'name', 'position', 'type', 'image', 'link_url', 'html_code',
        'open_new_tab', 'starts_at', 'ends_at', 'is_active',
        'impressions', 'clicks',
    ];

    protected function casts(): array
    {
        return [
            'open_new_tab' => 'boolean',
            'is_active'    => 'boolean',
            'starts_at'    => 'datetime',
            'ends_at'      => 'datetime',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    public static function positionLabels(): array
    {
        return [
            'home_top'             => 'Homepage — Top Banner',
            'home_middle'          => 'Homepage — Middle Banner',
            'home_footer'          => 'Homepage — Footer Banner',
            'article_above'        => 'Article Page — Above Article',
            'article_mid'          => 'Article Page — Mid Article',
            'article_end'          => 'Article Page — End Article',
            'sidebar_top'          => 'Sidebar — Top',
            'sidebar_middle'       => 'Sidebar — Middle',
            'sidebar_bottom'       => 'Sidebar — Bottom',
            'mobile_sticky_bottom' => 'Mobile — Sticky Bottom',
        ];
    }
}
