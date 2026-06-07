<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API — only accessible from nispakshya.com
| CORS restriction is enforced via RestrictApiToDomain middleware
|--------------------------------------------------------------------------
*/

// Articles
Route::get('/articles',           [ArticleController::class, 'index']);
Route::get('/articles/breaking',  [ArticleController::class, 'breaking']);
Route::get('/articles/trending',  [ArticleController::class, 'trending']);
Route::get('/articles/featured',  [ArticleController::class, 'featured']);
Route::get('/articles/{slug}',    [ArticleController::class, 'show']);

// Categories
Route::get('/categories',                      [CategoryController::class, 'index']);
Route::get('/categories/{slug}/articles',      [CategoryController::class, 'articles']);

// Tags
Route::get('/tags',          [TagController::class, 'index']);
Route::get('/tags/trending', [TagController::class, 'trending']);

// Search
Route::get('/search', SearchController::class);

// Site settings (logo, social links etc.)
Route::get('/settings', SettingsController::class);
