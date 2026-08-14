<?php
declare(strict_types=1);

namespace app\helpers;

use Exception;

class imagehelper
{
    public static function convertandupload(
        array $file, 
        string $destinationFolder, 
        int $maxWidth = 1000, 
        int $quality = 80
    ): string {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("File upload error code: " . ($file['error'] ?? 'Unknown'));
        }

        $tmpPath = $file['tmp_name'];

        if (!file_exists($tmpPath)) {
            throw new Exception("File temporary tidak ditemukan.");
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($extension, $allowedExtensions, true)) {
            throw new Exception("Format gambar tidak didukung. Harap gunakan JPG, PNG, atau WEBP.");
        }

        if (!is_dir($destinationFolder)) {
            @mkdir($destinationFolder, 0755, true);
        }

        // Coba konversi via GD jika tersedia
        if (function_exists('imagewebp')) {
            try {
                $sourceImage = null;
                if ($extension === 'jpg' || $extension === 'jpeg') {
                    $sourceImage = @imagecreatefromjpeg($tmpPath);
                } elseif ($extension === 'png') {
                    $sourceImage = @imagecreatefrompng($tmpPath);
                    if ($sourceImage) {
                        imagepalettetotruecolor($sourceImage);
                        imagealphablending($sourceImage, true);
                        imagesavealpha($sourceImage, true);
                    }
                } elseif ($extension === 'webp') {
                    $sourceImage = @imagecreatefromwebp($tmpPath);
                }

                if ($sourceImage) {
                    $filename = uniqid('prod_', true) . '.webp';
                    $fullPath = rtrim($destinationFolder, '/') . '/' . $filename;
                    
                    @imagewebp($sourceImage, $fullPath, $quality);

                    if (file_exists($fullPath)) {
                        return $filename;
                    }
                }
            } catch (\Throwable $e) {
                // Fallback ke direct upload jika proses GD gagal
            }
        }

        // Direct Fallback Upload jika GD PHP tidak merespons
        $filename = uniqid('prod_', true) . '.' . $extension;
        $fullPath = rtrim($destinationFolder, '/') . '/' . $filename;

        if (!@move_uploaded_file($tmpPath, $fullPath)) {
            throw new Exception("Gagal menyimpan file gambar ke direktori upload.");
        }

        return $filename;
    }
}<?php
declare(strict_types=1);

namespace app\helpers;

use Exception;

class imagehelper
{
    public static function convertandupload(
        array $file, 
        string $destinationFolder, 
        int $maxWidth = 1000, 
        int $quality = 80
    ): string {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("File upload error code: " . ($file['error'] ?? 'Unknown'));
        }

        $tmpPath = $file['tmp_name'];

        if (!file_exists($tmpPath)) {
            throw new Exception("File temporary tidak ditemukan.");
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($extension, $allowedExtensions, true)) {
            throw new Exception("Format gambar tidak didukung. Harap gunakan JPG, PNG, atau WEBP.");
        }

        if (!is_dir($destinationFolder)) {
            @mkdir($destinationFolder, 0755, true);
        }

        // Coba konversi via GD jika tersedia
        if (function_exists('imagewebp')) {
            try {
                $sourceImage = null;
                if ($extension === 'jpg' || $extension === 'jpeg') {
                    $sourceImage = @imagecreatefromjpeg($tmpPath);
                } elseif ($extension === 'png') {
                    $sourceImage = @imagecreatefrompng($tmpPath);
                    if ($sourceImage) {
                        imagepalettetotruecolor($sourceImage);
                        imagealphablending($sourceImage, true);
                        imagesavealpha($sourceImage, true);
                    }
                } elseif ($extension === 'webp') {
                    $sourceImage = @imagecreatefromwebp($tmpPath);
                }

                if ($sourceImage) {
                    $filename = uniqid('prod_', true) . '.webp';
                    $fullPath = rtrim($destinationFolder, '/') . '/' . $filename;
                    
                    @imagewebp($sourceImage, $fullPath, $quality);

                    if (file_exists($fullPath)) {
                        return $filename;
                    }
                }
            } catch (\Throwable $e) {
                // Fallback ke direct upload jika proses GD gagal
            }
        }

        // Direct Fallback Upload jika GD PHP tidak merespons
        $filename = uniqid('prod_', true) . '.' . $extension;
        $fullPath = rtrim($destinationFolder, '/') . '/' . $filename;

        if (!@move_uploaded_file($tmpPath, $fullPath)) {
            throw new Exception("Gagal menyimpan file gambar ke direktori upload.");
        }

        return $filename;
    }
}