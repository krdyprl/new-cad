<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Services\PerformanceAnalyticsService;

/**
 * System Health Check Command
 * 
 * Comprehensive system health monitoring including database,
 * cache, storage, performance metrics, and security checks
 */
class SystemHealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'system:health-check 
                           {--detailed : Show detailed health information}
                           {--export= : Export health report to file}';

    /**
     * The console command description.
     */
    protected $description = 'Perform comprehensive system health check with performance analysis';

    /**
     * Performance Analytics Service
     */
    private PerformanceAnalyticsService $performanceService;

    /**
     * Create a new command instance.
     */
    public function __construct(PerformanceAnalyticsService $performanceService)
    {
        parent::__construct();
        $this->performanceService = $performanceService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $startTime = microtime(true);
        
        $this->info('🔍 Starting System Health Check...');
        $this->newLine();

        Log::info('SystemHealthCheck: Starting comprehensive health check', [
            'command' => 'system:health-check',
            'detailed' => $this->option('detailed'),
            'export' => $this->option('export'),
            'timestamp' => now()
        ]);

        try {
            $healthReport = [
                'timestamp' => now()->toISOString(),
                'environment' => app()->environment(),
                'checks' => []
            ];

            // Perform health checks
            $healthReport['checks']['database'] = $this->checkDatabase();
            $healthReport['checks']['cache'] = $this->checkCache();
            $healthReport['checks']['storage'] = $this->checkStorage();
            $healthReport['checks']['performance'] = $this->checkPerformance();
            $healthReport['checks']['security'] = $this->checkSecurity();
            $healthReport['checks']['dependencies'] = $this->checkDependencies();

            // Calculate overall health status
            $healthReport['overall_status'] = $this->calculateOverallHealth($healthReport['checks']);
            
            // Display results
            $this->displayHealthReport($healthReport);

            // Export report if requested
            if ($this->option('export')) {
                $this->exportHealthReport($healthReport);
            }

            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);

            Log::info('SystemHealthCheck: Health check completed', [
                'overall_status' => $healthReport['overall_status'],
                'processing_time_ms' => $processingTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

            $this->newLine();
            $this->info("✅ Health check completed in {$processingTime}ms");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);

            Log::error('SystemHealthCheck: Health check failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            $this->error('❌ Health check failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Check database connectivity and performance
     */
    private function checkDatabase(): array
    {
        $this->info('🗄️  Checking Database...');
        
        try {
            $startTime = microtime(true);
            
            // Test connection
            DB::connection()->getPdo();
            
            // Test query performance
            $queryStartTime = microtime(true);
            $userCount = DB::table('users')->count();
            $bookingCount = DB::table('bookings')->count();
            $informationCount = DB::table('information')->count();
            $queryEndTime = microtime(true);
            
            $connectionTime = round(($queryEndTime - $startTime) * 1000, 2);
            $queryTime = round(($queryEndTime - $queryStartTime) * 1000, 2);

            $status = [
                'status' => 'healthy',
                'connection_time_ms' => $connectionTime,
                'query_time_ms' => $queryTime,
                'records' => [
                    'users' => $userCount,
                    'bookings' => $bookingCount,
                    'information' => $informationCount
                ]
            ];

            if ($connectionTime > 1000) {
                $status['status'] = 'warning';
                $status['message'] = 'Slow database connection';
            }

            $this->line("   ✅ Database: {$status['status']} ({$connectionTime}ms)");
            return $status;

        } catch (\Exception $e) {
            $this->line("   ❌ Database: failed - {$e->getMessage()}");
            return [
                'status' => 'failed',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check cache system
     */
    private function checkCache(): array
    {
        $this->info('🚀 Checking Cache System...');
        
        try {
            $startTime = microtime(true);
            
            // Test cache write/read
            $testKey = 'health_check_' . time();
            $testValue = 'health_check_value';
            
            Cache::put($testKey, $testValue, 60);
            $retrievedValue = Cache::get($testKey);
            Cache::forget($testKey);
            
            $endTime = microtime(true);
            $operationTime = round(($endTime - $startTime) * 1000, 2);

            $status = [
                'status' => $retrievedValue === $testValue ? 'healthy' : 'failed',
                'operation_time_ms' => $operationTime,
                'driver' => config('cache.default')
            ];

            if ($operationTime > 100) {
                $status['status'] = 'warning';
                $status['message'] = 'Slow cache operations';
            }

            $this->line("   ✅ Cache: {$status['status']} ({$operationTime}ms)");
            return $status;

        } catch (\Exception $e) {
            $this->line("   ❌ Cache: failed - {$e->getMessage()}");
            return [
                'status' => 'failed',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check storage system
     */
    private function checkStorage(): array
    {
        $this->info('💾 Checking Storage System...');
        
        try {
            $startTime = microtime(true);
            
            // Test file operations
            $testFile = 'health_check_' . time() . '.txt';
            $testContent = 'Health check test content';
            
            Storage::disk('local')->put($testFile, $testContent);
            $retrievedContent = Storage::disk('local')->get($testFile);
            Storage::disk('local')->delete($testFile);
            
            $endTime = microtime(true);
            $operationTime = round(($endTime - $startTime) * 1000, 2);

            // Check disk space
            $diskSpace = disk_free_space(storage_path());
            $diskSpaceGB = round($diskSpace / 1024 / 1024 / 1024, 2);

            $status = [
                'status' => $retrievedContent === $testContent ? 'healthy' : 'failed',
                'operation_time_ms' => $operationTime,
                'free_space_gb' => $diskSpaceGB,
                'driver' => config('filesystems.default')
            ];

            if ($diskSpaceGB < 1) {
                $status['status'] = 'warning';
                $status['message'] = 'Low disk space';
            }

            $this->line("   ✅ Storage: {$status['status']} ({$diskSpaceGB}GB free)");
            return $status;

        } catch (\Exception $e) {
            $this->line("   ❌ Storage: failed - {$e->getMessage()}");
            return [
                'status' => 'failed',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check performance metrics
     */
    private function checkPerformance(): array
    {
        $this->info('⚡ Checking Performance Metrics...');
        
        try {
            $performanceData = $this->performanceService->getPerformanceOverview();
            
            $status = [
                'status' => 'healthy',
                'metrics' => $performanceData
            ];

            // Evaluate performance status
            if ($performanceData['avg_response_time'] > 2000) {
                $status['status'] = 'warning';
                $status['message'] = 'High average response time';
            }

            if ($performanceData['total_requests'] === 0) {
                $status['status'] = 'info';
                $status['message'] = 'No recent performance data';
            }

            $this->line("   ✅ Performance: {$status['status']} (avg: {$performanceData['avg_response_time']}ms)");
            return $status;

        } catch (\Exception $e) {
            $this->line("   ❌ Performance: failed - {$e->getMessage()}");
            return [
                'status' => 'failed',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check security configurations
     */
    private function checkSecurity(): array
    {
        $this->info('🔐 Checking Security Configuration...');
        
        $checks = [];
        $overallStatus = 'healthy';

        // Check APP_DEBUG in production
        if (app()->environment('production') && config('app.debug')) {
            $checks['debug_mode'] = 'warning - Debug mode enabled in production';
            $overallStatus = 'warning';
        } else {
            $checks['debug_mode'] = 'ok';
        }

        // Check APP_KEY
        if (empty(config('app.key'))) {
            $checks['app_key'] = 'failed - APP_KEY not set';
            $overallStatus = 'failed';
        } else {
            $checks['app_key'] = 'ok';
        }

        // Check HTTPS in production
        if (app()->environment('production') && !request()->isSecure()) {
            $checks['https'] = 'warning - HTTPS not enforced';
            if ($overallStatus !== 'failed') $overallStatus = 'warning';
        } else {
            $checks['https'] = 'ok';
        }

        $this->line("   ✅ Security: {$overallStatus}");
        
        return [
            'status' => $overallStatus,
            'checks' => $checks
        ];
    }

    /**
     * Check system dependencies
     */
    private function checkDependencies(): array
    {
        $this->info('📦 Checking Dependencies...');
        
        $checks = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'extensions' => []
        ];

        // Check required PHP extensions
        $requiredExtensions = ['pdo', 'mbstring', 'openssl', 'json', 'bcmath', 'ctype', 'fileinfo', 'tokenizer', 'xml'];
        
        foreach ($requiredExtensions as $extension) {
            $checks['extensions'][$extension] = extension_loaded($extension) ? 'ok' : 'missing';
        }

        $missingExtensions = array_filter($checks['extensions'], fn($status) => $status === 'missing');
        $status = empty($missingExtensions) ? 'healthy' : 'failed';

        $this->line("   ✅ Dependencies: {$status}");
        
        return [
            'status' => $status,
            'checks' => $checks
        ];
    }

    /**
     * Calculate overall health status
     */
    private function calculateOverallHealth(array $checks): string
    {
        $statuses = array_column($checks, 'status');
        
        if (in_array('failed', $statuses)) {
            return 'failed';
        }
        
        if (in_array('warning', $statuses)) {
            return 'warning';
        }
        
        return 'healthy';
    }

    /**
     * Display health report
     */
    private function displayHealthReport(array $report): void
    {
        $this->newLine();
        $this->info('📊 Health Report Summary:');
        $this->line('================================');
        
        $statusIcon = match($report['overall_status']) {
            'healthy' => '✅',
            'warning' => '⚠️',
            'failed' => '❌',
            default => '❓'
        };
        
        $this->line("Overall Status: {$statusIcon} " . strtoupper($report['overall_status']));
        $this->line("Environment: " . $report['environment']);
        $this->line("Timestamp: " . $report['timestamp']);

        if ($this->option('detailed')) {
            $this->newLine();
            $this->info('📋 Detailed Results:');
            
            foreach ($report['checks'] as $checkName => $checkData) {
                $this->line("\n{$checkName}:");
                $this->line("  Status: {$checkData['status']}");
                
                if (isset($checkData['error'])) {
                    $this->line("  Error: {$checkData['error']}");
                }
                
                if (isset($checkData['message'])) {
                    $this->line("  Message: {$checkData['message']}");
                }
            }
        }
    }

    /**
     * Export health report to file
     */
    private function exportHealthReport(array $report): void
    {
        $filename = $this->option('export');
        $content = json_encode($report, JSON_PRETTY_PRINT);
        
        try {
            Storage::disk('local')->put($filename, $content);
            $this->info("📄 Health report exported to: storage/app/{$filename}");
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to export health report: {$e->getMessage()}");
        }
    }
}
