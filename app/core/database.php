<?php
declare(strict_types=1);

namespace app\core;

use PDO;
use PDOException;

class database
{
    private static ?PDO $connection = null;

    public static function getconnection(): PDO
    {
        if (self::$connection === null) {
            $host = '127.0.0.1';
            $dbname = 'bucket_bunga_dakota'; // Sesuaikan dengan nama database MySQL Anda
            $username = 'root';
            $password = ''; // Isikan password MySQL jika ada (default XAMPP biasanya kosong)

            try {
                $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
                self::$connection = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                die("Koneksi Database Gagal: " . $e->getMessage());
            }
        }

        return self::$connection;
    }
}