<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictApiToDomain
{
    /**
     * Allowed origins that can call the API.
     * Only requests from nispakshya.com (and its subdomains) are permitted.
     */
    protected array $allowedOrigins = [
        'https://nispakshya.com',
        'https://www.nispakshya.com',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->headers->get('Origin');
        $referer = $request->headers->get('Referer');

        // Allow server-side / non-browser calls only in local/testing env
        if (app()->environment('local', 'testing')) {
            return $this->addCorsHeaders($next($request), '*');
        }

        // For preflight OPTIONS requests
        if ($request->isMethod('OPTIONS')) {
            if ($origin && in_array($origin, $this->allowedOrigins)) {
                return $this->preflightResponse($origin);
            }
            return response('Forbidden', 403);
        }

        // Check Origin header (set by browsers on cross-origin requests)
        if ($origin) {
            if (!in_array($origin, $this->allowedOrigins)) {
                return response()->json([
                    'error' => 'Access denied. API is only accessible from nispakshya.com',
                ], 403);
            }
            return $this->addCorsHeaders($next($request), $origin);
        }

        // No origin header — direct browser access, curl, etc.
        // Block it in production to prevent direct API browsing
        return response()->json([
            'error' => 'Direct API access is not allowed.',
        ], 403);
    }

    protected function addCorsHeaders(Response $response, string $origin): Response
    {
        $response->headers->set('Access-Control-Allow-Origin', $origin);
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With');
        $response->headers->set('Access-Control-Max-Age', '86400');
        return $response;
    }

    protected function preflightResponse(string $origin): Response
    {
        return response('', 204)
            ->header('Access-Control-Allow-Origin', $origin)
            ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With')
            ->header('Access-Control-Max-Age', '86400');
    }
}
