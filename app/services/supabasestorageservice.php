<?php
declare(strict_types=1);

namespace app\services;

use RuntimeException;

class supabasestorageservice
{
    private string $supabaseUrl;
    private string $serviceRoleKey;
    private string $bucket;

    public function __construct(?string $supabaseUrl = null, ?string $serviceRoleKey = null, ?string $bucket = null)
    {
        $this->supabaseUrl = rtrim($supabaseUrl ?? (string) self::env('SUPABASE_URL', ''), '/');
        $this->serviceRoleKey = trim($serviceRoleKey ?? (string) self::env('SUPABASE_SERVICE_ROLE_KEY', ''));
        $this->bucket = trim($bucket ?? (string) self::env('SUPABASE_STORAGE_BUCKET', 'bucket-bunga-dakota'));
    }

    /**
     * Helper internal untuk membaca environment variable secara berurutan
     */
    private static function env(string $key, ?string $default = null): ?string
    {
        $val = getenv($key);
        if ($val !== false && $val !== '') {
            return (string) $val;
        }

        if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
            return (string) $_ENV[$key];
        }

        if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
            return (string) $_SERVER[$key];
        }

        return $default;
    }

    /**
     * Upload binary data secara langsung ke Supabase Storage (Mendukung WebP)
     */
    public function uploadFile(string $storagePath, string $binaryData, string $contentType = 'image/webp'): bool
    {
        error_log('[PRODUCT_TRACE] STORAGE_UPLOAD_START');

        if ($this->supabaseUrl === '' || $this->serviceRoleKey === '') {
            error_log('[PRODUCT_TRACE] STORAGE_UPLOAD_FAILED - Credentials not found in environment');
            throw new RuntimeException('Supabase Storage credentials are not fully configured.');
        }

        error_log('[PRODUCT_TRACE] STORAGE_ENV_READY');
        error_log('[PRODUCT_TRACE] STORAGE_BUCKET_READY - Bucket: ' . $this->bucket);

        $cleanPath = ltrim($storagePath, '/');
        $url = $this->supabaseUrl . '/storage/v1/object/' . $this->bucket . '/' . $cleanPath;
        
        error_log('[PRODUCT_TRACE] STORAGE_HTTP_REQUEST - Target: ' . $url);

        $ch = curl_init($url);
        if ($ch === false) {
            error_log('[PRODUCT_TRACE] STORAGE_UPLOAD_FAILED - Failed to init cURL');
            throw new RuntimeException('Failed to initialize cURL for Supabase Storage.');
        }

        $headers = [
            'Authorization: Bearer ' . $this->serviceRoleKey,
            'apikey: ' . $this->serviceRoleKey,
            'Content-Type: ' . $contentType,
            'x-upsert: true'
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $binaryData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError !== '') {
            error_log('[PRODUCT_TRACE] STORAGE_UPLOAD_FAILED - Network cURL Error: ' . $curlError);
            throw new RuntimeException('Network error during upload to Supabase Storage.');
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            error_log('[PRODUCT_TRACE] STORAGE_UPLOAD_SUCCESS - Object Path: ' . $cleanPath);
            return true;
        }

        // Diagnostic log aman tanpa secret, HTTP respons direkam untuk debugging permission (403, 401, 400, 404)
        $safeResponse = substr((string)$response, 0, 500);
        error_log(sprintf('[PRODUCT_TRACE] STORAGE_UPLOAD_FAILED - HTTP %d - Response: %s', $httpCode, $safeResponse));

        throw new RuntimeException('Supabase Storage upload failed. HTTP status: ' . $httpCode);
    }

    /**
     * Hapus objek dari Supabase Storage
     */
    public function deleteFile(string $storagePath): bool
    {
        if ($this->supabaseUrl === '' || $this->serviceRoleKey === '') {
            return false;
        }

        $cleanPath = ltrim($storagePath, '/');
        $url = $this->supabaseUrl . '/storage/v1/object/' . $this->bucket . '/' . $cleanPath;

        $ch = curl_init($url);
        if ($ch === false) {
            return false;
        }

        $headers = [
            'Authorization: Bearer ' . $this->serviceRoleKey,
            'apikey: ' . $this->serviceRoleKey
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($httpCode >= 200 && $httpCode < 300);
    }

    /**
     * Dapatkan Public URL permanen dari Supabase Storage
     */
    public function getPublicUrl(string $storagePath): string
    {
        $cleanPath = ltrim($storagePath, '/');
        return $this->supabaseUrl . '/storage/v1/object/public/' . $this->bucket . '/' . $cleanPath;
    }
}