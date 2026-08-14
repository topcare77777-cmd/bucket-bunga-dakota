<?php
declare(strict_types=1);

namespace app\models;

use PDO;

class product extends basemodel
{
    protected string $table = 'products';

    /**
     * Ambil semua produk katalog
     */
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Cari produk berdasarkan ID
     */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        return $product ?: null;
    }

    /**
     * Tambah produk baru (Mendukung MySQL & PostgreSQL)
     */
    public function create(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (name, price, description, image) 
            VALUES (:name, :price, :description, :image)
        ");

        return $stmt->execute([
            'name'        => $data['name'],
            'price'       => $data['price'],
            'description' => $data['description'] ?? '',
            'image'       => $data['image'] ?? null,
        ]);
    }

    /**
     * Perbarui produk
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET name = :name, price = :price, description = :description, image = :image 
            WHERE id = :id
        ");

        return $stmt->execute([
            'name'        => $data['name'],
            'price'       => $data['price'],
            'description' => $data['description'] ?? '',
            'image'       => $data['image'] ?? null,
            'id'          => $id,
        ]);
    }

    /**
     * Hapus produk
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}