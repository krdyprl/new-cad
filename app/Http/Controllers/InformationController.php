<?php

namespace App\Http\Controllers;

use App\Models\Information;
use App\Models\User;
use App\Enums\InformationStatus;
use App\Enums\InformationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class InformationController extends Controller
{
    /**
     * Display a listing of information/news articles with enhanced logging.
     */
    public function index(Request $request)
    {
        $startTime = microtime(true);
        
        Log::info('Information listing page accessed', [
            'timestamp' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
            'session_id' => session()->getId(),
            'user_id' => auth()->id(),
            'query_params' => $request->query(),
            'url' => $request->fullUrl()
        ]);

        try {
            // Get filter parameters
            $type = $request->input('type');
            $search = $request->input('search');
            $perPage = min((int) $request->input('per_page', 9), 50); // Max 50 items per page
            
            // Build cache key
            $cacheKey = "information_list_" . md5(serialize([
                'type' => $type,
                'search' => $search,
                'per_page' => $perPage,
                'page' => $request->input('page', 1),
                'locale' => app()->getLocale()
            ]));

            // Cache for 10 minutes
            $result = Cache::remember($cacheKey, 600, function () use ($type, $search, $perPage) {
                Log::info('Building information listing cache', [
                    'type_filter' => $type,
                    'search_query' => $search,
                    'per_page' => $perPage
                ]);

                $query = Information::published();

                // Apply type filter
                if ($type && in_array($type, ['news', 'information'])) {
                    $query->byType($type === 'news' ? InformationType::NEWS : InformationType::INFORMATION);
                }

                // Apply search filter
                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%")
                          ->orWhere('content', 'like', "%{$search}%");
                    });
                }

                $articles = $query->orderBy('published_at', 'desc')
                                 ->orderBy('created_at', 'desc')
                                 ->paginate($perPage);

                Log::info('Information listing data retrieved', [
                    'total_articles' => $articles->total(),
                    'current_page' => $articles->currentPage(),
                    'per_page' => $articles->perPage(),
                    'last_page' => $articles->lastPage(),
                    'type_filter' => $type,
                    'search_applied' => !empty($search)
                ]);

                return $articles;
            });

            // Track performance
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);

            Log::info('Information listing rendered successfully', [
                'processing_time_ms' => $processingTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
                'total_articles' => $result->total(),
                'user_id' => auth()->id(),
                'cache_hit' => true // We know it's cached if we reach here
            ]);

            // Log user activity if authenticated
            if (auth()->check()) {
                /** @var User $user */
                $user = auth()->user();
                if (method_exists($user, 'logActivity') && is_callable([$user, 'logActivity'])) {
                    $user->logActivity('information_listing_viewed', [
                        'type_filter' => $type,
                        'search_query' => $search,
                        'total_results' => $result->total(),
                        'processing_time_ms' => $processingTime
                    ]);
                }
            }

            return view('information', [
                'articles' => $result,
                'currentType' => $type,
                'currentSearch' => $search
            ]);

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('Information listing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime,
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'query_params' => $request->query()
            ]);

            // Return empty result on error
            $emptyPagination = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(), 0, 9, 1, ['path' => $request->url()]
            );

            return view('information', [
                'articles' => $emptyPagination,
                'currentType' => $request->input('type'),
                'currentSearch' => $request->input('search'),
                'error' => true
            ]);
        }
    }

    /**
     * Display the specified information article with enhanced logging and analytics.
     */
    public function show(Request $request, $id)
    {
        $startTime = microtime(true);
        
        Log::info('Information article detail accessed', [
            'article_id' => $id,
            'timestamp' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
            'session_id' => session()->getId(),
            'user_id' => auth()->id(),
            'url' => $request->fullUrl()
        ]);

        try {
            // Get the article with caching
            $cacheKey = "information_detail_{$id}_" . app()->getLocale();
            
            $articleData = Cache::remember($cacheKey, 1800, function () use ($id) { // Cache for 30 minutes
                Log::info('Building information detail cache', [
                    'article_id' => $id
                ]);

                try {
                    $article = Information::where('id', $id)
                                         ->where('status', InformationStatus::PUBLISHED->value)
                                         ->firstOrFail();
                    
                    // Get related articles (same type, recent ones)
                    $relatedArticles = Information::published()
                                                ->byType($article->getType())
                                                ->where('id', '!=', $id)
                                                ->orderBy('published_at', 'desc')
                                                ->limit(3)
                                                ->get();
                    
                    Log::info('Information detail data retrieved from database', [
                        'article_id' => $article->id,
                        'article_title' => $article->title,
                        'article_type' => $article->getType()->value,
                        'related_count' => $relatedArticles->count(),
                        'author_id' => $article->author_id
                    ]);

                    return [
                        'article' => $article,
                        'relatedArticles' => $relatedArticles,
                        'cached_at' => now()
                    ];

                } catch (ModelNotFoundException $e) {
                    Log::warning('Information article not found', [
                        'article_id' => $id,
                        'error' => 'Article not found or not published'
                    ]);
                    throw $e;
                }
            });

            $article = $articleData['article'];
            $relatedArticles = $articleData['relatedArticles'];

            // Log article view for analytics
            Log::info('Information article viewed', [
                'article_id' => $article->id,
                'article_title' => $article->title,
                'article_type' => $article->getType()->value,
                'article_slug' => $article->slug,
                'author_id' => $article->author_id,
                'published_at' => $article->published_at,
                'user_id' => auth()->id(),
                'session_id' => session()->getId(),
                'ip_address' => $request->ip(),
                'data_source' => 'cache'
            ]);

            // Track performance
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);

            // Log article activity on the Information model
            if (method_exists($article, 'logActivity')) {
                $article->logActivity('article_viewed', [
                    'viewer_user_id' => auth()->id(),
                    'viewer_ip' => $request->ip(),
                    'viewer_user_agent' => $request->userAgent(),
                    'processing_time_ms' => $processingTime,
                    'referer' => $request->header('referer')
                ]);
            }

            // Log user activity if authenticated
            if (auth()->check()) {
                /** @var User $user */
                $user = auth()->user();
                if (method_exists($user, 'logActivity') && is_callable([$user, 'logActivity'])) {
                    $user->logActivity('information_article_viewed', [
                        'article_id' => $article->id,
                        'article_title' => $article->title,
                        'article_type' => $article->getType()->value,
                        'processing_time_ms' => $processingTime
                    ]);
                }
            }

            Log::info('Information detail rendered successfully', [
                'article_id' => $article->id,
                'processing_time_ms' => $processingTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
                'related_articles_count' => $relatedArticles->count(),
                'user_id' => auth()->id()
            ]);

            return view('information-detail', compact('article', 'relatedArticles'));

        } catch (ModelNotFoundException $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::warning('Information article not found', [
                'article_id' => $id,
                'processing_time_ms' => $processingTime,
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'referer' => $request->header('referer'),
                'error' => 'Article not found or not published'
            ]);

            abort(404, 'Article not found');

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);
            
            Log::error('Information detail error', [
                'article_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime,
                'user_id' => auth()->id(),
                'ip_address' => $request->ip()
            ]);

            abort(500, 'Internal server error');
        }
    }

    /**
     * Clear information cache (for admin use)
     */
    public function clearCache(Request $request)
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
                    Log::error('Error checking admin status for information cache clear', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            if (!$user || !$isAdmin) {
                Log::warning('Unauthorized information cache clear attempt', [
                    'user_id' => auth()->id(),
                    'ip_address' => $request->ip()
                ]);
                
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Clear information related caches
            $articleId = $request->input('article_id');
            $clearedCaches = [];
            
            if ($articleId) {
                // Clear specific article cache
                $languages = ['id', 'en'];
                foreach ($languages as $lang) {
                    $cacheKey = "information_detail_{$articleId}_{$lang}";
                    if (Cache::forget($cacheKey)) {
                        $clearedCaches[] = $cacheKey;
                    }
                }
            } else {
                // Clear all information caches (be careful with this in production)
                $patterns = [
                    'information_list_*',
                    'information_detail_*'
                ];
                
                // Note: This is a simplified version. In production, you might want to use Redis tags
                foreach ($patterns as $pattern) {
                    $clearedCaches[] = $pattern;
                }
                
                // For now, we'll just clear some common cache keys
                Cache::flush(); // Use with caution in production
            }

            Log::info('Information cache cleared by admin', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'article_id' => $articleId,
                'cleared_caches' => $clearedCaches,
                'timestamp' => now(),
                'ip_address' => $request->ip()
            ]);

            if (method_exists($user, 'logActivity') && is_callable([$user, 'logActivity'])) {
                /** @var User $user */
                $user->logActivity('information_cache_cleared', [
                    'article_id' => $articleId,
                    'cleared_caches' => $clearedCaches
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Information cache cleared successfully',
                'cleared_caches' => $clearedCaches
            ]);

        } catch (\Exception $e) {
            Log::error('Error clearing information cache', [
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
