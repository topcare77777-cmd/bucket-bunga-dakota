<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_id'])) {
    header("Location: /login.php");
    exit;
}

require_once __DIR__ . '/../config/database.php';

$admin_id = $_SESSION['admin_id'];
$action = $_POST['action'] ?? '';

// 1. PROSES UPDATE NOMOR HP
if ($action === 'update_phone') {
    $phone = trim($_POST['phone'] ?? '');

    if (empty($phone)) {
        header("Location: /admin/settings.php?error=Nomor HP tidak boleh kosong");
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE users SET phone = ? WHERE id = ?");
        $stmt->execute([$phone, $admin_id]);

        header("Location: /admin/settings.php?success=Nomor HP berhasil diperbarui!");
        exit;
    } catch (PDOException $e) {
        header("Location: /admin/settings.php?error=Gagal mengupdate nomor HP: " . $e->getMessage());
        exit;
    }
}

// 2. PROSES UPDATE PASSWORD
if ($action === 'update_password') {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($old_password) || empty($new_password)) {
        header("Location: /admin/settings.php?error=Semua field password harus diisi");
        exit;
    }

    if ($new_password !== $confirm_password) {
        header("Location: /admin/settings.php?error=Konfirmasi password baru tidak cocok");
        exit;
    }

    // Ambil password lama dari database
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$admin_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifikasi password lama
    if (!$user || !password_verify($old_password, $user['password'])) {
        header("Location: /admin/settings.php?error=Password lama Anda salah!");
        exit;
    }

    // Hash password baru & simpan
    $new_hash = password_hash($new_password, PASSWORD_BCRYPT);
    try {
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$new_hash, $admin_id]);

        header("Location: /admin/settings.php?success=Password berhasil diubah!");
        exit;
    } catch (PDOException $e) {
        header("Location: /admin/settings.php?error=Gagal mengubah password: " . $e->getMessage());
        exit;
    }
}

// Redirect jika tidak ada aksi
header("Location: /admin/settings.php");
exit;