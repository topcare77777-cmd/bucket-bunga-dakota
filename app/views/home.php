<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Bucket Bunga Dakota - Toko Buket Bunga Segar & Custom</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-rose-50/40 text-slate-800 font-sans antialiased selection:bg-rose-200">

    <!-- Header / Navbar Mobile & Desktop -->
    <header class="bg-white/90 backdrop-blur-md sticky top-0 z-50 border-b border-rose-100 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 py-3 sm:py-4 flex justify-between items-center">
            <a href="/" class="text-xl sm:text-2xl font-bold text-rose-600 tracking-tight flex items-center gap-1.5">
                <span>🌸</span>
                <span>Bucket Bunga Dakota</span>
            </a>
            <a href="/admin/dashboard" class="text-xs font-semibold text-slate-500 hover:text-rose-600 transition bg-slate-100 hover:bg-rose-50 px-3 py-1.5 rounded-full border border-slate-200">
                Panel Admin →
            </a>
        </div>
    </header>

    <!-- Hero Banner Responsif -->
    <section class="bg-gradient-to-b from-rose-100/70 to-transparent py-8 sm:py-14 text-center px-4">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-2xl sm:text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                Hadiah Terindah untuk Momen Spesial ❤️
            </h1>
            <p class="text-slate-600 mt-2 sm:mt-3 text-xs sm:text-base leading-relaxed">
                Koleksi buket bunga wisuda, ulang tahun, dan acara istimewa dengan desain elegan & dapat disesuaikan.
            </p>
        </div>
    </section>

    <!-- Product Catalog Section -->
    <main class="max-w-6xl mx-auto px-3 sm:px-4 py-4 sm:py-8 mb-16">
        <h2 class="text-lg sm:text-xl font-bold text-slate-800 mb-4 sm:mb-6 flex items-center gap-2">
            <span>💐</span> Katalog Buket Bunga
        </h2>

        <?php if (!empty($products)): ?>
            <!-- Grid Responsif: 2 Kolom di HP, 3 Kolom di Tablet, 4 Kolom di Desktop -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 sm:gap-6">
                <?php foreach ($products as $product): ?>
                    <?php 
                        // Penentuan URL Gambar Berdasarkan Prioritas Pipeline Baru vs Legacy
                        $imgSrc = null;
                        if (!empty($product['thumbnail_url'])) {
                            $imgSrc = $product['thumbnail_url']; // Prioritas 1: Thumbnail Supabase
                        } elseif (!empty($product['image_url'])) {
                            $imgSrc = $product['image_url']; // Prioritas 2: Main Image Supabase
                        } elseif (!empty($product['image'])) {
                            $imgSrc = '/uploads/products/' . $product['image']; // Prioritas 3: Fallback Legacy Lokal
                        }
                    ?>
                    <div class="bg-white rounded-xl sm:rounded-2xl border border-rose-100 shadow-sm hover:shadow-md transition duration-200 overflow-hidden flex flex-col justify-between">
                        <div>
                            <!-- Product Image -->
                            <div class="h-36 sm:h-52 bg-slate-100 overflow-hidden relative">
                                <?php if (!empty($imgSrc)): ?>
                                    <img src="<?= htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8') ?>" 
                                         alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>" 
                                         class="w-full h-full object-cover hover:scale-105 transition duration-300">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-slate-400 text-[10px] sm:text-xs italic p-2 text-center">
                                        Gambar belum tersedia
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Product Info -->
                            <div class="p-2.5 sm:p-4">
                                <h3 class="font-bold text-slate-800 text-xs sm:text-base line-clamp-1">
                                    <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>
                                </h3>
                                <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5 sm:mt-1 line-clamp-2">
                                    <?= htmlspecialchars($product['description'] ?? 'Buket bunga cantik berkualitas tinggi.', ENT_QUOTES, 'UTF-8') ?>
                                </p>
                                <div class="mt-2 sm:mt-3 flex flex-col sm:flex-row sm:items-baseline justify-between gap-1">
                                    <span class="text-sm sm:text-lg font-extrabold text-rose-600">
                                        Rp <?= number_format((float) $product['price'], 0, ',', '.') ?>
                                    </span>
                                    <span class="text-[10px] sm:text-[11px] text-slate-400 font-medium">
                                        Stok: <?= (int) $product['stock'] ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Order via WhatsApp Button -->
                        <div class="p-2.5 sm:p-4 pt-0">
                            <?php 
                                $whatsappNumber = "6281234567890"; // Ganti dengan nomor WhatsApp Anda
                                $pesanWa = rawurlencode("Halo, saya ingin memesan buket: " . $product['name'] . " (Rp " . number_format((float)$product['price'], 0, ',', '.') . ")");
                                $waUrl = "https://wa.me/{$whatsappNumber}?text={$pesanWa}";
                            ?>
                            <a href="<?= $waUrl ?>" target="_blank" onclick="trackOrder(<?= $product['id'] ?>)"
                               class="w-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-medium text-[11px] sm:text-xs py-2 sm:py-2.5 px-2 sm:px-4 rounded-lg sm:rounded-xl flex items-center justify-center gap-1.5 transition shadow-sm touch-manipulation">
                                <span>💬</span>
                                <span>Pesan WA</span>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-12 text-slate-400 bg-white rounded-2xl border border-dashed border-rose-200 text-xs sm:text-sm">
                Belum ada produk yang ditampilkan di katalog.
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-rose-100 py-6 text-center text-[11px] sm:text-xs text-slate-400">
        &copy; 2026 Bucket Bunga Dakota. All rights reserved.
    </footer>

    <script>
    function trackOrder(productId) {
        const formData = new FormData();
        formData.append('product_id', productId);
        navigator.sendBeacon('/api/track-order', formData);
    }
    </script>

</body>
</html>