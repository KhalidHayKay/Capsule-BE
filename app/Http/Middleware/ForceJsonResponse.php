<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $accept = strtolower($request->header('Accept'));

        $allowedRoutes = ['docs*', 'preview*'];

        // If Accept header is present, not JSON, and route doesn't match any allowed pattern
        if (
            $accept &&
            stripos($accept, 'application/json') === false &&
            ! collect($allowedRoutes)->contains(function ($pattern) use ($request) {
                return $request->is($pattern);
            })
        ) {
            return response()->json([
                'error' => 'Only Accept: application/json is supported.',
            ], 406);
        }

        if (! $accept) {
            $request->headers->set('Accept', 'application/json');
        }

        return $next($request);
    }
}
