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

        if ($this->supabaseUrl === '' || $this->serviceRoleKey === '') {
            error_log('[SupabaseStorageService] Warning: SUPABASE_URL atau SUPABASE_SERVICE_ROLE_KEY belum terkonfigurasi di environment.');
        }
    }

    /**
     * Helper pembaca environment variable multi-sumber
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
     * Upload binary data ke Supabase Storage REST API
     */
    public function uploadFile(string $storagePath, string $binaryData, string $contentType = 'image/webp'): bool
    {
        if ($this->supabaseUrl === '' || $this->serviceRoleKey === '') {
            throw new RuntimeException('Kredensial Supabase Storage tidak lengkap pada environment server.');
        }

        $cleanPath = ltrim($storagePath, '/');
        $url = $this->supabaseUrl . '/storage/v1/object/' . $this->bucket . '/' . $cleanPath;

        $ch = curl_init($url);
        if ($ch === false) {
            throw new RuntimeException('Gagal menginisialisasi cURL untuk upload Supabase Storage.');
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
            error_log('[SupabaseStorageService] cURL Error upload: ' . $curlError);
            return false;
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            return true;
        }

        error_log(sprintf('[SupabaseStorageService] Upload Failed HTTP %d: %s', $httpCode, (string) $response));
        return false;
    }

    /**
     * Hapus objek dari Supabase Storage
     */
    public function deleteFile(string $storagePath): bool
    {
        if ($this->supabaseUrl === '' || $this->serviceRoleKey === '') {
            error_log('[SupabaseStorageService] Gagal menghapus file: Kredensial tidak lengkap.');
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
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError !== '') {
            error_log('[SupabaseStorageService] cURL Error delete: ' . $curlError);
            return false;
        }

        return ($httpCode >= 200 && $httpCode < 300);
    }

    /**
     * Dapatkan Public URL permanen
     */
    public function getPublicUrl(string $storagePath): string
    {
        $cleanPath = ltrim($storagePath, '/');
        return $this->supabaseUrl . '/storage/v1/object/public/' . $this->bucket . '/' . $cleanPath;
    }
}