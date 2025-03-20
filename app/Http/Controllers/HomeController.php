<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Information;
use App\Models\User;
use App\Enums\InformationType;

class HomeController extends Controller
{
    /**
     * Display the homepage with enhanced logging and caching.
     */
    public function index(Request $request)
    {
        $startTime = microtime(true);
        
        // Log homepage visit
        Log::info('Homepage accessed', [
            'timestamp' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
            'session_id' => session()->getId(),
            'user_id' => auth()->id(),
            'is_authenticated' => auth()->check(),
            'accept_language' => $request->header('accept-language'),
            'url' => $request->fullUrl()
        ]);

        try {
            // Cache key for homepage data
            $cacheKey = 'homepage_data_' . app()->getLocale();
            
            // Try to get data from cache first (cache for 15 minutes)
            $cachedData = Cache::remember($cacheKey, 900, function () {
                Log::info('Building homepage cache', [
                    'cache_key' => 'homepage_data_' . app()->getLocale(),
                    'timestamp' => now()
                ]);

                try {
                    // Get latest news/information for homepage
                    $latestNews = Information::published()
                        ->byType(InformationType::NEWS)
                        ->latest('published_at')
                        ->take(3)
                        ->get();

                    $featuredInfo = Information::published()
                        ->byType(InformationType::INFORMATION)
                        ->latest('published_at')
                        ->take(6)
                        ->get();

                    Log::info('Homepage data retrieved successfully', [
                        'news_count' => $latestNews->count(),
                        'info_count' => $featuredInfo->count(),
                        'query_time' => microtime(true)
                    ]);

                    return [
                        'latestNews' => $latestNews,
                        'featuredInfo' => $featuredInfo,
                        'cached_at' => now()
                    ];

                } catch (\Exception $e) {
                    Log::error('Error building homepage cache', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);

                    return [
                        'latestNews' => collect(),
                        'featuredInfo' => collect(),
                        'cached_at' => now(),
                        'error' => true
                    ];
                }
            });

            Log::info('Homepage data prepared', [
                'data_source' => isset($cachedData['cached_at']) ? 'cache' : 'database',
                'news_count' => $cachedData['latestNews']->count(),
                'info_count' => $cachedData['featuredInfo']->count(),
                'has_error' => isset($cachedData['error'])
            ]);

            // Track page performance
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);

            Log::info('Homepage rendered successfully', [
                'processing_time_ms' => $processingTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
                'user_id' => auth()->id(),
                'session_id' => session()->getId()
            ]);

            // Log user activity if authenticated
            if (auth()->check()) {
                /** @var User $user */
                $user = auth()->user();
                if (method_exists($user, 'logActivity') && is_callable([$user, 'logActivity'])) {
                    $user->logActivity('homepage_visited', [
                        'processing_time_ms' => $processingTime,
                        'news_count' => $cachedData['latestNews']->count(),
                        'info_count' => $cachedData['featuredInfo']->count()
                    ]);
                }
            }

            return view('home', [
                'latestNews' => $cachedData['latestNews'],
                'featuredInfo' => $cachedData['featuredInfo']
            ]);

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('Homepage error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime,
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl()
            ]);

            // Clear cache if there's an error
            Cache::forget('homepage_data_' . app()->getLocale());

            // Return view without data if there's an error
            return view('home', [
                'latestNews' => collect(),
                'featuredInfo' => collect()
            ]);
        }
    }

    /**
     * Clear homepage cache (for admin use)
     */
    public function clearCache()
    {
        try {
            /** @var User|null $user */
            $user = auth()->user();
            
            // Check if user is admin
            $isAdmin = false;
            if ($user) {
                try {
                    if (method_exists($user, 'isAdmin') && is_callable([$user, 'isAdmin'])) {
                        $isAdmin = $user->isAdmin();
                    } elseif (property_exists($user, 'is_admin')) {
                        $isAdmin = $user->is_admin;
                    } elseif (property_exists($user, 'role')) {
                        $isAdmin = $user->role === 'admin' || $user->role?->value === 'admin';
                    }
                } catch (\Exception $e) {
                    Log::error('Error checking admin status for cache clear', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            if (!$user || !$isAdmin) {
                Log::warning('Unauthorized cache clear attempt', [
                    'user_id' => auth()->id(),
                    'ip_address' => request()->ip()
                ]);
                
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Clear all homepage related caches
            $languages = ['id', 'en']; // Add your supported languages
            $clearedCaches = [];
            
            foreach ($languages as $lang) {
                $cacheKey = "homepage_data_{$lang}";
                if (Cache::forget($cacheKey)) {
                    $clearedCaches[] = $cacheKey;
                }
            }

            Log::info('Homepage cache cleared by admin', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'cleared_caches' => $clearedCaches,
                'timestamp' => now(),
                'ip_address' => request()->ip()
            ]);

            if (method_exists($user, 'logActivity') && is_callable([$user, 'logActivity'])) {
                /** @var User $user */
                $user->logActivity('homepage_cache_cleared', [
                    'cleared_caches' => $clearedCaches
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Homepage cache cleared successfully',
                'cleared_caches' => $clearedCaches
            ]);

        } catch (\Exception $e) {
            Log::error('Error clearing homepage cache', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to clear cache'
            ], 500);
        }
    }
}
