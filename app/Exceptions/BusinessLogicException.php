<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

/**
 * Custom Business Logic Exception
 * 
 * For handling business rule violations
 */
class BusinessLogicException extends Exception
{
    protected $code = Response::HTTP_UNPROCESSABLE_ENTITY;

    public static function bookingNotFound(): self
    {
        return new self('Booking tidak ditemukan.', Response::HTTP_NOT_FOUND);
    }

    public static function bookingCannotBeModified(): self
    {
        return new self('Booking ini tidak dapat dimodifikasi.', Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function informationNotFound(): self
    {
        return new self('Informasi tidak ditemukan.', Response::HTTP_NOT_FOUND);
    }

    public static function userNotFound(): self
    {
        return new self('User tidak ditemukan.', Response::HTTP_NOT_FOUND);
    }

    public static function unauthorizedAccess(): self
    {
        return new self('Akses tidak diizinkan.', Response::HTTP_FORBIDDEN);
    }

    public static function invalidStatus(string $status): self
    {
        return new self("Status '{$status}' tidak valid.", Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function imageUploadFailed(): self
    {
        return new self('Gagal mengupload gambar.', Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
