<?php
declare(strict_types=1);

namespace app\controllers;

use app\models\product;
use app\services\productimageservice;
use app\core\database;
use Throwable;

class productcontroller extends basecontroller
{
    private product $productModel;
    private productimageservice $imageService;

    public function __construct()
    {
        $this->productModel = new product();
        $this->imageService = new productimageservice();
    }

    /**
     * Tampilkan Halaman Utama Toko Publik
     */
    public function home(): void
    {
        try {
            $products = $this->productModel->getAll();
        } catch (Throwable $e) {
            error_log('[ProductController:home] Error fetching products: ' . $e->getMessage());
            $products = [];
        }

        $adminPhone = '081234567890';
        try {
            $db = database::getconnection();
            $stmt = $db->query("SELECT phone FROM admins ORDER BY id ASC LIMIT 1");
            $admin = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!empty($admin['phone'])) {
                $adminPhone = (string) $admin['phone'];
            }
        } catch (Throwable $e) {
            error_log('[ProductController:home] Error fetching contact: ' . $e->getMessage());
        }

        $possibleViews = [
            __DIR__ . '/../views/home/index.php',
            __DIR__ . '/../views/Home/index.php',
            __DIR__ . '/../views/home.php',
            __DIR__ . '/../views/index.php'
        ];

        foreach ($possibleViews as $viewFile) {
            if (file_exists($viewFile)) {
                require_once $viewFile;
                return;
            }
        }

        die('View halaman home tidak ditemukan.');
    }

    /**
     * Tampilkan Dashboard Admin
     */
    public function dashboard(): void
    {
        try {
            $products = $this->productModel->getAll();
        } catch (Throwable $e) {
            error_log('[ProductController:dashboard] Error fetching products: ' . $e->getMessage());
            $products = [];
        }

        $totalVisitors = 0;
        $totalOrders = 0;

        try {
            $db = database::getconnection();
            $totalVisitors = (int) $db->query("SELECT COUNT(*) FROM visitor_stats WHERE type = 'visitor'")->fetchColumn();
            $totalOrders   = (int) $db->query("SELECT COUNT(*) FROM visitor_stats WHERE type = 'order'")->fetchColumn();
        } catch (Throwable $e) {
            error_log('[ProductController:dashboard] Error fetching stats: ' . $e->getMessage());
        }

        $totalProducts = count($products);

        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    /**
     * Tampilkan Daftar Produk di Panel Admin
     */
    public function index(): void
    {
        try {
            $products = $this->productModel->getAll();
        } catch (Throwable $e) {
            error_log('[ProductController:index] Error fetching products: ' . $e->getMessage());
            $products = [];
        }

        $success = $_SESSION['flash_success'] ?? null;
        $error   = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        require_once __DIR__ . '/../views/admin/products/index.php';
    }

    /**
     * Halaman Tambah Produk Baru
     */
    public function create(): void
    {
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        require_once __DIR__ . '/../views/admin/products/create.php';
    }

    /**
     * Simpan Produk Baru (Safe Atomic Flow)
     */
    public function store(): void
    {
        $name        = trim((string) ($_POST['name'] ?? ''));
        $price       = (float) ($_POST['price'] ?? 0);
        $description = trim((string) ($_POST['description'] ?? ''));
        $stock       = max(0, (int) ($_POST['stock'] ?? 10));
        
        $allowedStatuses = ['available', 'reserved', 'sold'];
        $rawStatus = (string) ($_POST['status'] ?? 'available');
        $status = in_array($rawStatus, $allowedStatuses, true) ? $rawStatus : 'available';

        if ($name === '' || $price <= 0) {
            $_SESSION['flash_error'] = 'Nama produk dan harga harus diisi dengan benar!';
            header('Location: /admin/products/create');
            exit;
        }

        $uploadedImage = null;
        try {
            // Upload gambar via ProductImageService jika ada berkas yang dikirim
            if (isset($_FILES['image']) && is_array($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {
                $uploadedImage = $this->imageService->handleProductUpload($_FILES['image'], $name);
            }

            $insertSuccess = $this->productModel->create([
                'name'           => $name,
                'price'          => $price,
                'description'    => $description,
                'image_url'      => $uploadedImage['image_url'] ?? null,
                'thumbnail_url'  => $uploadedImage['thumbnail_url'] ?? null,
                'image_path'     => $uploadedImage['image_path'] ?? null,
                'thumbnail_path' => $uploadedImage['thumbnail_path'] ?? null,
                'stock'          => $stock,
                'status'         => $status
            ]);

            if (!$insertSuccess) {
                throw new \RuntimeException('Gagal menyimpan catatan produk ke database.');
            }

            $_SESSION['flash_success'] = 'Produk berhasil ditambahkan ke katalog!';
            header('Location: /admin/products');
            exit;
        } catch (Throwable $e) {
            // Rollback Storage jika DB gagal atau terjadi error
            if ($uploadedImage !== null) {
                $this->imageService->cleanupStorageFiles(
                    $uploadedImage['image_path'] ?? null,
                    $uploadedImage['thumbnail_path'] ?? null
                );
            }

            error_log('[ProductController:store] Storage/DB Error: ' . $e->getMessage());
            $_SESSION['flash_error'] = 'Gagal menambahkan produk: ' . $e->getMessage();
            header('Location: /admin/products/create');
            exit;
        }
    }

    /**
     * Halaman Edit Produk
     */
    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $product = $this->productModel->find($id);

        if (!$product) {
            $_SESSION['flash_error'] = 'Produk tidak ditemukan!';
            header('Location: /admin/products');
            exit;
        }

        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        require_once __DIR__ . '/../views/admin/products/edit.php';
    }

    /**
     * Update Produk (Safe Two-Phase Update Flow)
     */
    public function update(): void
    {
        $id          = (int) ($_POST['id'] ?? 0);
        $name        = trim((string) ($_POST['name'] ?? ''));
        $price       = (float) ($_POST['price'] ?? 0);
        $description = trim((string) ($_POST['description'] ?? ''));
        $stock       = max(0, (int) ($_POST['stock'] ?? 10));

        $allowedStatuses = ['available', 'reserved', 'sold'];
        $rawStatus = (string) ($_POST['status'] ?? 'available');
        $status = in_array($rawStatus, $allowedStatuses, true) ? $rawStatus : 'available';

        if ($id <= 0 || $name === '' || $price <= 0) {
            $_SESSION['flash_error'] = 'Data produk tidak valid!';
            header('Location: /admin/products');
            exit;
        }

        $existing = $this->productModel->find($id);
        if (!$existing) {
            $_SESSION['flash_error'] = 'Produk tidak ditemukan!';
            header('Location: /admin/products');
            exit;
        }

        $newUpload = null;
        try {
            $imageUrl      = $existing['image_url'] ?? null;
            $thumbnailUrl  = $existing['thumbnail_url'] ?? null;
            $imagePath     = $existing['image_path'] ?? null;
            $thumbnailPath = $existing['thumbnail_path'] ?? null;

            // Jika mengunggah gambar baru
            if (isset($_FILES['image']) && is_array($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {
                $newUpload     = $this->imageService->handleProductUpload($_FILES['image'], $name);
                $imageUrl      = $newUpload['image_url'];
                $thumbnailUrl  = $newUpload['thumbnail_url'];
                $imagePath     = $newUpload['image_path'];
                $thumbnailPath = $newUpload['thumbnail_path'];
            }

            $updateSuccess = $this->productModel->update($id, [
                'name'           => $name,
                'price'          => $price,
                'description'    => $description,
                'image_url'      => $imageUrl,
                'thumbnail_url'  => $thumbnailUrl,
                'image_path'     => $imagePath,
                'thumbnail_path' => $thumbnailPath,
                'stock'          => $stock,
                'status'         => $status
            ]);

            if (!$updateSuccess) {
                throw new \RuntimeException('Gagal memperbarui catatan database.');
            }

            // Jika DB update berhasil dan ada gambar baru, hapus gambar lama dari Storage
            if ($newUpload !== null) {
                $this->imageService->cleanupStorageFiles(
                    $existing['image_path'] ?? null,
                    $existing['thumbnail_path'] ?? null
                );
            }

            $_SESSION['flash_success'] = 'Produk berhasil diperbarui!';
            header('Location: /admin/products');
            exit;
        } catch (Throwable $e) {
            // Jika update gagal, bersihkan file baru dan pertahankan file lama
            if ($newUpload !== null) {
                $this->imageService->cleanupStorageFiles(
                    $newUpload['image_path'] ?? null,
                    $newUpload['thumbnail_path'] ?? null
                );
            }

            error_log('[ProductController:update] Storage/DB Error: ' . $e->getMessage());
            $_SESSION['flash_error'] = 'Gagal memperbarui produk: ' . $e->getMessage();
            header('Location: /admin/products/edit?id=' . $id);
            exit;
        }
    }

    /**
     * Hapus Produk (Database Delete -> Storage Cleanup -> Audit Log)
     */
    public function delete(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $product = $this->productModel->find($id);

        if (!$product) {
            $_SESSION['flash_error'] = 'Produk tidak ditemukan!';
            header('Location: /admin/products');
            exit;
        }

        $imagePath = $product['image_path'] ?? null;
        $thumbnailPath = $product['thumbnail_path'] ?? null;

        try {
            // 1. Hapus record DB terlebih dahulu
            $dbDeleted = $this->productModel->delete($id);
            if (!$dbDeleted) {
                throw new \RuntimeException('Gagal menghapus data produk dari database.');
            }

            // 2. Hapus objek dari Supabase Storage
            $storageCleaned = $this->imageService->cleanupStorageFiles($imagePath, $thumbnailPath);
            if (!$storageCleaned) {
                error_log(sprintf(
                    '[CRITICAL AUDIT] Product ID %d deleted from DB, but failed to delete Storage objects: main=%s, thumb=%s',
                    $id,
                    (string) $imagePath,
                    (string) $thumbnailPath
                ));
                $_SESSION['flash_success'] = 'Produk dihapus (Catatan audit log dibuat untuk rekonsiliasi storage).';
            } else {
                $_SESSION['flash_success'] = 'Produk dan gambar berhasil dihapus!';
            }
        } catch (Throwable $e) {
            error_log('[ProductController:delete] Error: ' . $e->getMessage());
            $_SESSION['flash_error'] = 'Gagal menghapus produk: ' . $e->getMessage();
        }

        header('Location: /admin/products');
        exit;
    }
}