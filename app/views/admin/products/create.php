<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Tambah Produk - Admin Panel</title>
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
                <a href="/admin/dashboard" class="block py-2.5 px-4 rounded hover:bg-slate-800 transition">📊 Dashboard</a>
                <div class="pt-4 pb-1"><span class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Manajemen Katalog</span></div>
                <a href="/admin/products" class="block py-2.5 px-4 rounded hover:bg-slate-800 transition">🌸 Daftar Produk</a>
                <a href="/admin/products/create" class="block py-2.5 px-4 rounded bg-slate-800 text-sky-400 font-medium transition">➕ Tambah Produk</a>
                <div class="pt-4 pb-1"><span class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Pengaturan</span></div>
                <a href="/admin/change-contact" class="block py-2.5 px-4 rounded hover:bg-slate-800 transition">📱 Ganti Kontak HP</a>
                <a href="/admin/change-password" class="block py-2.5 px-4 rounded hover:bg-slate-800 transition">🔒 Ganti Password</a>
                <div class="pt-4 pb-1"><span class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Akses Utama</span></div>
                <a href="/" target="_blank" class="block py-2.5 px-4 rounded hover:bg-slate-800 transition">🛍️ Lihat Toko Pembeli</a>
            </nav>
            <div class="mt-4 pt-4 border-t border-slate-800">
                <a href="/logout" class="block py-2.5 px-4 rounded text-red-400 hover:bg-slate-800 transition text-sm">🚪 Keluar (Logout)</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-4 md:p-8 w-full max-w-full overflow-x-hidden">
            <header class="mb-6 md:mb-8">
                <span class="text-sm text-slate-500 font-medium">Panel Kontrol</span>
                <h2 class="text-2xl font-bold text-slate-900 mt-1">Tambah Produk Baru</h2>
            </header>

            <?php if (!empty($error)): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg max-w-3xl">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 md:p-8 max-w-3xl w-full">
                <form action="/admin/products/store" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nama Produk <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="Contoh: Buket Mawar Merah"
                               class="w-full px-4 py-3 min-h-[44px] border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-base">
                    </div>

                    <!-- Responsive Grid: Stack di mobile, bersampingan di desktop -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Harga (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="price" required min="0" placeholder="Contoh: 150000"
                                   class="w-full px-4 py-3 min-h-[44px] border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-base">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Stok (pcs) <span class="text-red-500">*</span></label>
                            <input type="number" name="stock" required min="0" value="10"
                                   class="w-full px-4 py-3 min-h-[44px] border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-base">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Status Produk</label>
                        <select name="status" class="w-full px-4 py-3 min-h-[44px] border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-base bg-white">
                            <option value="available">Tersedia (Available)</option>
                            <option value="reserved">Dipesan (Reserved)</option>
                            <option value="sold">Habis (Sold)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi Produk</label>
                        <textarea name="description" rows="4" placeholder="Tuliskan detail bunga, ukuran, dll..."
                                  class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-base"></textarea>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Upload Gambar <span class="text-red-500">*</span></label>
                        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" required
                               class="w-full text-sm text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200 border border-slate-300 rounded-lg p-1 bg-white cursor-pointer min-h-[44px]">
                        <p class="text-xs text-slate-500 mt-2">Format: JPG, PNG, WEBP. Maks: 2MB. Gambar akan otomatis dioptimasi ke WebP.</p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-3 pt-6 border-t border-slate-100">
                        <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-3 min-h-[44px] rounded-lg shadow-sm transition order-1 sm:order-none">
                            Simpan & Upload Produk
                        </button>
                        <a href="/admin/products" class="w-full sm:w-auto text-center text-slate-500 hover:text-slate-800 font-bold px-6 py-3 min-h-[44px] order-2 sm:order-none">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>