<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Dashboard - Dakota Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-rose-50 font-sans text-slate-800 selection:bg-rose-200">
    <div class="flex flex-col md:flex-row min-h-screen w-full">
        
        <!-- Sidebar -->
        <aside class="w-full md:w-64 bg-slate-900 text-white flex flex-col flex-shrink-0 md:min-h-screen p-4">
            <div class="mb-8">
                <h1 class="text-xl font-bold px-2 tracking-wide">Dakota Admin</h1>
            </div>
            <nav class="space-y-1 flex-1">
                <a href="/admin/dashboard" class="block py-2.5 px-4 rounded bg-slate-800 text-sky-400 font-medium transition">📊 Dashboard</a>
                
                <div class="pt-4 pb-1">
                    <span class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Manajemen Katalog</span>
                </div>
                <a href="/admin/products" class="block py-2.5 px-4 rounded hover:bg-slate-800 transition">🌸 Daftar Produk</a>
                <a href="/admin/products/create" class="block py-2.5 px-4 rounded hover:bg-slate-800 transition">➕ Tambah Produk</a>
                
                <div class="pt-4 pb-1">
                    <span class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Pengaturan</span>
                </div>
                <a href="/admin/change-contact" class="block py-2.5 px-4 rounded hover:bg-slate-800 transition">📱 Ganti Kontak HP</a>
                <a href="/admin/change-password" class="block py-2.5 px-4 rounded hover:bg-slate-800 transition">🔒 Ganti Password</a>
                
                <div class="pt-4 pb-1">
                    <span class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Akses Utama</span>
                </div>
                <a href="/" target="_blank" class="block py-2.5 px-4 rounded hover:bg-slate-800 transition">🛍️ Lihat Toko Pembeli</a>
            </nav>
            <div class="mt-4 pt-4 border-t border-slate-800">
                <a href="/logout" class="block py-2.5 px-4 rounded text-red-400 hover:bg-slate-800 hover:text-red-300 transition text-sm">🚪 Keluar (Logout)</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-4 md:p-8 w-full max-w-full overflow-x-hidden">
            <header class="mb-8">
                <span class="text-sm text-slate-500 font-medium">Panel Kontrol</span>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-900 mt-1">Selamat Datang di Admin Panel</h1>
            </header>

            <?php if (!empty($_SESSION['flash_success'])): ?>
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-lg">
                    <?= htmlspecialchars($_SESSION['flash_success'], ENT_QUOTES, 'UTF-8') ?>
                </div>
                <?php unset($_SESSION['flash_success']); ?>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
                <!-- Total Produk -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Produk</span>
                        <span class="text-xl">💐</span>
                    </div>
                    <div class="text-3xl font-bold text-slate-800"><?= (int)($totalProducts ?? 0) ?></div>
                </div>

                <!-- Pengunjung Toko -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pengunjung Toko</span>
                        <span class="text-xl">👀</span>
                    </div>
                    <div class="text-3xl font-bold text-slate-800"><?= (int)($totalVisitors ?? 0) ?></div>
                </div>

                <!-- Klik Memesan WA -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Klik Memesan (WA)</span>
                        <span class="text-xl">💬</span>
                    </div>
                    <div class="text-3xl font-bold text-slate-800"><?= (int)($waClicks ?? 0) ?></div>
                </div>

                <!-- Status Sistem -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Status Sistem</span>
                    </div>
                    <div class="text-base font-bold text-emerald-600 flex items-center gap-2 mt-2">
                        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span> Online / Normal
                    </div>
                </div>
            </div>

            <footer class="mt-12 text-sm text-slate-400">
                &copy; <?= date('Y') ?> Bucket Bunga Dakota. All rights reserved.
            </footer>
        </main>
    </div>
</body>
</html>