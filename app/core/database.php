<?php
declare(strict_types=1);

namespace app\core;

use PDO;
use PDOException;
use RuntimeException;

class database
{
    private static ?PDO $connection = null;

    /**
     * Helper internal untuk membaca environment variable secara berurutan:
     * 1. getenv()
     * 2. $_ENV
     * 3. $_SERVER
     * 4. $default
     */
    private static function env(string $key, ?string $default = null): ?string
    {
        $val = getenv($key);
        if ($val !== false && $val !== '') {
            return (string) $val;
        }

        if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
            return (string) $_ENV[$key];
        }

        if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
            return (string) $_SERVER[$key];
        }

        return $default;
    }

    /**
     * Singleton instance koneksi database
     */
    public static function getconnection(): PDO
    {
        if (self::$connection === null) {
            self::$connection = self::createConnection();
        }

        return self::$connection;
    }

    /**
     * Factory pembuat koneksi database dengan isolasi ketat Production vs Local
     */
    private static function createConnection(): PDO
    {
        $isVercel = self::isVercelEnvironment();

        if ($isVercel) {
            return self::createProductionConnection();
        }

        return self::createLocalConnection();
    }

    /**
     * Deteksi lingkungan Vercel Serverless
     */
    private static function isVercelEnvironment(): bool
    {
        $vercelEnv = self::env('VERCEL');
        $vercelRegion = self::env('VERCEL_REGION');
        $vercelUrl = self::env('VERCEL_URL');

        if (!empty($vercelEnv) || !empty($vercelRegion) || !empty($vercelUrl)) {
            return true;
        }

        $serverName = $_SERVER['SERVER_NAME'] ?? '';
        $isLocalHost = in_array($serverName, ['localhost', '127.0.0.1'], true);

        // Jika bukan localhost dan ada konfigurasi cloud host, anggap environment cloud/production
        if (!$isLocalHost && !empty(self::env('DB_HOST'))) {
            return true;
        }

        return false;
    }

    /**
     * Inisialisasi koneksi PostgreSQL Supabase untuk Production (Vercel)
     * Tidak boleh ada silent fallback ke MySQL/Localhost.
     */
    private static function createProductionConnection(): PDO
    {
        $host = self::env('DB_HOST');
        $port = self::env('DB_PORT', '6543');
        $dbname = self::env('DB_NAME', 'postgres');
        $user = self::env('DB_USER');
        $pass = self::env('DB_PASS');

        // Dukungan connection URL (DATABASE_URL / POSTGRES_URL) jika parameter diskrit belum lengkap
        if (empty($host) || empty($user) || $pass === null) {
            $connUrl = self::env('DATABASE_URL') ?? self::env('POSTGRES_URL');
            if (!empty($connUrl)) {
                $parsed = parse_url($connUrl);
                if ($parsed !== false && isset($parsed['host'])) {
                    $host = $parsed['host'];
                    $port = isset($parsed['port']) ? (string) $parsed['port'] : '6543';
                    $dbname = isset($parsed['path']) ? ltrim($parsed['path'], '/') : 'postgres';
                    $user = $parsed['user'] ?? '';
                    $pass = $parsed['pass'] ?? '';
                }
            }
        }

        // Validasi parameter wajib production
        if (empty($host)) {
            throw new RuntimeException('[Database] Konfigurasi Production Gagal: DB_HOST tidak ditemukan pada environment Vercel.');
        }
        if (empty($user)) {
            throw new RuntimeException('[Database] Konfigurasi Production Gagal: DB_USER tidak ditemukan pada environment Vercel.');
        }
        if ($pass === null || $pass === '') {
            throw new RuntimeException('[Database] Konfigurasi Production Gagal: DB_PASS tidak ditemukan pada environment Vercel.');
        }

        $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT            => 10,
        ];

        try {
            // Diagnostic logging aman (tanpa kredensial sensitif)
            error_log(sprintf(
                '[Database] Inisialisasi Production | Driver: pgsql | Host: %s | Port: %s | DB: %s',
                $host,
                $port,
                $dbname
            ));

            return new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            error_log('[Database] PostgreSQL Supabase Connection Error: ' . $e->getMessage());
            throw new RuntimeException('Gagal terhubung ke database production PostgreSQL Supabase.');
        }
    }

    /**
     * Inisialisasi koneksi MySQL XAMPP untuk Local Development
     */
    private static function createLocalConnection(): PDO
    {
        $host = self::env('DB_HOST', '127.0.0.1');
        $port = self::env('DB_PORT', '3306');
        $dbname = self::env('DB_NAME', 'bucket_bunga_dakota');
        $user = self::env('DB_USER', 'root');
        $pass = self::env('DB_PASS', '');

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            return new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            error_log('[Database] Local MySQL Connection Error: ' . $e->getMessage());
            throw new RuntimeException('Gagal terhubung ke database MySQL lokal (XAMPP). Pastikan service MySQL berjalan.');
        }
    }
}