<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User;
use App\Models\Booking;
use App\Models\Information;
use App\Enums\UserRole;
use App\Enums\BookingStatus;
use App\Enums\InformationType;
use App\Enums\InformationStatus;
use App\Services\BookingService;
use App\Services\PDFService;
use App\Repositories\BookingRepository;
use App\Repositories\UserRepository;

/**
 * Enhanced System Integration Tests
 * 
 * Comprehensive testing for all enhanced features including
 * logging, caching, performance monitoring, and security
 */
class EnhancedSystemTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test user authentication and role management
     */
    public function test_user_authentication_and_roles(): void
    {
        // Create admin user
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
            'email' => 'admin@test.com'
        ]);

        // Create regular user
        $user = User::factory()->create([
            'role' => UserRole::USER->value,
            'email' => 'user@test.com'
        ]);

        // Test admin access
        $this->actingAs($admin)
             ->get('/admin/dashboard')
             ->assertStatus(200);

        // Test user access restriction
        $this->actingAs($user)
             ->get('/admin/dashboard')
             ->assertStatus(403);

        // Test role-based authorization
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($user->isAdmin());
        $this->assertEquals(UserRole::ADMIN, $admin->role);
        $this->assertEquals(UserRole::USER, $user->role);
    }

    /**
     * Test booking system with status management
     */
    public function test_booking_system_functionality(): void
    {
        $user = User::factory()->create();
        
        // Test booking creation
        $bookingData = [
            'package' => 'Premium Package',
            'participants' => 4,
            'booking_date' => '2025-06-15',
            'notes' => 'Test booking'
        ];

        $response = $this->actingAs($user)
                         ->post('/bookings', $bookingData);

        $response->assertStatus(302); // Redirect after creation
        
        $booking = Booking::where('user_id', $user->id)->first();
        $this->assertNotNull($booking);
        $this->assertEquals(BookingStatus::PENDING, $booking->status);
        $this->assertEquals('Premium Package', $booking->package);
        $this->assertEquals(4, $booking->participants);

        // Test booking status update
        $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);
        
        $response = $this->actingAs($admin)
                         ->patch("/bookings/{$booking->id}/status", [
                             'status' => BookingStatus::CONFIRMED->value
                         ]);

        $response->assertStatus(200);
        $booking->refresh();
        $this->assertEquals(BookingStatus::CONFIRMED, $booking->status);
    }

    /**
     * Test repository pattern implementation
     */
    public function test_repository_pattern_functionality(): void
    {
        $user = User::factory()->create();
        $bookings = Booking::factory()->count(3)->create(['user_id' => $user->id]);

        // Test UserRepository
        $userRepository = app(\App\Contracts\UserRepositoryInterface::class);
        
        $foundUser = $userRepository->find($user->id);
        $this->assertEquals($user->id, $foundUser->id);
        
        $userByEmail = $userRepository->findByEmail($user->email);
        $this->assertEquals($user->id, $userByEmail->id);

        // Test BookingRepository
        $bookingRepository = app(\App\Contracts\BookingRepositoryInterface::class);
        
        $userBookings = $bookingRepository->getByUserId($user->id);
        $this->assertCount(3, $userBookings);
        
        $paginatedBookings = $bookingRepository->getPaginated(10);
        $this->assertNotNull($paginatedBookings);
    }

    /**
     * Test service layer functionality
     */
    public function test_service_layer_functionality(): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create(['user_id' => $user->id]);

        // Test BookingService
        $bookingService = app(\App\Services\BookingService::class);
        
        $result = $bookingService->updateStatus($booking, BookingStatus::CONFIRMED->value);
        $this->assertTrue($result);
        
        $booking->refresh();
        $this->assertEquals(BookingStatus::CONFIRMED, $booking->status);

        // Test PDF generation
        $pdfService = app(\App\Services\PDFService::class);
        $pdfPath = $pdfService->generateBookingInvoice($booking);
        $this->assertNotNull($pdfPath);
    }

    /**
     * Test caching functionality
     */
    public function test_caching_functionality(): void
    {
        $users = User::factory()->count(5)->create();
        
        // Test cache miss and hit
        $cacheKey = 'test_users_all';
        
        // First call - cache miss
        $startTime = microtime(true);
        $cachedUsers = cache()->remember($cacheKey, 300, function () {
            return User::all();
        });
        $missTime = microtime(true) - $startTime;
        
        // Second call - cache hit
        $startTime = microtime(true);
        $cachedUsersAgain = cache()->get($cacheKey);
        $hitTime = microtime(true) - $startTime;
        
        $this->assertCount(5, $cachedUsers);
        $this->assertCount(5, $cachedUsersAgain);
        $this->assertLessThan($missTime, $hitTime); // Cache hit should be faster
        
        // Clean up
        cache()->forget($cacheKey);
    }

    /**
     * Test performance monitoring
     */
    public function test_performance_monitoring(): void
    {
        // Enable query logging
        DB::enableQueryLog();
        
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        
        // Perform some operations
        $users = User::factory()->count(10)->create();
        $bookings = Booking::factory()->count(20)->create();
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);
        
        $executionTime = ($endTime - $startTime) * 1000; // ms
        $memoryUsed = ($endMemory - $startMemory) / 1024 / 1024; // MB
        $queryCount = count(DB::getQueryLog());
        
        // Assert reasonable performance
        $this->assertLessThan(1000, $executionTime); // Less than 1 second
        $this->assertLessThan(50, $memoryUsed); // Less than 50MB
        $this->assertLessThan(100, $queryCount); // Less than 100 queries
        
        DB::disableQueryLog();
    }

    /**
     * Test error handling and logging
     */
    public function test_error_handling_and_logging(): void
    {
        // Test invalid user creation
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123' // Too short
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password']);

        // Test unauthorized access
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(302); // Redirect to login

        // Test invalid booking data
        $user = User::factory()->create();
        $response = $this->actingAs($user)
                         ->post('/bookings', [
                             'package' => '',
                             'participants' => 0,
                             'booking_date' => 'invalid-date'
                         ]);

        $response->assertSessionHasErrors(['package', 'participants', 'booking_date']);
    }

    /**
     * Test API endpoints functionality
     */
    public function test_api_endpoints(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);
        $user = User::factory()->create(['role' => UserRole::USER->value]);
        
        // Test user listing API (admin only)
        $response = $this->actingAs($admin, 'api')
                         ->get('/api/users');
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => ['id', 'name', 'email', 'role']
                     ]
                 ]);

        // Test unauthorized API access
        $response = $this->actingAs($user, 'api')
                         ->get('/api/users');
        $response->assertStatus(403);

        // Test booking creation API
        $bookingData = [
            'package' => 'API Test Package',
            'participants' => 2,
            'booking_date' => '2025-07-01'
        ];

        $response = $this->actingAs($user, 'api')
                         ->post('/api/bookings', $bookingData);
        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'data' => ['id', 'booking_id', 'package', 'status']
                 ]);
    }

    /**
     * Test security features
     */
    public function test_security_features(): void
    {
        // Test CSRF protection
        $response = $this->post('/login', [
            'email' => 'test@test.com',
            'password' => 'password'
        ]);
        $response->assertStatus(419); // CSRF token mismatch

        // Test rate limiting (if implemented)
        $user = User::factory()->create();
        
        // Make multiple rapid requests
        for ($i = 0; $i < 5; $i++) {
            $response = $this->actingAs($user)->get('/dashboard');
            $response->assertStatus(200);
        }

        // Test password hashing
        $plainPassword = 'test-password-123';
        $user = User::factory()->create(['password' => bcrypt($plainPassword)]);
        
        $this->assertTrue(Hash::check($plainPassword, $user->password));
        $this->assertFalse(Hash::check('wrong-password', $user->password));
    }

    /**
     * Test system integration
     */
    public function test_full_system_integration(): void
    {
        // Create test data
        $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);
        $user = User::factory()->create(['role' => UserRole::USER->value]);
        
        // User creates booking
        $this->actingAs($user);
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'status' => BookingStatus::PENDING
        ]);

        // Admin manages booking
        $this->actingAs($admin);
        
        // View booking list
        $response = $this->get('/admin/bookings');
        $response->assertStatus(200);
        
        // Update booking status
        $response = $this->patch("/admin/bookings/{$booking->id}", [
            'status' => BookingStatus::CONFIRMED->value
        ]);
        $response->assertStatus(302);
        
        // Generate PDF
        $response = $this->get("/admin/bookings/{$booking->id}/pdf");
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        
        // Verify booking status
        $booking->refresh();
        $this->assertEquals(BookingStatus::CONFIRMED, $booking->status);
    }
}
