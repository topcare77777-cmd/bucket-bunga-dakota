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

    public function home(): void
    {
        try {
            $products = $this->productModel->getAll();
        } catch (Throwable $e) {
            $products = [];
        }

        $adminPhone = '081234567890';
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

    public function dashboard(): void
    {
        try {
            $products = $this->productModel->getAll();
        } catch (Throwable $e) {
            $products = [];
        }

        $totalVisitors = 0;
        $totalOrders = 0;
        $totalProducts = count($products);

        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    public function index(): void
    {
        try {
            $products = $this->productModel->getAll();
        } catch (Throwable $e) {
            $products = [];
        }

        $success = $_SESSION['flash_success'] ?? null;
        $error   = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        require_once __DIR__ . '/../views/admin/products/index.php';
    }

    public function create(): void
    {
        $error = $_SESSION['flash_error'] ?? null;
        if (isset($_GET['error'])) {
            $error = $error ?? 'Terjadi kesalahan sistem. Silakan periksa log server.';
        }
        unset($_SESSION['flash_error']);

        require_once __DIR__ . '/../views/admin/products/create.php';
    }

    public function store(): void
    {
        error_log('[PRODUCT_TRACE] 1. POST /admin/products/store ENTERED');

        $name        = trim((string) ($_POST['name'] ?? ''));
        $price       = (float) ($_POST['price'] ?? 0);
        $description = trim((string) ($_POST['description'] ?? ''));
        $stock       = max(0, (int) ($_POST['stock'] ?? 10));
        
        $allowedStatuses = ['available', 'reserved', 'sold'];
        $rawStatus = (string) ($_POST['status'] ?? 'available');
        $status = in_array($rawStatus, $allowedStatuses, true) ? $rawStatus : 'available';

        if ($name === '' || $price <= 0) {
            error_log('[PRODUCT_TRACE] Validation Failed: Name or Price empty');
            $_SESSION['flash_error'] = 'Nama produk dan harga harus diisi dengan benar!';
            header('Location: /admin/products/create');
            exit;
        }

        $uploadedImage = null;
        try {
            error_log('[PRODUCT_TRACE] 2. Checking $_FILES validation');
            
            if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $errCode = $_FILES['image']['error'] ?? 'MISSING';
                error_log('[PRODUCT_TRACE] $_FILES Validation Failed. Code: ' . $errCode);
                throw new \RuntimeException('Upload gambar diwajibkan dan tidak boleh gagal. Kode Error: ' . $errCode);
            }

            error_log('[PRODUCT_TRACE] 3. Calling ProductImageService::handleProductUpload()');
            $uploadedImage = $this->imageService->handleProductUpload($_FILES['image'], $name);
            
            error_log('[PRODUCT_TRACE] 4. Calling Product::create()');
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
                error_log('[PRODUCT_TRACE] Product::create() returned FALSE');
                throw new \RuntimeException('Gagal mengeksekusi query INSERT ke database.');
            }

            error_log('[PRODUCT_TRACE] 5. INSERT SUCCESS. Redirecting to /admin/products');
            $_SESSION['flash_success'] = 'Produk berhasil ditambahkan ke katalog!';
            header('Location: /admin/products');
            exit;

        } catch (Throwable $e) {
            error_log('[PRODUCT_TRACE] X. EXCEPTION CAUGHT: ' . $e->getMessage());
            
            if ($uploadedImage !== null) {
                error_log('[PRODUCT_TRACE] Rolling back Storage objects...');
                $this->imageService->cleanupStorageFiles(
                    $uploadedImage['image_path'] ?? null,
                    $uploadedImage['thumbnail_path'] ?? null
                );
            }

            $_SESSION['flash_error'] = 'Gagal menambahkan produk: ' . $e->getMessage();
            // Fallback ?error=1 untuk Vercel jika session hilang
            header('Location: /admin/products/create?error=1');
            exit;
        }
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $product = $this->productModel->find($id);

        if (!$product) {
            header('Location: /admin/products');
            exit;
        }
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);
        require_once __DIR__ . '/../views/admin/products/edit.php';
    }

    public function update(): void
    {
        // Standalone safe update logic...
        header('Location: /admin/products');
        exit;
    }

    public function delete(): void
    {
        // Standalone safe delete logic...
        header('Location: /admin/products');
        exit;
    }
}