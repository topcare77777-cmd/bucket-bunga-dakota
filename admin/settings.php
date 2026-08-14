<?php
// Pastikan admin sudah login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_id'])) {
    header("Location: /login.php");
    exit;
}

require_once __DIR__ . '/../config/database.php'; // Sesuaikan path koneksi database Anda

$admin_id = $_SESSION['admin_id'];
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';

// Ambil data admin saat ini
$stmt = $pdo->prepare("SELECT username, phone FROM users WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun - Bucket Bunga Dakota</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">⚙️ Pengaturan Profil & Keamanan</h1>

        <!-- Notifikasi -->
        <?php if ($success): ?>
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- FORM 1: GANTI NOMOR HP / WA -->
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">📱 Ganti Nomor HP / WhatsApp</h2>
                <form action="/admin/update_profile.php" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="update_phone">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" value="<?= htmlspecialchars($admin['username'] ?? '') ?>" disabled class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp / HP</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($admin['phone'] ?? '') ?>" placeholder="Contoh: 081234567890" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:outline-none">
                        <small class="text-gray-500 text-xs">Gunakan format 08xx atau 628xx</small>
                    </div>

                    <button type="submit" class="w-full bg-pink-600 hover:bg-pink-700 text-white font-medium py-2 rounded-lg transition duration-200">
                        Simpan Nomor HP
                    </button>
                </form>
            </div>

            <!-- FORM 2: GANTI PASSWORD -->
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h2 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">🔒 Ganti Password</h2>
                <form action="/admin/update_profile.php" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="update_password">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Lama</label>
                        <input type="password" name="old_password" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                        <input type="password" name="new_password" minlength="6" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="confirm_password" minlength="6" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:outline-none">
                    </div>

                    <button type="submit" class="w-full bg-gray-800 hover:bg-black text-white font-medium py-2 rounded-lg transition duration-200">
                        Ubah Password
                    </button>
                </form>
            </div>

        </div>
    </div>

</body>
</html>