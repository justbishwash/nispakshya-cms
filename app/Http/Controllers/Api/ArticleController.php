<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    // GET /api/articles?page=1&per_page=15&category=&tag=
    public function index(Request $request): JsonResponse
    {
        $query = Article::published()
            ->with(['author:id,name,avatar', 'category:id,name,slug'])
            ->select([
                'id', 'title', 'slug', 'excerpt', 'featured_image',
                'author_id', 'category_id', 'is_breaking', 'is_trending',
                'is_featured', 'published_at', 'published_date_np', 'published_date_np_en',
                'views', 'reading_time',
            ]);

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', fn ($q) => $q->where('slug', $request->tag));
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $request->search . '%');
            });
        }

        $perPage = min((int) $request->get('per_page', 15), 50);
        $articles = $query->latest('published_at')->paginate($perPage);

        return response()->json($articles);
    }

    // GET /api/articles/{slug}
    public function show(string $slug): JsonResponse
    {
        $article = Article::published()
            ->with([
                'author:id,name,avatar,bio',
                'category:id,name,slug',
                'tags:id,name,slug',
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment view count
        $article->increment('views');

        return response()->json($article);
    }

    // GET /api/breaking
    public function breaking(): JsonResponse
    {
        $articles = Article::published()
            ->breaking()
            ->select(['id', 'title', 'slug', 'published_at'])
            ->latest('published_at')
            ->limit(10)
            ->get();

        return response()->json(['data' => $articles]);
    }

    // GET /api/trending
    public function trending(): JsonResponse
    {
        $articles = Article::published()
            ->where('is_trending', true)
            ->with(['category:id,name,slug'])
            ->select(['id', 'title', 'slug', 'featured_image', 'category_id', 'views', 'published_at', 'published_date_np', 'published_date_np_en'])
            ->latest('published_at')
            ->limit(10)
            ->get();

        return response()->json(['data' => $articles]);
    }

    // GET /api/featured
    public function featured(): JsonResponse
    {
        $articles = Article::published()
            ->where('is_featured', true)
            ->with(['category:id,name,slug', 'author:id,name'])
            ->select(['id', 'title', 'slug', 'excerpt', 'featured_image', 'category_id', 'author_id', 'published_at', 'published_date_np', 'published_date_np_en', 'reading_time'])
            ->latest('published_at')
            ->limit(6)
            ->get();

        return response()->json(['data' => $articles]);
    }
}
