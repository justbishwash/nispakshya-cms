<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    // GET /api/tags
    public function index(): JsonResponse
    {
        $tags = Tag::withCount(['articles' => fn ($q) => $q->where('status', 'published')])
            ->orderByDesc('articles_count')
            ->get(['id', 'name', 'slug', 'is_trending']);

        return response()->json(['data' => $tags]);
    }

    // GET /api/tags/trending
    public function trending(): JsonResponse
    {
        $tags = Tag::where('is_trending', true)
            ->withCount(['articles' => fn ($q) => $q->where('status', 'published')])
            ->orderByDesc('articles_count')
            ->limit(20)
            ->get(['id', 'name', 'slug']);

        return response()->json(['data' => $tags]);
    }
}
