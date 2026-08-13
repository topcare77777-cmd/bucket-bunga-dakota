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

// Hitung Kunjungan Toko Utama (Hanya jika belum tercatat di sesi saat ini)
$uri = $_SERVER['REQUEST_URI'] ?? '/';
if ($uri === '/' && empty($_SESSION['visited_store'])) {
    $_SESSION['visited_store'] = true;
    try {
        $db = database::getconnection();
        $db->exec("INSERT INTO visitor_stats (type) VALUES ('visitor')");
    } catch (\Throwable $e) {
        // Abaikan jika database belum siap
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

// ------------------------------------------------------------------
// PROTEKSI HALAMAN ADMIN (Sistem Login / Satpam Akses)
// ------------------------------------------------------------------
if (strpos($uri, '/admin') === 0) {
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
$router->add('GET', '/admin/change-password', [authcontroller::class, 'showchangePassword']);
$router->add('POST', '/admin/change-password', [authcontroller::class, 'updatepassword']);

// Jalankan Router
$router->resolve($request);