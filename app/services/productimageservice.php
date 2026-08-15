<?php

declare(strict_types=1);

namespace app\services;

use app\helpers\imagehelper;
use RuntimeException;
use Throwable;

class productimageservice
{
    private supabasestorageservice $storageService;

    public function __construct(?supabasestorageservice $storageService = null)
    {
        $this->storageService = $storageService ?? new supabasestorageservice();
    }

    public function handleProductUpload(array $file, string $productName): array
    {
        error_log(
            '[PRODUCT_TRACE] IMAGE_PIPELINE: Validating and Processing WebP...'
        );

        $mime = imagehelper::validateUpload($file);
        $filename = imagehelper::generateSlugFilename($productName);

        $mainPath = 'products/' . $filename;
        $thumbPath = 'thumbnails/' . $filename;

        try {
            $mainBinary = imagehelper::processToWebp(
                $file['tmp_name'],
                $mime,
                800,
                1000,
                80
            );

            error_log(
                '[PRODUCT_TRACE] IMAGE_PIPELINE: Uploading Main Image...'
            );

            $this->storageService->uploadFile(
                $mainPath,
                $mainBinary,
                'image/webp'
            );

            error_log(
                '[PRODUCT_TRACE] IMAGE_PIPELINE: Processing & Uploading Thumbnail...'
            );

            $thumbBinary = imagehelper::processToWebp(
                $file['tmp_name'],
                $mime,
                200,
                250,
                80
            );

            $this->storageService->uploadFile(
                $thumbPath,
                $thumbBinary,
                'image/webp'
            );

            error_log(
                '[PRODUCT_TRACE] IMAGE_PIPELINE: Success. URLs generated.'
            );

            return [
                'image_url' => $this->storageService->getPublicUrl(
                    $mainPath
                ),
                'thumbnail_url' => $this->storageService->getPublicUrl(
                    $thumbPath
                ),
                'image_path' => $mainPath,
                'thumbnail_path' => $thumbPath,
            ];
        } catch (Throwable $e) {
            error_log(
                '[PRODUCT_TRACE] IMAGE_PIPELINE FAILED: ' .
                $e->getMessage()
            );

            $this->cleanupStorageFiles(
                $mainPath,
                $thumbPath
            );

            throw new RuntimeException(
                $e->getMessage(),
                0,
                $e
            );
        }
    }

    public function cleanupStorageFiles(
        ?string $mainPath,
        ?string $thumbPath
    ): bool {
        $allSuccess = true;

        if (!empty($mainPath)) {
            if (!$this->storageService->deleteFile($mainPath)) {
                $allSuccess = false;
            }
        }

        if (!empty($thumbPath)) {
            if (!$this->storageService->deleteFile($thumbPath)) {
                $allSuccess = false;
            }
        }

        return $allSuccess;
    }
}