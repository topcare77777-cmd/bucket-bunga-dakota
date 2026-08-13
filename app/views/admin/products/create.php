<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk Baru - Admin Panel</title>
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
                    <a href="/admin/products/create" class="block py-2.5 px-4 rounded bg-slate-800 font-medium">Tambah & Unggah Produk</a>
                </nav>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 p-8">
            <header class="mb-8">
                <span class="text-sm text-slate-500">Panel Kontrol</span>
                <h2 class="text-2xl font-bold text-slate-900 mt-1">Tambah Produk Baru</h2>
            </header>

            <!-- Form Card -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-2xl">
                <form action="/admin/products/store" method="POST" enctype="multipart/form-data" class="space-y-6">
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Produk</label>
                        <input type="text" id="name" name="name" required placeholder="Contoh: Buket Bunga Mawar Merah"
                               class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="price" class="block text-sm font-medium text-slate-700 mb-1">Harga (Rp)</label>
                            <input type="number" id="price" name="price" required placeholder="150000" min="0" step="1000"
                                   class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        </div>
                        <div>
                            <label for="stock" class="block text-sm font-medium text-slate-700 mb-1">Stok (pcs)</label>
                            <input type="number" id="stock" name="stock" required placeholder="10" min="0" value="1"
                                   class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Deskripsi Produk</label>
                        <textarea id="description" name="description" rows="4" placeholder="Tuliskan deskripsi buket bunga..."
                                  class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"></textarea>
                    </div>

                    <div>
                        <label for="image" class="block text-sm font-medium text-slate-700 mb-1">Gambar Produk (JPG, PNG, WEBP)</label>
                        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" required
                               class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-slate-200 rounded-lg p-1">
                    </div>

                    <div class="flex items-center space-x-4 pt-4 border-t border-slate-100">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-lg shadow hover:shadow-md transition">
                            Simpan Produk
                        </button>
                        <a href="/admin/products" class="text-slate-500 hover:text-slate-700 text-sm font-medium">
                            Batal
                        </a>
                    </div>
                </form>
            </div>

            <footer class="mt-12 text-xs text-slate-400">
                &copy; 2026 Bucket Bunga Dakota. All rights reserved.
            </footer>
        </main>
    </div>
</body>
</html>