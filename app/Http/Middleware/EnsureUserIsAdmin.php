<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth('api')->check()) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $user = auth('api')->user();

        if (!$user->isAdmin()) {
            return response()->json([
                'message' => 'This action is unauthorized. Admin access required.'
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}

