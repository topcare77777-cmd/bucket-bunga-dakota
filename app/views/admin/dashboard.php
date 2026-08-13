<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 font-sans text-slate-800">
    <div class="flex min-h-screen">
        
        <!-- Sidebar Navigation -->
        <aside class="w-64 bg-slate-900 text-white flex flex-col justify-between p-4">
            <div>
                <h1 class="text-xl font-bold mb-8 px-2 tracking-wide">Dakota Admin</h1>
                <nav class="space-y-1">
                    <a href="/admin/dashboard" class="block py-2.5 px-4 rounded bg-slate-800 font-medium">Dashboard</a>
                    <div class="pt-4 pb-1">
                        <span class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Manajemen Katalog</span>
                    </div>
                    <a href="/admin/products" class="block py-2.5 px-4 rounded hover:bg-slate-800 transition">Daftar Produk</a>
                    <a href="/admin/products/create" class="block py-2.5 px-4 rounded hover:bg-slate-800 transition">Tambah & Unggah Produk</a>
                    
                    <div class="pt-4 pb-1">
                        <span class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Akses Utama</span>
                    </div>
                    <a href="/" target="_blank" class="block py-2.5 px-4 rounded hover:bg-rose-900/50 text-rose-300 font-medium transition flex items-center gap-2">
                        <span>🛍️</span> Lihat Toko Pembeli
                    </a>
                </nav>
            </div>

            <div class="pt-4 border-t border-slate-800">
                <a href="/logout" class="block py-2 px-4 rounded text-xs font-medium text-red-400 hover:bg-red-950/30 transition">
                    🚪 Keluar / Logout
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 p-8">
            <header class="mb-8">
                <span class="text-sm text-slate-500">Panel Kontrol</span>
                <h2 class="text-2xl font-bold text-slate-900 mt-1">Selamat Datang di Admin Panel</h2>
            </header>

            <!-- Stat Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                
                <!-- Card 1: Total Produk -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase text-slate-400">Total Produk</span>
                        <span class="text-xl">💐</span>
                    </div>
                    <p class="text-3xl font-bold text-slate-800 mt-2"><?= $totalProducts ?? 0 ?></p>
                </div>

                <!-- Card 2: Pengunjung Toko -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase text-slate-400">Pengunjung Toko</span>
                        <span class="text-xl">👀</span>
                    </div>
                    <p class="text-3xl font-bold text-blue-600 mt-2"><?= $stats['visitors'] ?? 0 ?></p>
                </div>

                <!-- Card 3: Klik Memesan WA -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase text-slate-400">Klik Memesan (WA)</span>
                        <span class="text-xl">💬</span>
                    </div>
                    <p class="text-3xl font-bold text-emerald-600 mt-2"><?= $stats['orders'] ?? 0 ?></p>
                </div>

                <!-- Card 4: Status Sistem -->
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <span class="text-xs font-semibold uppercase text-slate-400">Status Sistem</span>
                    <p class="text-base font-semibold text-emerald-600 mt-3">● Online / Normal</p>
                </div>

            </div>

            <footer class="mt-12 text-xs text-slate-400">
                &copy; 2026 Bucket Bunga Dakota. All rights reserved.
            </footer>
        </main>
    </div>
</body>
</html>