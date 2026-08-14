<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🌸</text></svg>">
    <title>Ganti Kontak - Dakota Admin</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        body { display: flex; min-height: 100vh; background-color: #f8fafc; color: #334155; }
        .admin-sidebar { width: 260px; background-color: #0f172a; color: #f8fafc; display: flex; flex-direction: column; flex-shrink: 0; min-height: 100vh; }
        .sidebar-brand { padding: 1.5rem; font-size: 1.25rem; font-weight: 700; border-bottom: 1px solid #1e293b; color: #ffffff; }
        .sidebar-nav { flex: 1; padding: 1rem 0; }
        .sidebar-nav ul { list-style: none; }
        .menu-title { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; padding: 1rem 1.5rem 0.5rem 1.5rem; }
        .sidebar-nav a { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.5rem; color: #cbd5e1; text-decoration: none; font-size: 0.9rem; font-weight: 500; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background-color: #1e293b; color: #38bdf8; }
        .sidebar-footer { padding: 1.25rem 1.5rem; border-top: 1px solid #1e293b; }
        .sidebar-footer a { color: #f87171; text-decoration: none; font-size: 0.9rem; font-weight: 600; }
        .admin-main { flex: 1; padding: 2rem; overflow-y: auto; }
        .form-card { max-width: 550px; background: #ffffff; padding: 2rem; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; font-size: 0.875rem; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; }
        .form-group input { width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; }
        .btn-submit { background: #0284c7; color: #ffffff; padding: 0.75rem 1.5rem; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.95rem; }
        .alert-success { background: #def7ec; color: #03543f; padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1.25rem; font-size: 0.9rem; }
        .alert-error { background: #fde8e8; color: #9b1c1c; padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1.25rem; font-size: 0.9rem; }
    </style>
</head>
<body>

    <aside class="admin-sidebar">
        <div class="sidebar-brand">Dakota Admin</div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="/admin/dashboard">📊 Dashboard</a></li>
                <li class="menu-title">MANAJEMEN KATALOG</li>
                <li><a href="/admin/products">🌸 Daftar Produk</a></li>
                <li><a href="/admin/products/create">➕ Tambah & Unggah Produk</a></li>
                <li class="menu-title">PENGATURAN AKUN</li>
                <li><a href="/admin/change-contact" class="active">📱 Ganti Kontak HP</a></li>
                <li><a href="/admin/change-password">🔒 Ganti Password</a></li>
                <li class="menu-title">AKSES UTAMA</li>
                <li><a href="/" target="_blank">🛍️ Lihat Toko Pembeli</a></li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="/logout">🚪 Keluar / Logout</a>
        </div>
    </aside>

    <main class="admin-main">
        <div style="margin-bottom: 1.5rem;">
            <span style="color: #64748b; font-size: 0.875rem;">Pengaturan Toko</span>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin-top: 0.25rem;">📱 Ganti Nomor Kontak WhatsApp</h1>
        </div>

        <div class="form-card">
            <?php if (!empty($success)): ?>
                <div class="alert-success"><?= htmlspecialchars((string)$success) ?></div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert-error"><?= htmlspecialchars((string)$error) ?></div>
            <?php endif; ?>

            <form action="/admin/change-contact" method="POST">
                <div class="form-group">
                    <label>Nomor WhatsApp / HP Toko</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars((string)($currentPhone ?? '')) ?>" placeholder="Contoh: 081234567890" required>
                    <small style="color: #64748b; font-size: 0.8rem; display: block; margin-top: 0.35rem;">Nomor tujuan untuk menerima pesanan chat WhatsApp dari pembeli.</small>
                </div>

                <button type="submit" class="btn-submit">Simpan Nomor Kontak</button>
            </form>
        </div>
    </main>

</body>
</html>