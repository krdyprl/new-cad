<?php

namespace App\Models;

use App\Enums\InformationStatus;
use App\Enums\InformationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class Information extends Model
{
    use HasFactory;

    /**
     * Table name for the model
     */
    protected $table = 'information';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'slug',
        'image',
        'description',
        'content',
        'type',
        'status',
        'author_id',
        'published_at'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'type' => InformationType::class,
        'status' => InformationStatus::class,
    ];

    /**
     * Author relationship
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Scope for published information
     */
    public function scopePublished($query)
    {
        return $query->where('status', InformationStatus::PUBLISHED->value);
    }

    /**
     * Scope for draft information
     */
    public function scopeDraft($query)
    {
        return $query->where('status', InformationStatus::DRAFT->value);
    }

    /**
     * Scope by type
     */
    public function scopeByType($query, InformationType $type)
    {
        return $query->where('type', $type->value);
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($information) {
            try {
                // Auto-generate slug if not provided
                if (empty($information->slug)) {
                    $information->slug = Str::slug($information->title);
                }
                
                // Set default author if not provided
                if (empty($information->author_id)) {
                    $information->author_id = auth()->id() ?? 1;
                }

                // Set default status if not provided
                if (empty($information->status)) {
                    $information->status = InformationStatus::DRAFT;
                }

                // Set default type if not provided
                if (empty($information->type)) {
                    $information->type = InformationType::INFORMATION;
                }

                Log::info('Information being created', [
                    'title' => $information->title,
                    'slug' => $information->slug,
                    'type' => $information->type?->value,
                    'status' => $information->status?->value,
                    'author_id' => $information->author_id
                ]);
                
            } catch (\Exception $e) {
                Log::error('Error in Information creating event', [
                    'title' => $information->title ?? 'Unknown',
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        });

        static::created(function ($information) {
            Log::info('Information created successfully', [
                'id' => $information->id,
                'title' => $information->title,
                'slug' => $information->slug,
                'author_id' => $information->author_id
            ]);
        });

        static::updating(function ($information) {
            try {
                $originalStatus = $information->getOriginal('status');
                $newStatus = $information->status;

                if ($originalStatus !== $newStatus) {
                    Log::info('Information status changing', [
                        'id' => $information->id,
                        'title' => $information->title,
                        'old_status' => $originalStatus,
                        'new_status' => $newStatus?->value,
                        'updated_by' => auth()->id()
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error in Information updating event', [
                    'id' => $information->id,
                    'error' => $e->getMessage()
                ]);
            }
        });
    }

    /**
     * Get excerpt from content
     */
    public function getExcerptAttribute($limit = 150)
    {
        try {
            return Str::limit(strip_tags($this->content), $limit);
        } catch (\Exception $e) {
            Log::warning('Failed to generate excerpt', [
                'information_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return '';
        }
    }

    /**
     * Get information status
     */
    public function getStatus(): InformationStatus
    {
        return $this->status ?? InformationStatus::DRAFT;
    }

    /**
     * Get information type
     */
    public function getType(): InformationType
    {
        return $this->type ?? InformationType::INFORMATION;
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        try {
            return $this->getStatus()->label();
        } catch (\Exception $e) {
            Log::warning('Information status label retrieval failed', [
                'information_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return 'Unknown';
        }
    }

    /**
     * Get type label
     */
    public function getTypeLabel(): string
    {
        try {
            return $this->getType()->label();
        } catch (\Exception $e) {
            Log::warning('Information type label retrieval failed', [
                'information_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return 'Unknown';
        }
    }

    /**
     * Check if information is published
     */
    public function isPublished(): bool
    {
        return $this->getStatus() === InformationStatus::PUBLISHED;
    }

    /**
     * Publish information
     */
    public function publish(?User $publishedBy = null): bool
    {
        try {
            $this->status = InformationStatus::PUBLISHED;
            $this->published_at = now();
            $result = $this->save();

            if ($result) {
                Log::info('Information published', [
                    'information_id' => $this->id,
                    'title' => $this->title,
                    'published_by' => $publishedBy?->id,
                    'published_at' => $this->published_at
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to publish information', [
                'information_id' => $this->id,
                'title' => $this->title,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get image URL
     */
    public function getImageUrl(): ?string
    {
        if (!$this->image) {
            return null;
        }

        // If image is already a full URL, return as is
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        // If image starts with storage/, it's already the correct path
        if (str_starts_with($this->image, 'storage/')) {
            // Build URL manually to ensure it works in all contexts
            $baseUrl = config('app.url') ?: 'http://localhost:8000';
            return $baseUrl . '/' . $this->image;
        }

        // If image exists in public path, return asset URL
        if (file_exists(public_path($this->image))) {
            $baseUrl = config('app.url') ?: 'http://localhost:8000';
            return $baseUrl . '/' . $this->image;
        }

        return null;
    }

    /**
     * Check if information has valid image
     */
    public function hasValidImage(): bool
    {
        return $this->getImageUrl() !== null;
    }

    /**
     * Log information activity
     */
    public function logActivity(string $action, array $context = []): void
    {
        Log::info('Information activity', [
            'information_id' => $this->id,
            'title' => $this->title,
            'type' => $this->getType()->value,
            'status' => $this->getStatus()->value,
            'author_id' => $this->author_id,
            'action' => $action,
            'context' => $context,
            'timestamp' => now(),
            'user_id' => auth()->id(),
            'ip_address' => request()->ip()
        ]);
    }
}
