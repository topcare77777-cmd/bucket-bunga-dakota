<?php
declare(strict_types=1);

namespace app\helpers;

use Exception;
use GdImage;

class imagehelper
{
    private const MAX_INPUT_SIZE = 2097152; // 2 MB (2 * 1024 * 1024 bytes)
    private const ALLOWED_MIMES = [
        'image/jpeg' => 'jpg',
        'image/jpg'  => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    /**
     * Validasi ketat terhadap berkas yang diunggah
     */
    public static function validateUpload(array $file): string
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            $errorCode = $file['error'] ?? UPLOAD_ERR_NO_FILE;
            throw new Exception('Gagal mengunggah berkas. Kode error: ' . $errorCode);
        }

        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception('Berkas yang diunggah tidak sah.');
        }

        $fileSize = (int) ($file['size'] ?? 0);
        if ($fileSize <= 0 || $fileSize > self::MAX_INPUT_SIZE) {
            throw new Exception('Ukuran gambar melebihi batas maksimum 2 MB.');
        }

        if (!extension_loaded('fileinfo')) {
            throw new Exception('Ekstensi PHP Fileinfo diperlukan untuk verifikasi tipe berkas.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) {
            throw new Exception('Gagal memverifikasi MIME type gambar.');
        }

        $mime = finfo_file($finfo, $file['tmp_name']);

        if (!is_string($mime) || !array_key_exists($mime, self::ALLOWED_MIMES)) {
            throw new Exception('Format berkas tidak didukung. Harap gunakan format JPG, JPEG, PNG, atau WEBP.');
        }

        return $mime;
    }

    /**
     * Generate slug nama berkas yang aman
     */
    public static function generateSlugFilename(string $productName): string
    {
        $slug = strtolower(trim($productName));
        $slug = (string) preg_replace('/[^a-z0-9]+/i', '-', $slug);
        $slug = trim($slug, '-');

        if ($slug === '') {
            $slug = 'produk';
        }

        $timestamp = (string) time();
        $random = bin2hex(random_bytes(4));

        return $slug . '-' . $timestamp . '-' . $random . '.webp';
    }

    /**
     * Memproses gambar secara in-memory: Resize proporsional & Encode ke WebP binary string
     */
    public static function processToWebp(string $sourcePath, string $mime, int $maxWidth, int $maxHeight, int $quality = 80): string
    {
        if (!extension_loaded('gd')) {
            throw new Exception('Ekstensi PHP GD diperlukan untuk memproses gambar.');
        }

        $sourceImage = self::createGdImageFromSource($sourcePath, $mime);
        if (!$sourceImage instanceof GdImage) {
            throw new Exception('Gagal membaca data gambar sumber.');
        }

        $origWidth = imagesx($sourceImage);
        $origHeight = imagesy($sourceImage);

        if ($origWidth <= 0 || $origHeight <= 0) {
            throw new Exception('Dimensi gambar tidak valid.');
        }

        // Kalkulasi aspek rasio agar tidak terdistorsi
        $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight, 1.0);
        $targetWidth = (int) round($origWidth * $ratio);
        $targetHeight = (int) round($origHeight * $ratio);

        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);
        if (!$targetImage instanceof GdImage) {
            throw new Exception('Gagal mengalokasikan memori kanvas gambar baru.');
        }

        // Penanganan transparansi PNG / WebP
        imagealphablending($targetImage, false);
        imagesavealpha($targetImage, true);

        $resampleSuccess = imagecopyresampled(
            $targetImage,
            $sourceImage,
            0, 0, 0, 0,
            $targetWidth,
            $targetHeight,
            $origWidth,
            $origHeight
        );

        if (!$resampleSuccess) {
            throw new Exception('Gagal melakukan resize proporsional gambar.');
        }

        // Tangkap binary WebP ke buffer tanpa menyentuh filesystem
        ob_start();
        $webpSuccess = imagewebp($targetImage, null, $quality);
        $binaryData = ob_get_clean();

        if (!$webpSuccess || $binaryData === false || $binaryData === '') {
            throw new Exception('Gagal mengonversi gambar ke format WebP.');
        }

        return $binaryData;
    }

    /**
     * Helper pembuat instance GdImage berdasarkan MIME terverifikasi
     */
    private static function createGdImageFromSource(string $sourcePath, string $mime): ?GdImage
    {
        $image = null;
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($sourcePath);
                break;
        }

        return $image instanceof GdImage ? $image : null;
    }
}