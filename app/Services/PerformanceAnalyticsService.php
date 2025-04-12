<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Performance Analytics Service
 * 
 * Provides comprehensive performance analysis, monitoring,
 * and optimization recommendations for the application
 */
class PerformanceAnalyticsService
{
    /**
     * Get performance summary for dashboard
     */
    public function getPerformanceSummary(): array
    {
        $startTime = microtime(true);
        
        Log::info('PerformanceAnalyticsService: Getting performance summary', [
            'method' => 'getPerformanceSummary',
            'timestamp' => now()
        ]);

        try {
            $summary = [
                'overview' => $this->getPerformanceOverview(),
                'slow_requests' => $this->getSlowRequests(),
                'database_performance' => $this->getDatabasePerformance(),
                'memory_usage' => $this->getMemoryUsageAnalysis(),
                'recommendations' => $this->getOptimizationRecommendations()
            ];

            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);

            Log::info('PerformanceAnalyticsService: Performance summary generated', [
                'summary_sections' => array_keys($summary),
                'processing_time_ms' => $processingTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

            return $summary;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);

            Log::error('PerformanceAnalyticsService: Failed to get performance summary', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Get performance overview metrics
     */
    public function getPerformanceOverview(): array
    {
        $cacheKey = 'performance_overview_' . date('Y-m-d');
        
        return Cache::remember($cacheKey, 3600, function () {
            Log::info('PerformanceAnalyticsService: Generating performance overview (cache miss)');
            
            $currentHour = date('Y-m-d-H');
            $metrics = Cache::get('performance_metrics_' . $currentHour, []);
            
            if (empty($metrics)) {
                return [
                    'avg_response_time' => 0,
                    'total_requests' => 0,
                    'slow_requests' => 0,
                    'avg_memory_usage' => 0,
                    'avg_query_count' => 0
                ];
            }

            $totalRequests = count($metrics);
            $responseTimes = array_column($metrics, 'execution_time');
            $memoryUsages = array_column($metrics, 'memory_used');
            $queryCounts = array_column($metrics, 'query_count');

            return [
                'avg_response_time' => round(array_sum($responseTimes) / $totalRequests, 2),
                'total_requests' => $totalRequests,
                'slow_requests' => count(array_filter($responseTimes, fn($time) => $time > 1000)),
                'avg_memory_usage' => round(array_sum($memoryUsages) / $totalRequests, 2),
                'avg_query_count' => round(array_sum($queryCounts) / $totalRequests, 1),
                'min_response_time' => min($responseTimes),
                'max_response_time' => max($responseTimes),
                'percentile_95' => $this->calculatePercentile($responseTimes, 95)
            ];
        });
    }

    /**
     * Get slow requests analysis
     */
    public function getSlowRequests(int $limit = 10): array
    {
        $currentHour = date('Y-m-d-H');
        $metrics = Cache::get('performance_metrics_' . $currentHour, []);
        
        // Filter slow requests (> 1000ms)
        $slowRequests = array_filter($metrics, fn($metric) => $metric['execution_time'] > 1000);
        
        // Sort by execution time (slowest first)
        usort($slowRequests, fn($a, $b) => $b['execution_time'] <=> $a['execution_time']);
        
        // Take top N slow requests
        $topSlowRequests = array_slice($slowRequests, 0, $limit);
        
        Log::info('PerformanceAnalyticsService: Slow requests analyzed', [
            'total_slow_requests' => count($slowRequests),
            'returned_count' => count($topSlowRequests)
        ]);

        return [
            'slow_requests' => $topSlowRequests,
            'total_slow_count' => count($slowRequests),
            'slow_request_percentage' => count($metrics) > 0 ? 
                round((count($slowRequests) / count($metrics)) * 100, 2) : 0
        ];
    }

    /**
     * Get database performance analysis
     */
    public function getDatabasePerformance(): array
    {
        Log::info('PerformanceAnalyticsService: Analyzing database performance');
        
        try {
            $currentHour = date('Y-m-d-H');
            $metrics = Cache::get('performance_metrics_' . $currentHour, []);
            
            if (empty($metrics)) {
                return [
                    'avg_queries_per_request' => 0,
                    'high_query_requests' => 0,
                    'query_efficiency' => 'No data'
                ];
            }

            $queryCounts = array_column($metrics, 'query_count');
            $totalRequests = count($metrics);
            $highQueryRequests = count(array_filter($queryCounts, fn($count) => $count > 10));

            $avgQueries = array_sum($queryCounts) / $totalRequests;
            $efficiency = $this->getQueryEfficiencyRating($avgQueries);

            return [
                'avg_queries_per_request' => round($avgQueries, 1),
                'high_query_requests' => $highQueryRequests,
                'high_query_percentage' => round(($highQueryRequests / $totalRequests) * 100, 2),
                'query_efficiency' => $efficiency,
                'min_queries' => min($queryCounts),
                'max_queries' => max($queryCounts)
            ];

        } catch (\Exception $e) {
            Log::error('PerformanceAnalyticsService: Database performance analysis failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'avg_queries_per_request' => 0,
                'high_query_requests' => 0,
                'query_efficiency' => 'Error'
            ];
        }
    }

    /**
     * Get memory usage analysis
     */
    public function getMemoryUsageAnalysis(): array
    {
        $currentHour = date('Y-m-d-H');
        $metrics = Cache::get('performance_metrics_' . $currentHour, []);
        
        if (empty($metrics)) {
            return [
                'avg_memory_usage' => 0,
                'high_memory_requests' => 0,
                'memory_efficiency' => 'No data'
            ];
        }

        $memoryUsages = array_column($metrics, 'memory_used');
        $totalRequests = count($metrics);
        $highMemoryRequests = count(array_filter($memoryUsages, fn($usage) => $usage > 20));

        $avgMemory = array_sum($memoryUsages) / $totalRequests;
        $efficiency = $this->getMemoryEfficiencyRating($avgMemory);

        return [
            'avg_memory_usage' => round($avgMemory, 2),
            'high_memory_requests' => $highMemoryRequests,
            'high_memory_percentage' => round(($highMemoryRequests / $totalRequests) * 100, 2),
            'memory_efficiency' => $efficiency,
            'min_memory' => min($memoryUsages),
            'max_memory' => max($memoryUsages)
        ];
    }

    /**
     * Get optimization recommendations
     */
    public function getOptimizationRecommendations(): array
    {
        Log::info('PerformanceAnalyticsService: Generating optimization recommendations');
        
        $overview = $this->getPerformanceOverview();
        $dbPerformance = $this->getDatabasePerformance();
        $memoryAnalysis = $this->getMemoryUsageAnalysis();
        
        $recommendations = [];

        // Response time recommendations
        if ($overview['avg_response_time'] > 1000) {
            $recommendations[] = [
                'type' => 'performance',
                'priority' => 'high',
                'title' => 'High Average Response Time',
                'description' => 'Average response time is ' . $overview['avg_response_time'] . 'ms',
                'suggestion' => 'Consider implementing caching, database query optimization, or code profiling'
            ];
        }

        // Database query recommendations
        if ($dbPerformance['avg_queries_per_request'] > 10) {
            $recommendations[] = [
                'type' => 'database',
                'priority' => 'medium',
                'title' => 'High Database Query Count',
                'description' => 'Average ' . $dbPerformance['avg_queries_per_request'] . ' queries per request',
                'suggestion' => 'Implement eager loading, query optimization, or database indexing'
            ];
        }

        // Memory usage recommendations
        if ($memoryAnalysis['avg_memory_usage'] > 30) {
            $recommendations[] = [
                'type' => 'memory',
                'priority' => 'medium',
                'title' => 'High Memory Usage',
                'description' => 'Average memory usage is ' . $memoryAnalysis['avg_memory_usage'] . 'MB',
                'suggestion' => 'Check for memory leaks, optimize data structures, or implement garbage collection'
            ];
        }

        // Slow request recommendations
        if ($overview['slow_requests'] > ($overview['total_requests'] * 0.1)) {
            $recommendations[] = [
                'type' => 'performance',
                'priority' => 'high',
                'title' => 'High Percentage of Slow Requests',
                'description' => 'More than 10% of requests are slow (>1000ms)',
                'suggestion' => 'Profile slow endpoints and implement targeted optimizations'
            ];
        }

        return $recommendations;
    }

    /**
     * Calculate percentile value
     */
    private function calculatePercentile(array $values, int $percentile): float
    {
        sort($values);
        $index = ($percentile / 100) * (count($values) - 1);
        
        if (floor($index) == $index) {
            return $values[$index];
        }
        
        $lower = $values[floor($index)];
        $upper = $values[ceil($index)];
        $fraction = $index - floor($index);
        
        return $lower + ($fraction * ($upper - $lower));
    }

    /**
     * Get query efficiency rating
     */
    private function getQueryEfficiencyRating(float $avgQueries): string
    {
        if ($avgQueries <= 3) return 'Excellent';
        if ($avgQueries <= 7) return 'Good';
        if ($avgQueries <= 15) return 'Fair';
        return 'Poor';
    }

    /**
     * Get memory efficiency rating
     */
    private function getMemoryEfficiencyRating(float $avgMemory): string
    {
        if ($avgMemory <= 10) return 'Excellent';
        if ($avgMemory <= 25) return 'Good';
        if ($avgMemory <= 50) return 'Fair';
        return 'Poor';
    }
}
