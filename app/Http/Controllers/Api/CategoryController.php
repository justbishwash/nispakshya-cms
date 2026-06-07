<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    // GET /api/categories
    public function index(): JsonResponse
    {
        $categories = Category::where('is_active', true)
            ->withCount(['articles' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'icon', 'image', 'color', 'parent_id']);

        return response()->json(['data' => $categories]);
    }

    // GET /api/categories/{slug}/articles
    public function articles(string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $articles = $category->articles()
            ->published()
            ->with(['author:id,name', 'category:id,name,slug'])
            ->select(['id', 'title', 'slug', 'excerpt', 'featured_image', 'author_id', 'category_id', 'published_at', 'views', 'reading_time'])
            ->latest('published_at')
            ->paginate(15);

        return response()->json($articles);
    }
}
