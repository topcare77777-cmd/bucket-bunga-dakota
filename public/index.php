<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';

use app\core\router;
use app\core\request;
use app\controllers\productcontroller;
use app\controllers\authcontroller;
use app\controllers\statscontroller;
use app\core\database;

$router = new router();
$request = new request();

// ------------------------------------------------------------------
// PENCATAT KUNJUNGAN TOKO (Otomatis Buat Tabel & Tambah Visitor)
// ------------------------------------------------------------------
$uri = $_SERVER['REQUEST_URI'] ?? '/';
if ($uri === '/' || $uri === '/index.php') {
    try {
        $db = database::getconnection();
        $db->exec("
            CREATE TABLE IF NOT EXISTS visitor_stats (
                id INT AUTO_INCREMENT PRIMARY KEY,
                type VARCHAR(50) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        $db->exec("INSERT INTO visitor_stats (type) VALUES ('visitor')");
    } catch (\Throwable $e) {
        // Abaikan jika database offline
    }
}

// ------------------------------------------------------------------
// ROUTE PUBLIK (Toko Depan & Autentikasi)
// ------------------------------------------------------------------
$router->add('GET', '/', [productcontroller::class, 'home']);
$router->add('POST', '/api/track-order', [statscontroller::class, 'trackorder']);

$router->add('GET', '/login', [authcontroller::class, 'showlogin']);
$router->add('POST', '/login', [authcontroller::class, 'login']);
$router->add('GET', '/logout', [authcontroller::class, 'logout']);
$router->add('GET', '/admin/logout', [authcontroller::class, 'logout']);

// ------------------------------------------------------------------
// PROTEKSI HALAMAN ADMIN (Sistem Login / Satpam Akses)
// ------------------------------------------------------------------
if (strpos($uri, '/admin') === 0 && $uri !== '/admin/logout') {
    if (empty($_SESSION['is_admin'])) {
        header('Location: /login');
        exit;
    }
}

// ------------------------------------------------------------------
// ROUTE ADMIN PANEL (Hanya dapat diakses setelah Login)
// ------------------------------------------------------------------
$router->add('GET', '/admin/dashboard', [productcontroller::class, 'dashboard']);
$router->add('GET', '/admin/products', [productcontroller::class, 'index']);
$router->add('GET', '/admin/products/create', [productcontroller::class, 'create']);
$router->add('POST', '/admin/products/store', [productcontroller::class, 'store']);
$router->add('GET', '/admin/products/edit', [productcontroller::class, 'edit']);
$router->add('POST', '/admin/products/update', [productcontroller::class, 'update']);
$router->add('POST', '/admin/products/delete', [productcontroller::class, 'delete']);

// ROUTE PENGATURAN KONTAK HP / WA
$router->add('GET', '/admin/change-contact', [authcontroller::class, 'showChangeContact']);
$router->add('POST', '/admin/change-contact', [authcontroller::class, 'updateContact']);

// ROUTE PENGATURAN PASSWORD
$router->add('GET', '/admin/change-password', [authcontroller::class, 'showchangePassword']);
$router->add('POST', '/admin/change-password', [authcontroller::class, 'updatepassword']);

// Jalankan Router
$router->resolve($request);