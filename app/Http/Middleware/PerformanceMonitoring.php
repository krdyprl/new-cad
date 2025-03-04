<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Performance Monitoring Middleware
 * 
 * Tracks request performance, memory usage, and database queries
 * for comprehensive application monitoring and optimization
 */
class PerformanceMonitoring
{
    /**
     * Handle an incoming request with performance tracking.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        $startPeakMemory = memory_get_peak_usage(true);
        
        // Track database queries
        $initialQueryCount = $this->getDatabaseQueryCount();
        
        // Log request start
        Log::channel('performance')->info('Request started', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'start_memory_mb' => round($startMemory / 1024 / 1024, 2),
            'timestamp' => now()
        ]);

        // Process the request
        $response = $next($request);

        // Calculate performance metrics
        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);
        $endPeakMemory = memory_get_peak_usage(true);
        $finalQueryCount = $this->getDatabaseQueryCount();
        
        $executionTime = round(($endTime - $startTime) * 1000, 2);
        $memoryUsed = round(($endMemory - $startMemory) / 1024 / 1024, 2);
        $peakMemoryUsed = round(($endPeakMemory - $startPeakMemory) / 1024 / 1024, 2);
        $queryCount = $finalQueryCount - $initialQueryCount;

        // Log performance metrics
        $performanceData = [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'status_code' => $response->getStatusCode(),
            'execution_time_ms' => $executionTime,
            'memory_used_mb' => $memoryUsed,
            'peak_memory_mb' => $peakMemoryUsed,
            'database_queries' => $queryCount,
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
            'timestamp' => now()
        ];

        // Determine log level based on performance
        $logLevel = $this->getLogLevel($executionTime, $memoryUsed, $queryCount);
        
        Log::channel('performance')->log($logLevel, 'Request completed', $performanceData);

        // Log slow requests
        if ($executionTime > 1000) { // Slower than 1 second
            Log::channel('performance')->warning('Slow request detected', [
                'performance' => $performanceData,
                'threshold_ms' => 1000,
                'suggestion' => 'Consider optimization'
            ]);
        }

        // Log high memory usage
        if ($memoryUsed > 50) { // More than 50MB
            Log::channel('performance')->warning('High memory usage detected', [
                'performance' => $performanceData,
                'threshold_mb' => 50,
                'suggestion' => 'Check for memory leaks'
            ]);
        }

        // Log excessive database queries
        if ($queryCount > 20) { // More than 20 queries
            Log::channel('performance')->warning('Excessive database queries detected', [
                'performance' => $performanceData,
                'threshold_queries' => 20,
                'suggestion' => 'Consider eager loading or query optimization'
            ]);
        }

        // Store performance metrics in cache for analytics
        $this->storePerformanceMetrics($performanceData);

        // Add performance headers in development
        if (app()->environment('local', 'development')) {
            $response->headers->set('X-Execution-Time', $executionTime . 'ms');
            $response->headers->set('X-Memory-Used', $memoryUsed . 'MB');
            $response->headers->set('X-Database-Queries', $queryCount);
        }

        return $response;
    }

    /**
     * Get database query count
     */
    private function getDatabaseQueryCount(): int
    {
        return DB::getQueryLog() ? count(DB::getQueryLog()) : 0;
    }

    /**
     * Determine log level based on performance metrics
     */
    private function getLogLevel(float $executionTime, float $memoryUsed, int $queryCount): string
    {
        // Critical performance issues
        if ($executionTime > 5000 || $memoryUsed > 100 || $queryCount > 50) {
            return 'error';
        }

        // Warning level performance issues
        if ($executionTime > 2000 || $memoryUsed > 50 || $queryCount > 20) {
            return 'warning';
        }

        // Good performance
        if ($executionTime < 500 && $memoryUsed < 10 && $queryCount < 5) {
            return 'debug';
        }

        // Normal performance
        return 'info';
    }

    /**
     * Store performance metrics for analytics
     */
    private function storePerformanceMetrics(array $data): void
    {
        try {
            $cacheKey = 'performance_metrics_' . date('Y-m-d-H');
            $existingMetrics = Cache::get($cacheKey, []);
            
            $existingMetrics[] = [
                'timestamp' => $data['timestamp'],
                'execution_time' => $data['execution_time_ms'],
                'memory_used' => $data['memory_used_mb'],
                'query_count' => $data['database_queries'],
                'url' => $data['url'],
                'method' => $data['method'],
                'status_code' => $data['status_code']
            ];
            
            // Keep only last 1000 entries per hour
            if (count($existingMetrics) > 1000) {
                $existingMetrics = array_slice($existingMetrics, -1000);
            }
            
            Cache::put($cacheKey, $existingMetrics, 3600); // 1 hour

        } catch (\Exception $e) {
            Log::channel('performance')->error('Failed to store performance metrics', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
