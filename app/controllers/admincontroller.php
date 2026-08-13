<?php
declare(strict_types=1);

namespace app\controllers;

class admincontroller extends basecontroller
{
    public function index(): void
    {
        $viewFile = __DIR__ . '/../../resources/views/admin/layout/master.php';

        if (file_exists($viewFile)) {
            $content = "<h2>Selamat Datang di Panel Admin Bucket Bunga Dakota</h2><p>Sistem Core Framework berjalan sempurna!</p>";
            require_once $viewFile;
        } else {
            echo "<h1>Bucket Bunga Dakota - Online</h1>";
            echo "<p>Core Framework & Routing Berhasil Diaktifkan!</p>";
        }
    }
}