<?php
declare(strict_types=1);

namespace app\controllers;

use app\services\productservice;
use app\helpers\imagehelper;
use app\controllers\statscontroller;

class productcontroller
{
    private productservice $productService;

    public function __construct()
    {
        $this->productService = new productservice();
    }

    public function home(): void
    {
        $products = $this->productService->getallproducts();
        require_once __DIR__ . '/../views/home.php';
    }

    public function dashboard(): void
    {
        $products = $this->productService->getallproducts();
        $totalProducts = count($products);
        
        // Ambil data statistik kunjungan & pesanan
        $stats = statscontroller::getstats();
        
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    public function index(): void
    {
        $products = $this->productService->getallproducts();
        require_once __DIR__ . '/../views/admin/products/index.php';
    }

    public function create(): void
    {
        require_once __DIR__ . '/../views/admin/products/create.php';
    }

    public function store(): void
    {
        try {
            $name = trim($_POST['name'] ?? '');
            $price = (float) ($_POST['price'] ?? 0);
            $stock = (int) ($_POST['stock'] ?? 0);
            $description = trim($_POST['description'] ?? '');

            $imageFilename = null;

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/products';
                $imageFilename = imagehelper::convertandupload($_FILES['image'], $uploadDir);
            }

            $inputData = [
                'name'        => $name,
                'price'       => $price,
                'stock'       => $stock,
                'description' => $description,
                'image'       => $imageFilename,
                'status'      => 'active'
            ];

            $this->productService->createproduct($inputData);

            header('Location: /admin/products');
            exit;

        } catch (\Throwable $e) {
            echo "Error: " . htmlspecialchars($e->getMessage());
        }
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $product = $this->productService->getproductbyid($id);

        if (!$product) {
            header('Location: /admin/products');
            exit;
        }

        require_once __DIR__ . '/../views/admin/products/edit.php';
    }

    public function update(): void
    {
        try {
            $id = (int) ($_POST['id'] ?? 0);
            $existingProduct = $this->productService->getproductbyid($id);

            if (!$existingProduct) {
                header('Location: /admin/products');
                exit;
            }

            $name = trim($_POST['name'] ?? '');
            $price = (float) ($_POST['price'] ?? 0);
            $stock = (int) ($_POST['stock'] ?? 0);
            $description = trim($_POST['description'] ?? '');

            $imageFilename = $existingProduct['image'];

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/products';
                $imageFilename = imagehelper::convertandupload($_FILES['image'], $uploadDir);
            }

            $updateData = [
                'name'        => $name,
                'price'       => $price,
                'stock'       => $stock,
                'description' => $description,
                'image'       => $imageFilename,
                'status'      => $_POST['status'] ?? 'active'
            ];

            $this->productService->updateproduct($id, $updateData);

            header('Location: /admin/products');
            exit;

        } catch (\Throwable $e) {
            echo "Error Update: " . htmlspecialchars($e->getMessage());
        }
    }

    public function delete(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->productService->deleteproduct($id);
        }

        header('Location: /admin/products');
        exit;
    }
}