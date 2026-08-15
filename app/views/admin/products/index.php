<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Daftar Produk - Admin Panel</title>
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
                <a href="/admin/products" class="block py-2.5 px-4 rounded bg-slate-800 text-sky-400 font-medium transition">🌸 Daftar Produk</a>
                <a href="/admin/products/create" class="block py-2.5 px-4 rounded hover:bg-slate-800 transition">➕ Tambah Produk</a>
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
            <header class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6 md:mb-8">
                <div>
                    <span class="text-sm text-slate-500 font-medium">Panel Kontrol</span>
                    <h2 class="text-2xl font-bold text-slate-900 mt-1">Daftar Produk Bucket Bunga</h2>
                </div>
                <a href="/admin/products/create" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2.5 rounded-lg shadow-sm transition text-center min-h-[44px] flex items-center justify-center">
                    + Tambah Produk
                </a>
            </header>

            <?php if (!empty($success)): ?>
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-lg">
                    <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 w-full overflow-hidden">
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse min-w-[600px]">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <th class="p-4">Gambar</th>
                                <th class="p-4">Nama Produk</th>
                                <th class="p-4">Harga</th>
                                <th class="p-4">Stok</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-sm">
                            <?php if (!empty($products)): ?>
                                <?php foreach ($products as $product): ?>
                                    <?php 
                                        $imgSrc = !empty($product['thumbnail_url']) ? $product['thumbnail_url'] : ($product['image_url'] ?? null);
                                        $status = strtolower((string) ($product['status'] ?? 'available'));
                                        
                                        $statusBadge = match($status) {
                                            'sold' => 'bg-red-100 text-red-800',
                                            'reserved' => 'bg-amber-100 text-amber-800',
                                            default => 'bg-emerald-100 text-emerald-800'
                                        };
                                    ?>
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="p-4">
                                            <?php if (!empty($imgSrc)): ?>
                                                <img src="<?= htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8') ?>" 
                                                     alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>" 
                                                     class="w-14 h-14 object-cover rounded-lg border border-slate-200 shadow-sm"
                                                     loading="lazy">
                                            <?php else: ?>
                                                <div class="w-14 h-14 bg-slate-100 rounded-lg flex items-center justify-center text-[10px] text-slate-400 italic border border-slate-200">Kosong</div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="p-4 font-bold text-slate-800">
                                            <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>
                                        </td>
                                        <td class="p-4 text-slate-600 font-medium">
                                            Rp <?= number_format((float) $product['price'], 0, ',', '.') ?>
                                        </td>
                                        <td class="p-4 text-slate-600">
                                            <?= (int) ($product['stock'] ?? 0) ?>
                                        </td>
                                        <td class="p-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold <?= $statusBadge ?>">
                                                <?= strtoupper(htmlspecialchars($status, ENT_QUOTES, 'UTF-8')) ?>
                                            </span>
                                        </td>
                                        <td class="p-4 text-right">
                                            <div class="flex items-center justify-end gap-3">
                                                <a href="/admin/products/edit?id=<?= (int) $product['id'] ?>" class="text-blue-600 hover:text-blue-800 font-bold text-xs p-2">Edit</a>
                                                <form action="/admin/products/delete" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus produk ini? Semua data dan foto akan dihapus permanen.');">
                                                    <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                                                    <button type="submit" class="text-red-600 hover:text-red-800 font-bold text-xs p-2">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400 font-medium">
                                        Belum ada data produk di katalog.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>