<?php
declare(strict_types=1);

namespace app\repositories;

use app\core\database;
use PDO;

class productrepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = database::getconnection();
    }

    public function findall(): array
    {
        $stmt = $this->db->query("SELECT * FROM products ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function findbyid(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function create(array $data): bool
    {
        $sql = "INSERT INTO products (name, slug, price, stock, description, image, status) 
                VALUES (:name, :slug, :price, :stock, :description, :image, :status)";

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['name']), '-')) . '-' . time();

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name'        => $data['name'],
            ':slug'        => $slug,
            ':price'       => $data['price'],
            ':stock'       => $data['stock'],
            ':description' => $data['description'],
            ':image'       => $data['image'],
            ':status'      => $data['status'] ?? 'active'
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE products 
                SET name = :name, price = :price, stock = :stock, description = :description, image = :image, status = :status 
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'          => $id,
            ':name'        => $data['name'],
            ':price'       => $data['price'],
            ':stock'       => $data['stock'],
            ':description' => $data['description'],
            ':image'       => $data['image'],
            ':status'      => $data['status'] ?? 'active'
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}