<?php
declare(strict_types=1);

namespace app\controllers;

use app\core\database;

class productcontroller extends basecontroller
{
    /**
     * Tampilkan Halaman Depan Toko (Katalog untuk Pembeli)
     */
    public function home(): void
    {
        $products = [];
        $adminPhone = '081234567890';

        try {
            $db = database::getconnection();
            
            // Ambil daftar produk
            $stmt = $db->query("SELECT * FROM products ORDER BY id DESC");
            $products = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            // Ambil nomor WA admin toko
            $phoneStmt = $db->query("SELECT phone FROM admins ORDER BY id ASC LIMIT 1");
            $admin = $phoneStmt->fetch(\PDO::FETCH_ASSOC);
            if (!empty($admin['phone'])) {
                $adminPhone = $admin['phone'];
            }
        } catch (\Throwable $e) {
            // Abaikan jika database baru
        }

        require_once __DIR__ . '/../views/home.php';
    }

    /**
     * Tampilkan Dashboard Admin Panel
     */
    public function dashboard(): void
    {
        $totalProducts = 0;
        $totalVisitors = 0;
        $waClicks = 0;

        try {
            $db = database::getconnection();

            // 1. Hitung Total Produk
            $stmt = $db->query("SELECT COUNT(*) FROM products");
            $totalProducts = (int) $stmt->fetchColumn();

            // 2. Hitung Pengunjung Toko Asli
            $stmt = $db->query("SELECT COUNT(*) FROM visitor_stats WHERE type = 'visitor'");
            $totalVisitors = (int) $stmt->fetchColumn();

            // 3. Hitung Klik Pesan WA
            $stmt = $db->query("SELECT COUNT(*) FROM visitor_stats WHERE type = 'wa_click'");
            $waClicks = (int) $stmt->fetchColumn();
        } catch (\Throwable $e) {
            // Tangani jika tabel belum dibuat
        }

        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    /**
     * Daftar Produk di Admin
     */
    public function index(): void
    {
        $products = [];
        try {
            $db = database::getconnection();
            $stmt = $db->query("SELECT * FROM products ORDER BY id DESC");
            $products = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            // Error handling
        }

        require_once __DIR__ . '/../views/admin/products/index.php';
    }

    /**
     * Form Tambah Produk
     */
    public function create(): void
    {
        require_once __DIR__ . '/../views/admin/products/create.php';
    }

    /**
     * Simpan Produk Baru
     */
    public function store(): void
    {
        $name        = trim($_POST['name'] ?? '');
        $price       = (float) ($_POST['price'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $imagePath   = '';

        if (!empty($_FILES['image']['name'])) {
            $uploadDir = __DIR__ . '/../../public/uploads/products/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = uniqid('prod_', true) . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $imagePath = '/uploads/products/' . $fileName;
            }
        }

        try {
            $db = database::getconnection();
            $stmt = $db->prepare("INSERT INTO products (name, price, description, image) VALUES (:name, :price, :description, :image)");
            $stmt->execute([
                'name'        => $name,
                'price'       => $price,
                'description' => $description,
                'image'       => $imagePath
            ]);
        } catch (\Throwable $e) {
            // Error handling
        }

        header('Location: /admin/products');
        exit;
    }

    /**
     * Form Edit Produk
     */
    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $product = null;

        try {
            $db = database::getconnection();
            $stmt = $db->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $id]);
            $product = $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            // Error handling
        }

        if (!$product) {
            header('Location: /admin/products');
            exit;
        }

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

        try {
            $db = database::getconnection();
            
            if (!empty($_FILES['image']['name'])) {
                $uploadDir = __DIR__ . '/../../public/uploads/products/';
                $fileName = uniqid('prod_', true) . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $imagePath = '/uploads/products/' . $fileName;
                    $stmt = $db->prepare("UPDATE products SET name = :name, price = :price, description = :description, image = :image WHERE id = :id");
                    $stmt->execute([
                        'name'        => $name,
                        'price'       => $price,
                        'description' => $description,
                        'image'       => $imagePath,
                        'id'          => $id
                    ]);
                }
            } else {
                $stmt = $db->prepare("UPDATE products SET name = :name, price = :price, description = :description WHERE id = :id");
                $stmt->execute([
                    'name'        => $name,
                    'price'       => $price,
                    'description' => $description,
                    'id'          => $id
                ]);
            }
        } catch (\Throwable $e) {
            // Error handling
        }

        header('Location: /admin/products');
        exit;
    }

    /**
     * Hapus Produk
     */
    public function delete(): void
    {
        $id = (int) ($_POST['id'] ?? 0);

        try {
            $db = database::getconnection();
            $stmt = $db->prepare("DELETE FROM products WHERE id = :id");
            $stmt->execute(['id' => $id]);
        } catch (\Throwable $e) {
            // Error handling
        }

        header('Location: /admin/products');
        exit;
    }
}