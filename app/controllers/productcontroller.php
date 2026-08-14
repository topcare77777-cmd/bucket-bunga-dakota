<?php
declare(strict_types=1);

namespace app\controllers;

use app\models\product;
use app\core\database;

class productcontroller extends basecontroller
{
    private product $productModel;

    public function __construct()
    {
        $this->productModel = new product();
    }

    /**
     * Tampilkan Halaman Utama Toko Publik
     */
    public function home(): void
    {
        try {
            $products = $this->productModel->getAll();
        } catch (\Throwable $e) {
            $products = [];
        }

        $adminPhone = '081234567890';
        try {
            $db = database::getconnection();
            $stmt = $db->query("SELECT phone FROM admins ORDER BY id ASC LIMIT 1");
            $admin = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!empty($admin['phone'])) {
                $adminPhone = $admin['phone'];
            }
        } catch (\Throwable $e) {
            // Default nomor jika error
        }

        // Cari file view home secara otomatis (kompatibel berbagai penamaan folder)
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

        die("View halaman home tidak ditemukan. Pastikan file ada di app/views/home/index.php");
    }

    /**
     * Tampilkan Dashboard Admin
     */
    public function dashboard(): void
    {
        try {
            $products = $this->productModel->getAll();
        } catch (\Throwable $e) {
            $products = [];
        }

        $totalVisitors = 0;
        $totalOrders = 0;

        try {
            $db = database::getconnection();
            $totalVisitors = (int) $db->query("SELECT COUNT(*) FROM visitor_stats WHERE type = 'visitor'")->fetchColumn();
            $totalOrders   = (int) $db->query("SELECT COUNT(*) FROM visitor_stats WHERE type = 'order'")->fetchColumn();
        } catch (\Throwable $e) {
            // Database offline fallback
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
        } catch (\Throwable $e) {
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
     * Helper Unggah & Otomatis Konversi Semua Gambar ke Format WEBP
     */
    private function handleImageUpload(array $file): ?string
    {
        if (empty($file['name']) || empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $imageResource = null;
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $imageResource = @imagecreatefromjpeg($file['tmp_name']);
                break;
            case 'image/png':
                $imageResource = @imagecreatefrompng($file['tmp_name']);
                if ($imageResource) {
                    imagepalettetotruecolor($imageResource);
                    imagealphablending($imageResource, true);
                    imagesavealpha($imageResource, true);
                }
                break;
            case 'image/webp':
                $imageResource = @imagecreatefromwebp($file['tmp_name']);
                break;
            default:
                throw new \Exception('Format file tidak didukung. Harap unggah format JPG, PNG, atau WEBP.');
        }

        if (!$imageResource) {
            throw new \Exception('Gagal memproses gambar. Pastikan file gambar tidak rusak.');
        }

        $isVercel = !empty(getenv('VERCEL')) || !empty(getenv('DB_HOST'));

        // 1. JIKA DI VERCEL: Simpan sebagai WebP Base64 Data URL (Kualitas 80%)
        if ($isVercel) {
            ob_start();
            imagewebp($imageResource, null, 80);
            $webpData = ob_get_clean();
            imagedestroy($imageResource);

            return 'data:image/webp;base64,' . base64_encode($webpData);
        }

        // 2. JIKA DI LOKAL: Simpan file .webp di public/uploads/products/
        $uploadDir = __DIR__ . '/../../public/uploads/products/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }

        $fileName = 'prod_' . uniqid('', true) . '.webp';
        $targetPath = $uploadDir . $fileName;

        imagewebp($imageResource, $targetPath, 80);
        imagedestroy($imageResource);

        return '/uploads/products/' . $fileName;
    }

    /**
     * Simpan Produk Baru
     */
    public function store(): void
    {
        $name        = trim($_POST['name'] ?? '');
        $price       = (float) ($_POST['price'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        if (empty($name) || $price <= 0) {
            $_SESSION['flash_error'] = 'Nama produk dan harga harus diisi dengan benar!';
            header('Location: /admin/products/create');
            exit;
        }

        try {
            $image = null;
            if (!empty($_FILES['image']['name'])) {
                $image = $this->handleImageUpload($_FILES['image']);
            }

            $this->productModel->create([
                'name'        => $name,
                'price'       => $price,
                'description' => $description,
                'image'       => $image
            ]);

            $_SESSION['flash_success'] = 'Produk berhasil ditambahkan ke katalog!';
            header('Location: /admin/products');
            exit;
        } catch (\Throwable $e) {
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
     * Update Produk
     */
    public function update(): void
    {
        $id          = (int) ($_POST['id'] ?? 0);
        $name        = trim($_POST['name'] ?? '');
        $price       = (float) ($_POST['price'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        $product = $this->productModel->find($id);
        if (!$product) {
            $_SESSION['flash_error'] = 'Produk tidak ditemukan!';
            header('Location: /admin/products');
            exit;
        }

        try {
            $image = $product['image'];
            if (!empty($_FILES['image']['name'])) {
                $newImage = $this->handleImageUpload($_FILES['image']);
                if ($newImage) {
                    $image = $newImage;
                }
            }

            $this->productModel->update($id, [
                'name'        => $name,
                'price'       => $price,
                'description' => $description,
                'image'       => $image
            ]);

            $_SESSION['flash_success'] = 'Produk berhasil diperbarui!';
            header('Location: /admin/products');
            exit;
        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = 'Gagal memperbarui produk: ' . $e->getMessage();
            header('Location: /admin/products/edit?id=' . $id);
            exit;
        }
    }

    /**
     * Hapus Produk
     */
    public function delete(): void
    {
        $id = (int) ($_POST['id'] ?? 0);

        try {
            $this->productModel->delete($id);
            $_SESSION['flash_success'] = 'Produk berhasil dihapus!';
        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = 'Gagal menghapus produk: ' . $e->getMessage();
        }

        header('Location: /admin/products');
        exit;
    }
}