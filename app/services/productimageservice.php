<?php
declare(strict_types=1);

namespace app\services;

use app\helpers\imagehelper;
use Exception;

class productimageservice
{
    private supabasestorageservice $storageService;

    public function __construct(?supabasestorageservice $storageService = null)
    {
        $this->storageService = $storageService ?? new supabasestorageservice();
    }

    /**
     * Eksekusi alur upload sequential aman untuk produk
     *
     * @return array{image_url: string, thumbnail_url: string, image_path: string, thumbnail_path: string}
     */
    public function handleProductUpload(array $file, string $productName): array
    {
        $mime = imagehelper::validateUpload($file);
        $filename = imagehelper::generateSlugFilename($productName);

        $mainPath = 'products/' . $filename;
        $thumbPath = 'thumbnails/' . $filename;

        // 1. Proses Main Image WebP (Maks 800x1000)
        $mainBinary = imagehelper::processToWebp($file['tmp_name'], $mime, 800, 1000, 80);

        // 2. Upload Sequential: Main Image
        $mainUploadOk = $this->storageService->uploadFile($mainPath, $mainBinary, 'image/webp');
        if (!$mainUploadOk) {
            throw new Exception('Gagal mengunggah foto utama ke Supabase Storage.');
        }

        // 3. Proses Thumbnail WebP (Maks 200x250)
        try {
            $thumbBinary = imagehelper::processToWebp($file['tmp_name'], $mime, 200, 250, 80);
        } catch (Exception $e) {
            $this->storageService->deleteFile($mainPath);
            throw new Exception('Gagal memproses thumbnail: ' . $e->getMessage());
        }

        // 4. Upload Sequential: Thumbnail
        $thumbUploadOk = $this->storageService->uploadFile($thumbPath, $thumbBinary, 'image/webp');
        if (!$thumbUploadOk) {
            $this->storageService->deleteFile($mainPath);
            throw new Exception('Gagal mengunggah thumbnail ke Supabase Storage.');
        }

        return [
            'image_url'      => $this->storageService->getPublicUrl($mainPath),
            'thumbnail_url'  => $this->storageService->getPublicUrl($thumbPath),
            'image_path'     => $mainPath,
            'thumbnail_path' => $thumbPath,
        ];
    }

    /**
     * Cleanup sekumpulan file storage (misal ketika DB query gagal atau saat hapus produk)
     */
    public function cleanupStorageFiles(?string $mainPath, ?string $thumbPath): bool
    {
        $allSuccess = true;

        if (!empty($mainPath)) {
            $deletedMain = $this->storageService->deleteFile($mainPath);
            if (!$deletedMain) {
                error_log('[CRITICAL AUDIT] Gagal menghapus storage object: ' . $mainPath);
                $allSuccess = false;
            }
        }

        if (!empty($thumbPath)) {
            $deletedThumb = $this->storageService->deleteFile($thumbPath);
            if (!$deletedThumb) {
                error_log('[CRITICAL AUDIT] Gagal menghapus storage object: ' . $thumbPath);
                $allSuccess = false;
            }
        }

        return $allSuccess;
    }
}