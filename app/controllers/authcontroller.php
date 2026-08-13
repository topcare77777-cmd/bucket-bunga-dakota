<?php
declare(strict_types=1);

namespace app\controllers;

use app\core\database;
use PDO;

class authcontroller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = database::getconnection();
    }

    public function showlogin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!empty($_SESSION['is_admin'])) {
            header('Location: /admin/dashboard');
            exit;
        }

        require_once __DIR__ . '/../views/admin/login.php';
    }

    public function login(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        // Cek user di database
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fallback jika belum ada user di database, gunakan default 'admin' / 'dakota123'
        if (!$user && $username === 'admin' && $password === 'dakota123') {
            $_SESSION['is_admin'] = true;
            $_SESSION['admin_user'] = 'admin';
            header('Location: /admin/dashboard');
            exit;
        }

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['is_admin'] = true;
            $_SESSION['admin_user'] = $user['username'];
            header('Location: /admin/dashboard');
            exit;
        } else {
            $error = "Username atau password salah!";
            require_once __DIR__ . '/../views/admin/login.php';
        }
    }

    public function showchangePassword(): void
    {
        require_once __DIR__ . '/../views/admin/change_password.php';
    }

    public function updatepassword(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $username = $_SESSION['admin_user'] ?? 'admin';
        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($newPassword !== $confirmPassword) {
            $error = "Konfirmasi password baru tidak cocok!";
            require_once __DIR__ . '/../views/admin/change_password.php';
            return;
        }

        if (strlen($newPassword) < 6) {
            $error = "Password baru minimal 6 karakter!";
            require_once __DIR__ . '/../views/admin/change_password.php';
            return;
        }

        // Cek password lama
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $isOldValid = false;
        if ($user && password_verify($oldPassword, $user['password'])) {
            $isOldValid = true;
        } elseif (!$user && $oldPassword === 'dakota123') {
            $isOldValid = true;
        }

        if (!$isOldValid) {
            $error = "Password lama Anda salah!";
            require_once __DIR__ . '/../views/admin/change_password.php';
            return;
        }

        // Hash password baru dan simpan/update ke database
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        if ($user) {
            $updateStmt = $this->db->prepare("UPDATE users SET password = :password WHERE username = :username");
            $updateStmt->execute([':password' => $hashedPassword, ':username' => $username]);
        } else {
            $insertStmt = $this->db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
            $insertStmt->execute([':username' => $username, ':password' => $hashedPassword]);
        }

        $success = "Password berhasil diperbarui! Silakan gunakan password baru ini untuk login berikutnya.";
        require_once __DIR__ . '/../views/admin/change_password.php';
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION['is_admin']);
        unset($_SESSION['admin_user']);
        session_destroy();

        header('Location: /login');
        exit;
    }
}