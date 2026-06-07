<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    // GET /api/search?q=keyword
    public function __invoke(Request $request): JsonResponse
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 2) {
            return response()->json(['error' => 'Search query too short.'], 422);
        }

        $articles = Article::published()
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('excerpt', 'like', "%{$q}%");
            })
            ->with(['category:id,name,slug'])
            ->select(['id', 'title', 'slug', 'excerpt', 'featured_image', 'category_id', 'published_at'])
            ->latest('published_at')
            ->limit(20)
            ->get();

        $categories = Category::where('is_active', true)
            ->where('name', 'like', "%{$q}%")
            ->limit(5)
            ->get(['id', 'name', 'slug']);

        $tags = Tag::where('name', 'like', "%{$q}%")
            ->limit(5)
            ->get(['id', 'name', 'slug']);

        return response()->json([
            'query'      => $q,
            'articles'   => $articles,
            'categories' => $categories,
            'tags'       => $tags,
        ]);
    }
}
