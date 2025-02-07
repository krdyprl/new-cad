<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'price',
        'description',
        'specifications',
        'image',
        'ecommerce_link',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

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

        // For product images stored in storage/app/public/products/
        // Convert to public storage URL
        $storagePath = 'storage/' . $this->image;
        $baseUrl = config('app.url') ?: 'http://localhost:8000';
        return $baseUrl . '/' . $storagePath;
    }

    /**
     * Check if product has valid image
     */
    public function hasValidImage(): bool
    {
        return $this->getImageUrl() !== null;
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Scope for active products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
