<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

/**
 * Admin Access Middleware
 * 
 * Ensures only admin users can access admin routes
 */
class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request
     */
    public function handle(Request $request, Closure $next): BaseResponse
    {
        if (!$this->userIsAdmin($request)) {
            return $this->handleUnauthorizedAccess($request);
        }

        return $next($request);
    }

    /**
     * Check if the current user is an admin
     */
    private function userIsAdmin(Request $request): bool
    {
        return $request->user()?->role === 'admin';
    }

    /**
     * Handle unauthorized access based on request type
     */
    private function handleUnauthorizedAccess(Request $request): BaseResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Access denied. Admin privileges required.',
                'error' => 'unauthorized'
            ], Response::HTTP_FORBIDDEN);
        }

        abort(Response::HTTP_FORBIDDEN, 'Access denied. Admin privileges required.');
    }
}
