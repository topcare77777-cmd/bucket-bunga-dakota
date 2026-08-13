<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Admin Panel</title>
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
                    <a href="/admin/products" class="block py-2.5 px-4 rounded bg-slate-800 font-medium">Daftar Produk</a>
                    <a href="/admin/products/create" class="block py-2.5 px-4 rounded hover:bg-slate-800 transition">Tambah & Unggah Produk</a>
                </nav>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 p-8">
            <header class="mb-8">
                <span class="text-sm text-slate-500">Panel Kontrol</span>
                <h2 class="text-2xl font-bold text-slate-900 mt-1">Edit Produk</h2>
            </header>

            <!-- Form Card -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-2xl">
                <form action="/admin/products/update" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="id" value="<?= $product['id'] ?>">
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Produk</label>
                        <input type="text" id="name" name="name" required value="<?= htmlspecialchars($product['name']) ?>"
                               class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="price" class="block text-sm font-medium text-slate-700 mb-1">Harga (Rp)</label>
                            <input type="number" id="price" name="price" required value="<?= (float) $product['price'] ?>"
                                   class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                        </div>
                        <div>
                            <label for="stock" class="block text-sm font-medium text-slate-700 mb-1">Stok (pcs)</label>
                            <input type="number" id="stock" name="stock" required value="<?= (int) $product['stock'] ?>"
                                   class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Deskripsi Produk</label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Gambar Produk Saat Ini</label>
                        <?php if (!empty($product['image'])): ?>
                            <img src="/uploads/products/<?= htmlspecialchars($product['image']) ?>" class="w-20 h-20 object-cover rounded-lg mb-2 border">
                        <?php endif; ?>
                        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp"
                               class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border rounded-lg p-1">
                        <span class="text-xs text-slate-400">Pilih gambar baru hanya jika ingin mengganti foto lama.</span>
                    </div>

                    <div class="flex items-center space-x-4 pt-4 border-t border-slate-100">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-lg shadow transition">
                            Simpan Perubahan
                        </button>
                        <a href="/admin/products" class="text-slate-500 hover:text-slate-700 text-sm font-medium">Batal</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>