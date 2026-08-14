<?php
declare(strict_types=1);

namespace app\controllers;

use app\core\database;

class authcontroller extends basecontroller
{
    /**
     * Tampilkan Halaman Login
     */
    public function showlogin(): void
    {
        if (!empty($_SESSION['is_admin'])) {
            header('Location: /admin/dashboard');
            exit;
        }

        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);

        require_once __DIR__ . '/../views/admin/login.php';
    }

    /**
     * Proses Login Admin (Mendukung PostgreSQL & MySQL)
     */
    public function login(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            $_SESSION['login_error'] = 'Username dan password wajib diisi!';
            header('Location: /login');
            exit;
        }

        try {
            $db = database::getconnection();

            $stmt = $db->prepare("SELECT * FROM admins WHERE username = :username LIMIT 1");
            $stmt->execute(['username' => $username]);
            $admin = $stmt->fetch(\PDO::FETCH_ASSOC);

            $loginSuccess = false;

            if ($admin) {
                // 1. Verifikasi dengan enkripsi Bcrypt
                if (password_verify($password, $admin['password'])) {
                    $loginSuccess = true;
                } 
                // 2. Fallback jika akun default admin / admin123
                elseif ($admin['password'] === $password || ($username === 'admin' && $password === 'admin123')) {
                    $loginSuccess = true;
                    $newHash = password_hash($password, PASSWORD_BCRYPT);
                    $db->prepare("UPDATE admins SET password = :pass WHERE id = :id")->execute([
                        'pass' => $newHash,
                        'id'   => $admin['id']
                    ]);
                }
            } else {
                // Jika data admin belum ada di database, otomatis buatkan 1 akun pertama
                if ($username === 'admin' && $password === 'admin123') {
                    $newHash = password_hash('admin123', PASSWORD_BCRYPT);
                    $db->prepare("INSERT INTO admins (username, password, phone) VALUES ('admin', :pass, '081234567890')")->execute([
                        'pass' => $newHash
                    ]);
                    
                    $checkStmt = $db->prepare("SELECT * FROM admins WHERE username = 'admin' LIMIT 1");
                    $checkStmt->execute();
                    $admin = $checkStmt->fetch(\PDO::FETCH_ASSOC);
                    $loginSuccess = true;
                }
            }

            if ($loginSuccess && $admin) {
                $_SESSION['is_admin'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                header('Location: /admin/dashboard');
                exit;
            }
        } catch (\Throwable $e) {
            $_SESSION['login_error'] = 'Terjadi kesalahan sistem: ' . $e->getMessage();
            header('Location: /login');
            exit;
        }

        $_SESSION['login_error'] = 'Username atau password salah!';
        header('Location: /login');
        exit;
    }

    /**
     * Logout Admin
     */
    public function logout(): void
    {
        unset($_SESSION['is_admin'], $_SESSION['admin_id'], $_SESSION['admin_username']);
        session_destroy();
        header('Location: /login');
        exit;
    }

    /**
     * Tampilkan Halaman Ganti Kontak HP
     */
    public function showChangeContact(): void
    {
        $currentPhone = '';
        try {
            $db = database::getconnection();
            $adminId = $_SESSION['admin_id'] ?? null;
            
            if ($adminId) {
                $stmt = $db->prepare("SELECT phone FROM admins WHERE id = :id LIMIT 1");
                $stmt->execute(['id' => $adminId]);
            } else {
                $stmt = $db->query("SELECT phone FROM admins ORDER BY id ASC LIMIT 1");
            }

            $admin = $stmt->fetch(\PDO::FETCH_ASSOC);
            $currentPhone = $admin['phone'] ?? '';
        } catch (\Throwable $e) {
            $currentPhone = '';
        }

        $success = $_SESSION['flash_success'] ?? null;
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        require_once __DIR__ . '/../views/admin/change_contact.php';
    }

    /**
     * Proses Simpan Kontak HP
     */
    public function updateContact(): void
    {
        $phone = trim($_POST['phone'] ?? '');

        if (empty($phone)) {
            $_SESSION['flash_error'] = 'Nomor HP tidak boleh kosong!';
            header('Location: /admin/change-contact');
            exit;
        }

        try {
            $db = database::getconnection();
            $adminId = $_SESSION['admin_id'] ?? null;

            if ($adminId) {
                $stmt = $db->prepare("UPDATE admins SET phone = :phone WHERE id = :id");
                $stmt->execute(['phone' => $phone, 'id' => $adminId]);
            } else {
                $stmt = $db->prepare("UPDATE admins SET phone = :phone WHERE id = (SELECT id FROM admins ORDER BY id ASC LIMIT 1)");
                $stmt->execute(['phone' => $phone]);
            }

            $_SESSION['flash_success'] = 'Nomor kontak WhatsApp berhasil diperbarui!';
        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = 'Gagal memperbarui nomor: ' . $e->getMessage();
        }

        header('Location: /admin/change-contact');
        exit;
    }

    /**
     * Tampilkan Halaman Ganti Password
     */
    public function showchangePassword(): void
    {
        $success = $_SESSION['flash_success'] ?? null;
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        require_once __DIR__ . '/../views/admin/change_password.php';
    }

    /**
     * Proses Ganti Password -> Otomatis Menuju Dashboard
     */
    public function updatepassword(): void
    {
        $oldPassword     = $_POST['old_password'] ?? '';
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($oldPassword) || empty($newPassword)) {
            $_SESSION['flash_error'] = 'Semua kolom password wajib diisi!';
            header('Location: /admin/change-password');
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['flash_error'] = 'Konfirmasi password baru tidak cocok!';
            header('Location: /admin/change-password');
            exit;
        }

        try {
            $db = database::getconnection();
            $adminId = $_SESSION['admin_id'] ?? null;
            
            if ($adminId) {
                $stmt = $db->prepare("SELECT * FROM admins WHERE id = :id LIMIT 1");
                $stmt->execute(['id' => $adminId]);
            } else {
                $stmt = $db->query("SELECT * FROM admins ORDER BY id ASC LIMIT 1");
            }

            $admin = $stmt->fetch(\PDO::FETCH_ASSOC);
            $oldValid = false;

            if ($admin) {
                if (password_verify($oldPassword, $admin['password']) || $admin['password'] === $oldPassword) {
                    $oldValid = true;
                }
            }

            if (!$oldValid) {
                $_SESSION['flash_error'] = 'Password lama Anda salah!';
                header('Location: /admin/change-password');
                exit;
            }

            $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
            $updateStmt = $db->prepare("UPDATE admins SET password = :password WHERE id = :id");
            $updateStmt->execute(['password' => $newHash, 'id' => $admin['id']]);

            $_SESSION['flash_success'] = 'Password berhasil diperbarui!';
            header('Location: /admin/dashboard');
            exit;
        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = 'Gagal mengubah password: ' . $e->getMessage();
            header('Location: /admin/change-password');
            exit;
        }
    }
}