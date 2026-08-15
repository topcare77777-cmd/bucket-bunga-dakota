<?php
declare(strict_types=1);

namespace app\services;

use app\helpers\imagehelper;
use Exception;
use RuntimeException;

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
        error_log('[PRODUCT_TRACE] FILE_RECEIVED - Processing upload for: ' . $productName);

        $mime = imagehelper::validateUpload($file);
        $filename = imagehelper::generateSlugFilename($productName);

        $mainPath = 'products/' . $filename;
        $thumbPath = 'thumbnails/' . $filename;

        try {
            // 1. Proses Main Image WebP (Maks 800x1000)
            error_log('[PRODUCT_TRACE] IMAGE_UPLOAD_STARTED - Main Image');
            $mainBinary = imagehelper::processToWebp($file['tmp_name'], $mime, 800, 1000, 80);

            // 2. Upload Sequential: Main Image
            $this->storageService->uploadFile($mainPath, $mainBinary, 'image/webp');

            // 3. Proses Thumbnail WebP (Maks 200x250)
            error_log('[PRODUCT_TRACE] IMAGE_UPLOAD_STARTED - Thumbnail Image');
            $thumbBinary = imagehelper::processToWebp($file['tmp_name'], $mime, 200, 250, 80);

            // 4. Upload Sequential: Thumbnail
            $this->storageService->uploadFile($thumbPath, $thumbBinary, 'image/webp');

            error_log('[PRODUCT_TRACE] IMAGE_UPLOAD_SUCCESS - Both files uploaded successfully');

            return [
                'image_url'      => $this->storageService->getPublicUrl($mainPath),
                'thumbnail_url'  => $this->storageService->getPublicUrl($thumbPath),
                'image_path'     => $mainPath,
                'thumbnail_path' => $thumbPath,
            ];

        } catch (Exception $e) {
            error_log('[PRODUCT_TRACE] IMAGE_UPLOAD_FAILED - Error: ' . $e->getMessage());
            
            // Clean up potentially orphaned storage files before throwing
            $this->cleanupStorageFiles($mainPath, $thumbPath);
            
            throw new RuntimeException('Gagal mengunggah gambar ke storage. Silakan coba lagi.');
        }
    }

    /**
     * Cleanup sekumpulan file storage (digunakan saat rollback)
     */
    public function cleanupStorageFiles(?string $mainPath, ?string $thumbPath): bool
    {
        $allSuccess = true;

        if (!empty($mainPath)) {
            if (!$this->storageService->deleteFile($mainPath)) {
                error_log('[CRITICAL AUDIT] Gagal menghapus storage object: ' . $mainPath);
                $allSuccess = false;
            }
        }

        if (!empty($thumbPath)) {
            if (!$this->storageService->deleteFile($thumbPath)) {
                error_log('[CRITICAL AUDIT] Gagal menghapus storage object: ' . $thumbPath);
                $allSuccess = false;
            }
        }

        return $allSuccess;
    }
}