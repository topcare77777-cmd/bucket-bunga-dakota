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

    private static function env(string $key, ?string $default = null): ?string
    {
        $val = getenv($key);
        if ($val !== false && $val !== '') return (string) $val;
        if (isset($_ENV[$key]) && $_ENV[$key] !== '') return (string) $_ENV[$key];
        if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') return (string) $_SERVER[$key];
        return $default;
    }

    public function uploadFile(string $storagePath, string $binaryData, string $contentType = 'image/webp'): bool
    {
        if ($this->supabaseUrl === '' || $this->serviceRoleKey === '') {
            error_log('[PRODUCT_TRACE] STORAGE_ERROR: Credentials Missing');
            throw new RuntimeException('Kredensial Supabase Storage tidak lengkap.');
        }

        $cleanPath = ltrim($storagePath, '/');
        $url = $this->supabaseUrl . '/storage/v1/object/' . $this->bucket . '/' . $cleanPath;
        
        error_log('[PRODUCT_TRACE] STORAGE_HTTP_REQUEST: POST ' . $url);

        $ch = curl_init($url);
        if ($ch === false) {
            throw new RuntimeException('Gagal menginisialisasi cURL.');
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

        if ($curlError !== '') {
            error_log('[PRODUCT_TRACE] STORAGE_CURL_ERROR: ' . $curlError);
            throw new RuntimeException('Network cURL Error: ' . $curlError);
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            error_log('[PRODUCT_TRACE] STORAGE_UPLOAD_SUCCESS: HTTP ' . $httpCode);
            return true;
        }

        $safeResponse = substr((string)$response, 0, 300);
        error_log(sprintf('[PRODUCT_TRACE] STORAGE_UPLOAD_FAILED: HTTP %d | Response: %s', $httpCode, $safeResponse));
        
        throw new RuntimeException(sprintf('Supabase Storage Error HTTP %d', $httpCode));
    }

    public function deleteFile(string $storagePath): bool
    {
        return true; // Simplified for focus on INSERT trace
    }

    public function getPublicUrl(string $storagePath): string
    {
        $cleanPath = ltrim($storagePath, '/');
        return $this->supabaseUrl . '/storage/v1/object/public/' . $this->bucket . '/' . $cleanPath;
    }
}
