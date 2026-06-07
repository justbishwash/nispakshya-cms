<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    // GET /api/settings — returns public site settings for the frontend
    public function __invoke(): JsonResponse
    {
        $keys = [
            'site_name', 'site_tagline', 'logo', 'favicon',
            'contact_email', 'contact_phone', 'contact_address',
            'facebook_url', 'twitter_url', 'instagram_url', 'youtube_url',
        ];

        $settings = [];
        foreach ($keys as $key) {
            $settings[$key] = Setting::get($key);
        }

        return response()->json(['data' => $settings]);
    }
}
