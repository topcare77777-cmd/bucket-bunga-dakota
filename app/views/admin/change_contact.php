<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Ganti Kontak - Dakota Admin</title>
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
                <a href="/admin/products/create" class="block py-2.5 px-4 rounded hover:bg-slate-800 transition">➕ Tambah Produk</a>
                <div class="pt-4 pb-1"><span class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Pengaturan</span></div>
                <a href="/admin/change-contact" class="block py-2.5 px-4 rounded bg-slate-800 text-sky-400 font-medium transition">📱 Ganti Kontak HP</a>
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
                <span class="text-sm text-slate-500 font-medium">Pengaturan Toko</span>
                <h1 class="text-2xl font-bold text-slate-900 mt-1">📱 Ganti Nomor Kontak WhatsApp</h1>
            </header>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 md:p-8 max-w-lg w-full">
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

                <form action="/admin/change-contact" method="POST" class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nomor WhatsApp / HP Toko</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars((string)($currentPhone ?? '')) ?>" placeholder="Contoh: 081234567890" required
                               class="w-full px-4 py-3 min-h-[44px] border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-base">
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">Nomor tujuan untuk menerima pesanan chat WhatsApp dari pembeli. Pastikan aktif.</p>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 min-h-[44px] rounded-lg shadow-sm transition">
                        Simpan Nomor Kontak
                    </button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>