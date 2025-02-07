<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_admin', // Using is_admin field for admin check
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean', // Cast is_admin to boolean
    ];

    /**
     * Get user's role
     */
    public function getRole(): UserRole
    {
        return $this->is_admin ? UserRole::ADMIN : UserRole::USER;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        try {
            return (bool) $this->is_admin;
        } catch (\Exception $e) {
            Log::warning('User role check failed', [
                'user_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(UserRole $role): bool
    {
        try {
            return $this->getRole() === $role;
        } catch (\Exception $e) {
            Log::warning('User role verification failed', [
                'user_id' => $this->id,
                'target_role' => $role->value,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get user's role label
     */
    public function getRoleLabel(): string
    {
        try {
            return $this->getRole()->label();
        } catch (\Exception $e) {
            Log::warning('User role label retrieval failed', [
                'user_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return 'Unknown';
        }
    }

    /**
     * User's bookings relationship
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Log user activity for audit purposes
     */
    public function logActivity(string $action, array $context = []): void
    {
        Log::info('User activity', [
            'user_id' => $this->id,
            'user_email' => $this->email,
            'user_role' => $this->is_admin ? 'admin' : 'user',
            'action' => $action,
            'context' => $context,
            'timestamp' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}
