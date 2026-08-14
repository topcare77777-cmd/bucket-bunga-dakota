<?php
declare(strict_types=1);

namespace app\core;

use PDO;
use PDOException;

class database
{
    private static ?PDO $connection = null;

    /**
     * Mendapatkan instance koneksi PDO tunggal (Singleton)
     */
    public static function getconnection(): PDO
    {
        if (self::$connection === null) {
            // Deteksi apakah sedang berjalan di Localhost atau di Server Vercel Cloud
            $isLocal = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1'], true) 
                       || empty(getenv('DB_HOST'));

            if ($isLocal) {
                // ==========================================
                // 1. KONEKSI LOKAL (XAMPP / MySQL)
                // ==========================================
                $host   = '127.0.0.1';
                $port   = '3306';
                $dbname = 'bucket_bunga_dakota';
                $user   = 'root';
                $pass   = '';

                $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

                try {
                    self::$connection = new PDO($dsn, $user, $pass, [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]);
                } catch (PDOException $e) {
                    die("Koneksi Database Gagal (Lokal): " . $e->getMessage());
                }
            } else {
                // ==========================================
                // 2. KONEKSI ONLINE VERCEL (Supabase PostgreSQL)
                // ==========================================
                $host   = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? 'aws-0-ap-southeast-1.pooler.supabase.com');
                $port   = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? '6543');
                $dbname = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'postgres');
                $user   = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'postgres.vaoloqhyrdimththrfll');
                $pass   = getenv('DB_PASS') ?: ($_ENV['DB_PASS'] ?? '');

                $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require";

                try {
                    self::$connection = new PDO($dsn, $user, $pass, [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]);
                } catch (PDOException $e) {
                    die("Koneksi Database Gagal (Cloud): " . $e->getMessage());
                }
            }
        }

        return self::$connection;
    }
}