<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\Paginator;

// Services
use App\Services\DashboardService;
use App\Services\BookingService;
use App\Services\InformationService;
use App\Services\UserService;
use App\Services\ReportService;
use App\Services\PDFService;

// Repositories
use App\Repositories\BookingRepository;

// Contracts
use App\Contracts\BookingRepositoryInterface;
use App\Contracts\DashboardServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the singleton services that should be registered.
     *
     * @var array
     */
    public $singletons = [
        DashboardService::class => DashboardService::class,
        BookingService::class => BookingService::class,
        InformationService::class => InformationService::class,
        UserService::class => UserService::class,
        ReportService::class => ReportService::class,
        PDFService::class => PDFService::class,
        BookingRepository::class => BookingRepository::class,
    ];

    /**
     * Register any application services with comprehensive logging and monitoring.
     */
    public function register(): void
    {
        $startTime = microtime(true);
        
        Log::info('AppServiceProvider: Starting application service registration', [
            'provider' => self::class,
            'environment' => app()->environment(),
            'singletons_count' => count($this->singletons),
            'timestamp' => now()
        ]);

        try {
            // Register Services as Singletons with enhanced logging
            $this->registerSingletonServices();

            // Register additional interface bindings
            $this->registerInterfaceBindings();

            // Register conditional service bindings based on environment
            $this->registerConditionalServices();

            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);

            Log::info('AppServiceProvider: Application services registered successfully', [
                'registered_singletons' => array_keys($this->singletons),
                'processing_time_ms' => $processingTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);

            Log::error('AppServiceProvider: Failed to register application services', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Bootstrap any application services with enhanced configuration and monitoring.
     */
    public function boot(): void
    {
        $startTime = microtime(true);
        
        Log::info('AppServiceProvider: Starting application bootstrap', [
            'provider' => self::class,
            'environment' => app()->environment(),
            'timestamp' => now()
        ]);

        try {
            // Configure database schema defaults
            $this->configureDatabase();

            // Configure view systems and global data
            $this->configureViews();

            // Configure pagination
            $this->configurePagination();

            // Configure localization
            $this->configureLocalization();

            // Configure performance monitoring
            $this->configurePerformanceMonitoring();

            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);

            Log::info('AppServiceProvider: Application bootstrap completed', [
                'processing_time_ms' => $processingTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);

            Log::error('AppServiceProvider: Application bootstrap failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Register singleton services with dependency resolution logging
     */
    private function registerSingletonServices(): void
    {
        Log::info('AppServiceProvider: Registering singleton services');

        foreach ($this->singletons as $abstract => $concrete) {
            $this->app->singleton($abstract, function ($app) use ($abstract, $concrete) {
                Log::debug('AppServiceProvider: Resolving singleton service', [
                    'service' => $abstract,
                    'implementation' => $concrete,
                    'memory_before_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
                ]);

                return new $concrete();
            });
        }

        Log::info('AppServiceProvider: Singleton services registered', [
            'count' => count($this->singletons)
        ]);
    }

    /**
     * Register interface bindings with validation
     */
    private function registerInterfaceBindings(): void
    {
        Log::info('AppServiceProvider: Registering interface bindings');

        // Bind interfaces to implementations
        $this->app->bind(BookingRepositoryInterface::class, BookingRepository::class);

        Log::info('AppServiceProvider: Interface bindings registered');
    }

    /**
     * Register conditional services based on environment
     */
    private function registerConditionalServices(): void
    {
        Log::info('AppServiceProvider: Registering conditional services', [
            'environment' => app()->environment()
        ]);

        // Register debug services in development
        if (app()->environment('local', 'development')) {
            Log::info('AppServiceProvider: Registering development services');
            // Add development-specific services here
        }

        // Register production optimizations
        if (app()->environment('production')) {
            Log::info('AppServiceProvider: Registering production optimizations');
            // Add production-specific services here
        }
    }

    /**
     * Configure database schema settings
     */
    private function configureDatabase(): void
    {
        Log::debug('AppServiceProvider: Configuring database schema');
        
        // Set default string length for older MySQL versions
        Schema::defaultStringLength(191);
        
        Log::debug('AppServiceProvider: Database schema configured');
    }

    /**
     * Configure view system and global data sharing
     */
    private function configureViews(): void
    {
        Log::debug('AppServiceProvider: Configuring view system');

        // Share global data with all views
        View::composer('*', function ($view) {
            $globalData = $this->getGlobalViewData();
            
            Log::debug('AppServiceProvider: Sharing global view data', [
                'data_keys' => array_keys($globalData),
                'locale' => $globalData['currentLocale'] ?? 'unknown'
            ]);

            $view->with($globalData);
        });

        Log::debug('AppServiceProvider: View system configured');
    }

    /**
     * Configure pagination settings
     */
    private function configurePagination(): void
    {
        Log::debug('AppServiceProvider: Configuring pagination');
        
        // Use Bootstrap for pagination
        Paginator::useBootstrap();
        
        Log::debug('AppServiceProvider: Pagination configured with Bootstrap');
    }

    /**
     * Configure localization settings
     */
    private function configureLocalization(): void
    {
        Log::debug('AppServiceProvider: Configuring localization');

        // Handle locale switching in boot
        if (session('locale')) {
            $locale = session('locale');
            app()->setLocale($locale);
            
            Log::info('AppServiceProvider: Locale set from session', [
                'locale' => $locale
            ]);
        }

        Log::debug('AppServiceProvider: Localization configured');
    }

    /**
     * Configure performance monitoring
     */
    private function configurePerformanceMonitoring(): void
    {
        if (app()->environment('production')) {
            Log::debug('AppServiceProvider: Configuring performance monitoring');
            
            // Log application boot completion
            app()->booted(function () {
                Log::info('AppServiceProvider: Application fully booted', [
                    'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
                    'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2)
                ]);
            });
        }
    }

    /**
     * Get global data to share with all views
     */
    private function getGlobalViewData(): array
    {
        try {
            // Use caching for global view data
            return Cache::remember('global_view_data', 300, function () {
                return [
                    'currentLocale' => app()->getLocale(),
                    'availableLocales' => ['en' => 'English', 'id' => 'Indonesia'],
                    'appName' => config('app.name'),
                    'appVersion' => config('app.version', '1.0.0'),
                ];
            });
        } catch (\Exception $e) {
            Log::error('AppServiceProvider: Failed to get global view data', [
                'error' => $e->getMessage()
            ]);

            // Return fallback data
            return [
                'currentLocale' => 'en',
                'availableLocales' => ['en' => 'English', 'id' => 'Indonesia'],
                'appName' => 'Laravel App',
                'appVersion' => '1.0.0',
            ];
        }
    }
}
