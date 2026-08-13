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

$router = new router();
$request = new request();

// Route Publik
$router->add('GET', '/', [productcontroller::class, 'home']);
$router->add('GET', '/login', [authcontroller::class, 'showlogin']);
$router->add('POST', '/login', [authcontroller::class, 'login']);
$router->add('GET', '/logout', [authcontroller::class, 'logout']);

// Proteksi Halaman Admin (Satpam Akses)
$uri = $_SERVER['REQUEST_URI'] ?? '/';
if (strpos($uri, '/admin') === 0) {
    if (empty($_SESSION['is_admin'])) {
        header('Location: /login');
        exit;
    }
}

// Route Admin (Hanya bisa dibuka jika sudah login)
$router->add('GET', '/admin/dashboard', [productcontroller::class, 'dashboard']);
$router->add('GET', '/admin/products', [productcontroller::class, 'index']);
$router->add('GET', '/admin/products/create', [productcontroller::class, 'create']);
$router->add('POST', '/admin/products/store', [productcontroller::class, 'store']);
$router->add('GET', '/admin/products/edit', [productcontroller::class, 'edit']);
$router->add('POST', '/admin/products/update', [productcontroller::class, 'update']);
$router->add('POST', '/admin/products/delete', [productcontroller::class, 'delete']);

$router->resolve($request);