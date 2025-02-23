<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class EventServiceProvider extends ServiceProvider
{    
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Authentication Events
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        
        // Booking System Events - Clean Code: Event-Driven Architecture
        \App\Events\BookingCreated::class => [
            \App\Listeners\SendBookingConfirmation::class,
        ],
        
        \App\Events\BookingStatusUpdated::class => [
            // Add listeners here for booking status changes
        ],
    ];

    /**
     * Register any events for your application with comprehensive logging.
     */
    public function boot(): void
    {
        $startTime = microtime(true);
        
        Log::info('EventServiceProvider: Starting event system registration', [
            'provider' => self::class,
            'events_count' => count($this->listen),
            'environment' => app()->environment(),
            'timestamp' => now()
        ]);

        try {
            // Register event listeners with logging
            $this->registerEventListeners();

            // Register application-wide event subscribers
            $this->registerEventSubscribers();

            // Register development event listeners
            if (app()->environment('local', 'development')) {
                $this->registerDevelopmentEventListeners();
            }

            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);

            Log::info('EventServiceProvider: Event system registered successfully', [
                'registered_events' => array_keys($this->listen),
                'processing_time_ms' => $processingTime,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]);

        } catch (\Exception $e) {
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2);

            Log::error('EventServiceProvider: Failed to register event system', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => $processingTime
            ]);

            throw $e;
        }
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        Log::info('EventServiceProvider: Event discovery configuration', [
            'auto_discovery' => false,
            'manual_registration' => true
        ]);
        
        return false;
    }

    /**
     * Register event listeners with enhanced monitoring
     */
    private function registerEventListeners(): void
    {
        Log::info('EventServiceProvider: Registering event listeners');

        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
                
                Log::debug('EventServiceProvider: Event listener registered', [
                    'event' => $event,
                    'listener' => $listener
                ]);
            }
        }

        Log::info('EventServiceProvider: Event listeners registered', [
            'events_count' => count($this->listen),
            'total_listeners' => array_sum(array_map('count', $this->listen))
        ]);
    }

    /**
     * Register application-wide event subscribers
     */
    private function registerEventSubscribers(): void
    {
        Log::info('EventServiceProvider: Registering event subscribers');

        // Register global event listeners for system monitoring
        Event::listen('*', function ($eventName, array $data) {
            // Only log important events to avoid spam
            if ($this->shouldLogEvent($eventName)) {
                Log::info('EventServiceProvider: Event fired', [
                    'event' => $eventName,
                    'data_count' => count($data),
                    'timestamp' => now()
                ]);
            }
        });

        Log::info('EventServiceProvider: Event subscribers registered');
    }

    /**
     * Register development-specific event listeners
     */
    private function registerDevelopmentEventListeners(): void
    {
        Log::info('EventServiceProvider: Registering development event listeners');

        // Register listeners for debugging in development
        Event::listen(\App\Events\BookingCreated::class, function ($event) {
            Log::debug('EventServiceProvider: BookingCreated event fired', [
                'booking_id' => $event->booking->id ?? 'unknown',
                'user_id' => $event->booking->user_id ?? 'unknown',
                'event_class' => get_class($event)
            ]);
        });

        Event::listen(\App\Events\BookingStatusUpdated::class, function ($event) {
            Log::debug('EventServiceProvider: BookingStatusUpdated event fired', [
                'booking_id' => $event->booking->id ?? 'unknown',
                'old_status' => $event->oldStatus ?? 'unknown',
                'new_status' => $event->newStatus ?? 'unknown',
                'event_class' => get_class($event)
            ]);
        });

        Log::info('EventServiceProvider: Development event listeners registered');
    }

    /**
     * Determine if an event should be logged
     */
    private function shouldLogEvent(string $eventName): bool
    {
        // List of events to log
        $importantEvents = [
            'App\Events\BookingCreated',
            'App\Events\BookingStatusUpdated',
            'Illuminate\Auth\Events\Login',
            'Illuminate\Auth\Events\Logout',
            'Illuminate\Auth\Events\Registered',
            'Illuminate\Auth\Events\Failed',
        ];

        return in_array($eventName, $importantEvents);
    }
}
