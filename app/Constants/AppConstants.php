<?php

namespace App\Constants;

/**
 * Application Constants
 * 
 * Contains all magic strings and constant values used throughout the application
 */
class AppConstants
{
    // User Roles
    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';

    // Booking Statuses
    public const BOOKING_STATUS_PENDING = 'pending';
    public const BOOKING_STATUS_CONFIRMED = 'confirmed';
    public const BOOKING_STATUS_COMPLETED = 'completed';
    public const BOOKING_STATUS_CANCELLED = 'cancelled';

    // Information Statuses
    public const INFO_STATUS_DRAFT = 'draft';
    public const INFO_STATUS_PUBLISHED = 'published';

    // Information Types
    public const INFO_TYPE_NEWS = 'news';
    public const INFO_TYPE_INFORMATION = 'information';

    // Pagination
    public const DEFAULT_PAGINATION_LIMIT = 10;
    public const RECENT_ITEMS_LIMIT = 5;

    // File Upload
    public const MAX_IMAGE_SIZE = 2048; // KB
    public const ALLOWED_IMAGE_TYPES = ['jpeg', 'png', 'jpg', 'gif'];
    public const IMAGE_UPLOAD_PATH = 'img/information';

    // Date Formats
    public const DATE_FORMAT = 'Y-m-d';
    public const DATETIME_FORMAT = 'Y-m-d H:i:s';

    // Available Booking Statuses
    public const BOOKING_STATUSES = [
        self::BOOKING_STATUS_PENDING,
        self::BOOKING_STATUS_CONFIRMED,
        self::BOOKING_STATUS_COMPLETED,
        self::BOOKING_STATUS_CANCELLED,
    ];

    // Available Information Statuses
    public const INFO_STATUSES = [
        self::INFO_STATUS_DRAFT,
        self::INFO_STATUS_PUBLISHED,
    ];

    // Available Information Types
    public const INFO_TYPES = [
        self::INFO_TYPE_NEWS,
        self::INFO_TYPE_INFORMATION,
    ];

    // Status Labels
    public const BOOKING_STATUS_LABELS = [
        self::BOOKING_STATUS_PENDING => 'Menunggu Konfirmasi',
        self::BOOKING_STATUS_CONFIRMED => 'Dikonfirmasi',
        self::BOOKING_STATUS_COMPLETED => 'Selesai',
        self::BOOKING_STATUS_CANCELLED => 'Dibatalkan',
    ];

    public const INFO_STATUS_LABELS = [
        self::INFO_STATUS_DRAFT => 'Draft',
        self::INFO_STATUS_PUBLISHED => 'Dipublikasikan',
    ];

    public const INFO_TYPE_LABELS = [
        self::INFO_TYPE_NEWS => 'Berita',
        self::INFO_TYPE_INFORMATION => 'Informasi',
    ];

    public const ROLE_LABELS = [
        self::ROLE_ADMIN => 'Administrator',
        self::ROLE_USER => 'Pengguna',
    ];
}
