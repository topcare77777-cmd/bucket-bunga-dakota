<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Password - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 font-sans text-slate-800">
    <div class="flex min-h-screen">
        
        <!-- Sidebar Navigation -->
        <aside class="w-64 bg-slate-900 text-white flex flex-col justify-between p-4">
            <div>
                <h1 class="text-xl font-bold mb-8 px-2 tracking-wide">Dakota Admin</h1>
                <nav class="space-y-1">
                    <a href="/admin/dashboard" class="block py-2.5 px-4 rounded hover:bg-slate-800 transition">Dashboard</a>
                    <div class="pt-4 pb-1">
                        <span class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Manajemen Katalog</span>
                    </div>
                    <a href="/admin/products" class="block py-2.5 px-4 rounded hover:bg-slate-800 transition">Daftar Produk</a>
                    <a href="/admin/products/create" class="block py-2.5 px-4 rounded hover:bg-slate-800 transition">Tambah & Unggah Produk</a>
                    
                    <div class="pt-4 pb-1">
                        <span class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Pengaturan</span>
                    </div>
                    <a href="/admin/change-password" class="block py-2.5 px-4 rounded bg-slate-800 font-medium">Ubah Password Admin</a>

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
                <span class="text-sm text-slate-500">Pengaturan Akun</span>
                <h2 class="text-2xl font-bold text-slate-900 mt-1">Ubah Password Admin</h2>
            </header>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-md">
                <?php if (!empty($error)): ?>
                    <div class="bg-red-50 text-red-600 text-xs p-3 rounded-lg mb-4 border border-red-200">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="bg-emerald-50 text-emerald-600 text-xs p-3 rounded-lg mb-4 border border-emerald-200">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <form action="/admin/change-password" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Password Lama</label>
                        <input type="password" name="old_password" required placeholder="••••••••"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Password Baru</label>
                        <input type="password" name="new_password" required placeholder="••••••••"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="confirm_password" required placeholder="••••••••"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-sm">
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg shadow transition text-sm mt-2">
                        Simpan Password Baru
                    </button>
                </form>
            </div>

            <footer class="mt-12 text-xs text-slate-400">
                &copy; 2026 Bucket Bunga Dakota. All rights reserved.
            </footer>
        </main>
    </div>
</body>
</html>