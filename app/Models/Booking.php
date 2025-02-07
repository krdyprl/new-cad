<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'user_id',
        'package',
        'package_name',
        'full_name',
        'email',
        'phone',
        'participants',
        'visit_date',
        'visit_time',
        'notes',
        'price_per_unit',
        'subtotal',
        'tax',
        'total',
        'pdf_file',
        'status'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'visit_date' => 'date',
        'price_per_unit' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'status' => BookingStatus::class, // Cast status to enum
    ];

    /**
     * User relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get PDF URL attribute
     */
    public function getPdfUrlAttribute()
    {
        try {
            if ($this->pdf_file) {
                return asset('storage/invoices/' . $this->pdf_file);
            }
            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get PDF URL', [
                'booking_id' => $this->id,
                'pdf_file' => $this->pdf_file,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get booking status
     */
    public function getStatus(): BookingStatus
    {
        return $this->status ?? BookingStatus::PENDING;
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        try {
            return $this->getStatus()->label();
        } catch (\Exception $e) {
            Log::warning('Booking status label retrieval failed', [
                'booking_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return 'Unknown';
        }
    }

    /**
     * Update booking status with logging
     */
    public function updateStatus(BookingStatus $newStatus, ?User $updatedBy = null): bool
    {
        try {
            $oldStatus = $this->getStatus();
            $this->status = $newStatus;
            $result = $this->save();

            if ($result) {
                Log::info('Booking status updated', [
                    'booking_id' => $this->id,
                    'booking_code' => $this->booking_id,
                    'old_status' => $oldStatus->value,
                    'new_status' => $newStatus->value,
                    'updated_by' => $updatedBy?->id,
                    'customer_email' => $this->email,
                    'timestamp' => now()
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to update booking status', [
                'booking_id' => $this->id,
                'new_status' => $newStatus->value,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Log booking activity
     */
    public function logActivity(string $action, array $context = []): void
    {
        Log::info('Booking activity', [
            'booking_id' => $this->id,
            'booking_code' => $this->booking_id,
            'user_id' => $this->user_id,
            'customer_email' => $this->email,
            'status' => $this->getStatus()->value,
            'action' => $action,
            'context' => $context,
            'timestamp' => now(),
            'ip_address' => request()->ip()
        ]);
    }
}
