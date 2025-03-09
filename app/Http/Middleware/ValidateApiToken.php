<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestKey = $request->headers->get('x-api-key');

        if (! $requestKey) {
            return response()->json([], 403);
        }

        $localKey = config('api.key');

        if (! $localKey || $requestKey !== $localKey) {
            return response()->json([], 403);
        }

        return $next($request);
    }
}
