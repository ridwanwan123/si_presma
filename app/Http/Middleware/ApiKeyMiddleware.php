<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-KEY');

        if (!$apiKey || $apiKey !== config('services.presma.api_key')) {
            return response()->json([
                'success' => false,
                'code' => 401,
                'message' => 'Unauthorized. Invalid API Key.',
            ], 401);
        }

        return $next($request);
    }
}