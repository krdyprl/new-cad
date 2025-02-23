<?php

namespace App\Providers;

use App\Models\Booking;
use App\Policies\BookingPolicyClean;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Booking::class => BookingPolicyClean::class,
    ];

    /**
     * Register any authentication / authorization services with comprehensive logging.
     */
    public function boot(): void
    {
        $startTime = microtime(true);
        
        Log::info('AuthServiceProvider: Starting authentication/authorization setup', [
            'provider' => self::class,
            'policies_count' => count($this->policies),
            'environment' => app()->environment(),
            'timestamp' => now()
        ]);

        try {
            // Register policies with logging
            $this->registerPolicies();

            // Define custom gates
            $this->defineCustomGates();

            // Register authentication event listeners
            $this->registerAuthEventListeners();

            // Register authorization logging
            $this->registerAuthorizationLogging();

            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);

            Log::info('AuthServiceProvider: Authentication/authorization setup completed', [
                'registered_policies' => array_keys($this->policies),
                'processing_time_ms' => $processingTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);

            Log::error('AuthServiceProvider: Failed to setup authentication/authorization', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Define custom authorization gates
     */
    private function defineCustomGates(): void
    {
        Log::info('AuthServiceProvider: Defining custom gates');

        // Admin access gate
        Gate::define('admin-access', function ($user) {
            $hasAccess = $user && ($user->role === 'admin' || 
                                 (method_exists($user, 'isAdmin') && $user->isAdmin()));
            
            Log::info('AuthServiceProvider: Admin access gate checked', [
                'user_id' => $user?->id,
                'user_role' => $user?->role,
                'access_granted' => $hasAccess,
                'gate' => 'admin-access'
            ]);

            return $hasAccess;
        });

        // User management gate
        Gate::define('manage-users', function ($user) {
            $hasAccess = $user && ($user->role === 'admin' || 
                                 (method_exists($user, 'isAdmin') && $user->isAdmin()));
            
            Log::info('AuthServiceProvider: User management gate checked', [
                'user_id' => $user?->id,
                'access_granted' => $hasAccess,
                'gate' => 'manage-users'
            ]);

            return $hasAccess;
        });

        // Booking management gate
        Gate::define('manage-bookings', function ($user) {
            $hasAccess = $user && ($user->role === 'admin' || 
                                 (method_exists($user, 'isAdmin') && $user->isAdmin()));
            
            Log::info('AuthServiceProvider: Booking management gate checked', [
                'user_id' => $user?->id,
                'access_granted' => $hasAccess,
                'gate' => 'manage-bookings'
            ]);

            return $hasAccess;
        });

        Log::info('AuthServiceProvider: Custom gates defined');
    }

    /**
     * Register authentication event listeners
     */
    private function registerAuthEventListeners(): void
    {
        Log::info('AuthServiceProvider: Registering authentication event listeners');

        // Listen for successful login attempts
        Gate::after(function ($user, $ability, $result, $arguments) {
            Log::info('AuthServiceProvider: Gate authorization checked', [
                'user_id' => $user?->id,
                'ability' => $ability,
                'result' => $result ? 'granted' : 'denied',
                'arguments_count' => count($arguments ?? []),
                'timestamp' => now()
            ]);
        });

        Log::info('AuthServiceProvider: Authentication event listeners registered');
    }

    /**
     * Register authorization logging for security audit
     */
    private function registerAuthorizationLogging(): void
    {
        Log::info('AuthServiceProvider: Registering authorization logging');

        // Log policy authorization attempts
        foreach ($this->policies as $model => $policy) {
            Log::debug('AuthServiceProvider: Policy registered', [
                'model' => $model,
                'policy' => $policy
            ]);
        }

        // Log when authorization is denied
        Gate::before(function ($user, $ability, $arguments) {
            Log::debug('AuthServiceProvider: Authorization attempt', [
                'user_id' => $user?->id,
                'ability' => $ability,
                'arguments_count' => count($arguments ?? []),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()
            ]);
        });

        Log::info('AuthServiceProvider: Authorization logging registered');
    }
}
