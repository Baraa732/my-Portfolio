<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ImageHelper
{
    /**
     * Get the full URL for a stored image
     */
    public static function getImageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        self::ensureStorageLink();
        return Storage::disk('public')->url($path);
    }

    /**
     * Ensure storage link exists
     */
    public static function ensureStorageLink(): void
    {
        $link = public_path('storage');
        $target = storage_path('app/public');

        if (!File::exists($link)) {
            try {
                if (windows_os()) {
                    exec("mklink /J \"$link\" \"$target\"");
                } else {
                    File::link($target, $link);
                }
            } catch (\Exception $e) {
                \Log::warning('Could not create storage link: ' . $e->getMessage());
            }
        }
    }

    /**
     * Check if image exists
     */
    public static function imageExists(?string $path): bool
    {
        if (!$path) {
            return false;
        }
        return Storage::disk('public')->exists($path);
    }

    /**
     * Delete image safely
     */
    public static function deleteImage(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        try {
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->delete($path);
            }
        } catch (\Exception $e) {
            \Log::error('Error deleting image: ' . $e->getMessage());
        }
        return false;
    }
}
