<?php
declare(strict_types=1);

namespace app\services;

use Exception;
use RuntimeException;

class supabasestorageservice
{
    private string $supabaseUrl;
    private string $serviceRoleKey;
    private string $bucket;

    public function __construct(?string $supabaseUrl = null, ?string $serviceRoleKey = null, ?string $bucket = null)
    {
        $this->supabaseUrl = rtrim($supabaseUrl ?? (string) (getenv('SUPABASE_URL') ?: ($_ENV['SUPABASE_URL'] ?? '')), '/');
        $this->serviceRoleKey = trim($serviceRoleKey ?? (string) (getenv('SUPABASE_SERVICE_ROLE_KEY') ?: ($_ENV['SUPABASE_SERVICE_ROLE_KEY'] ?? '')));
        $this->bucket = trim($bucket ?? (string) (getenv('SUPABASE_STORAGE_BUCKET') ?: ($_ENV['SUPABASE_STORAGE_BUCKET'] ?? 'bucket-bunga-dakota')));

        if ($this->supabaseUrl === '' || $this->serviceRoleKey === '') {
            error_log('[SupabaseStorageService] Inisialisasi gagal: SUPABASE_URL atau SUPABASE_SERVICE_ROLE_KEY belum dikonfigurasi di Environment Variables.');
        }
    }

    /**
     * Upload binary data secara langsung ke Supabase Storage (Mendukung WebP)
     */
    public function uploadFile(string $storagePath, string $binaryData, string $contentType = 'image/webp'): bool
    {
        if ($this->supabaseUrl === '' || $this->serviceRoleKey === '') {
            throw new RuntimeException('Supabase Storage belum dikonfigurasi dengan benar.');
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
            error_log('[SupabaseStorageService] cURL Error saat upload: ' . $curlError);
            return false;
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            return true;
        }

        error_log('[SupabaseStorageService] Upload HTTP Fail Code ' . $httpCode . ': ' . (string) $response);
        return false;
    }

    /**
     * Hapus objek dari Supabase Storage
     */
    public function deleteFile(string $storagePath): bool
    {
        if ($this->supabaseUrl === '' || $this->serviceRoleKey === '') {
            error_log('[SupabaseStorageService] Gagal menghapus file: Konfigurasi Supabase tidak lengkap.');
            return false;
        }

        $cleanPath = ltrim($storagePath, '/');
        $url = $this->supabaseUrl . '/storage/v1/object/' . $this->bucket . '/' . $cleanPath;

        $ch = curl_init($url);
        if ($ch === false) {
            error_log('[SupabaseStorageService] Gagal menginisialisasi cURL untuk delete.');
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
            error_log('[SupabaseStorageService] cURL Error saat delete file (' . $cleanPath . '): ' . $curlError);
            return false;
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            return true;
        }

        error_log('[SupabaseStorageService] Delete HTTP Fail Code ' . $httpCode . ' on ' . $cleanPath . ': ' . (string) $response);
        return false;
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