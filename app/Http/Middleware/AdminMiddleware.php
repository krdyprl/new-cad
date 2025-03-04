<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request with enhanced security logging.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        // Log all admin access attempts
        Log::info('Admin middleware check initiated', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => session()->getId(),
            'timestamp' => now(),
            'is_authenticated' => auth()->check(),
            'user_id' => auth()->id()
        ]);

        // Check authentication
        if (!auth()->check()) {
            Log::warning('Unauthenticated admin access attempt', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => session()->getId(),
                'referer' => $request->header('referer')
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Authentication required',
                    'redirect_url' => route('login')
                ], 401);
            }
            
            return redirect('/login')->with('error', 'Please login to access admin area');
        }

        /** @var User|null $user */
        $user = auth()->user();
        
        // Check admin privileges using role system
        $isAdmin = false;
        
        try {
            // Support both old is_admin field and new role enum
            if (method_exists($user, 'isAdmin') && is_callable([$user, 'isAdmin'])) {
                $isAdmin = $user->isAdmin();
            } elseif (property_exists($user, 'is_admin') && isset($user->is_admin)) {
                $isAdmin = $user->is_admin;
            } elseif (property_exists($user, 'role') && isset($user->role)) {
                $isAdmin = $user->role === UserRole::ADMIN || $user->role === 'admin';
            }
        } catch (\Exception $e) {
            Log::error('Error checking admin privileges', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $isAdmin = false;
        }

        if (!$isAdmin) {
            Log::warning('Unauthorized admin access attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_role' => $user->role ?? ($user->is_admin ?? 'unknown'),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => session()->getId(),
                'timestamp' => now()
            ]);
            
            // Log user activity for audit trail
            if (method_exists($user, 'logActivity') && is_callable([$user, 'logActivity'])) {
                /** @var User $user */
                $user->logActivity('unauthorized_admin_access_attempt', [
                    'url' => $request->fullUrl(),
                    'method' => $request->method()
                ]);
            }
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Access denied. Admin privileges required.'
                ], 403);
            }
            
            abort(403, 'Access denied. Admin privileges required.');
        }

        // Log successful admin access
        $processingTime = round((microtime(true) - $startTime) * 1000, 2);
        
        Log::info('Admin access granted', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->role ?? ($user->is_admin ? 'admin' : 'user'),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'processing_time_ms' => $processingTime,
            'timestamp' => now()
        ]);

        // Log user activity for audit trail
        if (method_exists($user, 'logActivity') && is_callable([$user, 'logActivity'])) {
            /** @var User $user */
            $user->logActivity('admin_access_granted', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'processing_time_ms' => $processingTime
            ]);
        }

        return $next($request);
    }
}
