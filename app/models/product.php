<?php
declare(strict_types=1);

namespace app\models;

use PDO;
use Throwable;

class product extends basemodel
{
    protected string $table = 'products';

    public function getAll(): array
    {
        try {
            $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY id DESC");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            return array_map([$this, 'normalizeRow'], $rows);
        } catch (Throwable $e) {
            error_log('[ProductModel] getAll Error: ' . $e->getMessage());
            return [];
        }
    }

    public function find(int $id): ?array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $this->normalizeRow($row) : null;
        } catch (Throwable $e) {
            error_log('[ProductModel] find Error: ' . $e->getMessage());
            return null;
        }
    }

    public function create(array $data): bool
    {
        $name = trim((string) ($data['name'] ?? ''));
        $slug = $this->generateUniqueSlug($name);

        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (
                name, slug, price, description, image_url, thumbnail_url, image_path, thumbnail_path, stock, status
            ) VALUES (
                :name, :slug, :price, :description, :image_url, :thumbnail_url, :image_path, :thumbnail_path, :stock, :status
            )
        ");

        return $stmt->execute([
            'name'           => $name,
            'slug'           => $slug,
            'price'          => $data['price'],
            'description'    => $data['description'] ?? '',
            'image_url'      => $data['image_url'] ?? null,
            'thumbnail_url'  => $data['thumbnail_url'] ?? null,
            'image_path'     => $data['image_path'] ?? null,
            'thumbnail_path' => $data['thumbnail_path'] ?? null,
            'stock'          => (int) ($data['stock'] ?? 10),
            'status'         => $data['status'] ?? 'available'
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} SET 
                name = :name, 
                price = :price, 
                description = :description, 
                image_url = :image_url, 
                thumbnail_url = :thumbnail_url, 
                image_path = :image_path, 
                thumbnail_path = :thumbnail_path, 
                stock = :stock, 
                status = :status,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");

        return $stmt->execute([
            'name'           => $data['name'],
            'price'          => $data['price'],
            'description'    => $data['description'] ?? '',
            'image_url'      => $data['image_url'] ?? null,
            'thumbnail_url'  => $data['thumbnail_url'] ?? null,
            'image_path'     => $data['image_path'] ?? null,
            'thumbnail_path' => $data['thumbnail_path'] ?? null,
            'stock'          => (int) ($data['stock'] ?? 10),
            'status'         => $data['status'] ?? 'available',
            'id'             => $id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    private function normalizeSlug(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = (string) preg_replace('/[^a-z0-9]+/i', '-', $slug);
        $slug = trim($slug, '-');
        return $slug === '' ? 'produk' : $slug;
    }

    private function generateUniqueSlug(string $name): string
    {
        $baseSlug = $this->normalizeSlug($name);
        $slug = $baseSlug;
        $counter = 2;
        while ($this->slugExists($slug)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        return $slug;
    }

    private function slugExists(string $slug): bool
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE slug = :slug");
            $stmt->execute(['slug' => $slug]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (Throwable $e) {
            return false;
        }
    }

    private function normalizeRow(array $row): array
    {
        $imageUrl = $row['image_url'] ?? ($row['image'] ?? null);
        $row['image_url'] = $imageUrl;
        $row['thumbnail_url'] = $row['thumbnail_url'] ?? $imageUrl;
        $row['stock'] = isset($row['stock']) ? (int) $row['stock'] : 10;
        $row['status'] = $row['status'] ?? 'available';
        $row['slug'] = $row['slug'] ?? '';
        return $row;
    }
}