<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Produk - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 font-sans text-slate-800">
    <div class="flex min-h-screen">
        
        <aside class="w-64 bg-slate-900 text-white flex flex-col justify-between p-4">
            <div>
                <h1 class="text-xl font-bold mb-8 px-2 tracking-wide">Dakota Admin</h1>
                <nav class="space-y-1">
                    <a href="/admin/dashboard" class="block py-2.5 px-4 rounded hover:bg-slate-800 transition">Dashboard</a>
                    <div class="pt-4 pb-1">
                        <span class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Manajemen Katalog</span>
                    </div>
                    <a href="/admin/products" class="block py-2.5 px-4 rounded bg-slate-800 font-medium">Daftar Produk</a>
                    <a href="/admin/products/create" class="block py-2.5 px-4 rounded hover:bg-slate-800 transition">Tambah & Unggah Produk</a>
                    <div class="pt-4 pb-1">
                        <span class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Pengaturan</span>
                    </div>
                    <a href="/admin/change-contact" class="block py-2.5 px-4 rounded hover:bg-slate-800 transition">Kontak WhatsApp</a>
                    <a href="/admin/change-password" class="block py-2.5 px-4 rounded hover:bg-slate-800 transition">Ganti Password</a>
                </nav>
            </div>
            <div>
                <a href="/admin/logout" class="block py-2.5 px-4 rounded text-red-400 hover:bg-slate-800 hover:text-red-300 transition text-sm">Keluar (Logout)</a>
            </div>
        </aside>

        <main class="flex-1 p-8">
            <header class="flex justify-between items-center mb-8">
                <div>
                    <span class="text-sm text-slate-500">Panel Kontrol</span>
                    <h2 class="text-2xl font-bold text-slate-900 mt-1">Daftar Produk Bucket Bunga</h2>
                </div>
                <a href="/admin/products/create" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg shadow hover:shadow-md transition">
                    + Tambah Produk Baru
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

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
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
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-4">
                                        <?php if (!empty($imgSrc)): ?>
                                            <img src="<?= htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8') ?>" 
                                                 alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>" 
                                                 class="w-14 h-14 object-cover rounded-lg border border-slate-200 shadow-sm"
                                                 loading="lazy">
                                        <?php else: ?>
                                            <span class="text-xs text-slate-400 italic">Tanpa Gambar</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 font-semibold text-slate-800">
                                        <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td class="p-4 text-slate-600">
                                        Rp <?= number_format((float) $product['price'], 0, ',', '.') ?>
                                    </td>
                                    <td class="p-4 text-slate-600">
                                        <?= (int) ($product['stock'] ?? 0) ?> pcs
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusBadge ?>">
                                            <?= strtoupper(htmlspecialchars($status, ENT_QUOTES, 'UTF-8')) ?>
                                        </span>
                                    </td>
                                    <td class="p-4 text-right space-x-2">
                                        <a href="/admin/products/edit?id=<?= (int) $product['id'] ?>" class="text-blue-600 hover:text-blue-800 font-medium text-xs">Edit</a>
                                        <form action="/admin/products/delete" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                            <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-xs">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400">
                                    Belum ada data produk. Klik menu "Tambah & Unggah Produk" untuk menambahkan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>