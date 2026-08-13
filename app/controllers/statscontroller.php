<?php
declare(strict_types=1);

namespace app\controllers;

use app\core\database;
use PDO;

class statscontroller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = database::getconnection();
    }

    // Melacak klik tombol pesan WhatsApp
    public function trackorder(): void
    {
        $productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : null;

        $stmt = $this->db->prepare("INSERT INTO visitor_stats (type, product_id) VALUES ('order_click', :product_id)");
        $stmt->execute([':product_id' => $productId]);

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success']);
        exit;
    }

    // Mengambil total statistik untuk Dashboard
    public static function getstats(): array
    {
        $db = database::getconnection();

        $vStmt = $db->query("SELECT COUNT(*) FROM visitor_stats WHERE type = 'visitor'");
        $totalVisitors = (int) $vStmt->fetchColumn();

        $oStmt = $db->query("SELECT COUNT(*) FROM visitor_stats WHERE type = 'order_click'");
        $totalOrders = (int) $oStmt->fetchColumn();

        return [
            'visitors' => $totalVisitors,
            'orders'   => $totalOrders
        ];
    }
}