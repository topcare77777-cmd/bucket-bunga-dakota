<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🌸</text></svg>">
    <title>Dashboard - Dakota Admin</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        body { display: flex; min-height: 100vh; background-color: #f8fafc; color: #334155; }
        .admin-sidebar { width: 260px; background-color: #0f172a; color: #f8fafc; display: flex; flex-direction: column; flex-shrink: 0; min-height: 100vh; }
        .sidebar-brand { padding: 1.5rem; font-size: 1.25rem; font-weight: 700; border-bottom: 1px solid #1e293b; color: #ffffff; }
        .sidebar-nav { flex: 1; padding: 1rem 0; }
        .sidebar-nav ul { list-style: none; }
        .menu-title { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; padding: 1rem 1.5rem 0.5rem 1.5rem; }
        .sidebar-nav a { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.5rem; color: #cbd5e1; text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: all 0.2s ease; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background-color: #1e293b; color: #38bdf8; }
        .sidebar-footer { padding: 1.25rem 1.5rem; border-top: 1px solid #1e293b; }
        .sidebar-footer a { color: #f87171; text-decoration: none; font-size: 0.9rem; font-weight: 600; }
        .admin-main { flex: 1; padding: 2rem; overflow-y: auto; }
        .dashboard-header { margin-bottom: 2rem; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: #ffffff; padding: 1.5rem; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <aside class="admin-sidebar">
        <div class="sidebar-brand">Dakota Admin</div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="/admin/dashboard" class="active">📊 Dashboard</a></li>
                <li class="menu-title">MANAJEMEN KATALOG</li>
                <li><a href="/admin/products">🌸 Daftar Produk</a></li>
                <li><a href="/admin/products/create">➕ Tambah & Unggah Produk</a></li>
                <li class="menu-title">PENGATURAN AKUN</li>
                <li><a href="/admin/change-contact">📱 Ganti Kontak HP</a></li>
                <li><a href="/admin/change-password">🔒 Ganti Password</a></li>
                <li class="menu-title">AKSES UTAMA</li>
                <li><a href="/" target="_blank">🛍️ Lihat Toko Pembeli</a></li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="/admin/logout">🚪 Keluar / Logout</a>
        </div>
    </aside>

    <main class="admin-main">
        <div class="dashboard-header">
            <span style="color: #64748b; font-size: 0.875rem; font-weight: 500;">Panel Kontrol</span>
            <h1 style="font-size: 1.75rem; font-weight: 700; color: #0f172a; margin-top: 0.25rem;">Selamat Datang di Admin Panel</h1>
        </div>

        <div class="stats-grid">
            <!-- TOTAL PRODUK -->
            <div class="stat-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; letter-spacing: 0.05em;">TOTAL PRODUK</span>
                    <span style="font-size: 1.25rem;">💐</span>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: #0f172a;"><?= htmlspecialchars((string)($totalProducts ?? '0')) ?></div>
            </div>

            <!-- PENGUNJUNG TOKO -->
            <div class="stat-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; letter-spacing: 0.05em;">PENGUNJUNG TOKO</span>
                    <span style="font-size: 1.25rem;">👀</span>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: #0f172a;"><?= htmlspecialchars((string)($totalVisitors ?? '0')) ?></div>
            </div>

            <!-- KLIK MEMESAN (WA) -->
            <div class="stat-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; letter-spacing: 0.05em;">KLIK MEMESAN (WA)</span>
                    <span style="font-size: 1.25rem;">💬</span>
                </div>
                <div style="font-size: 2rem; font-weight: 700; color: #0f172a;"><?= htmlspecialchars((string)($waClicks ?? '0')) ?></div>
            </div>

            <!-- STATUS SISTEM -->
            <div class="stat-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; letter-spacing: 0.05em;">STATUS SISTEM</span>
                </div>
                <div style="font-size: 1rem; font-weight: 600; color: #16a34a; display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem;">
                    <span style="display: inline-block; width: 8px; height: 8px; background: #16a34a; border-radius: 50%;"></span> Online / Normal
                </div>
            </div>
        </div>

        <footer style="margin-top: 3rem; color: #94a3b8; font-size: 0.875rem;">
            &copy; <?= date('Y') ?> Bucket Bunga Dakota. All rights reserved.
        </footer>
    </main>

</body>
</html>