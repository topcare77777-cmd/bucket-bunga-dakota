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
            return null;
        }
    }

    public function create(array $data): bool
    {
        $name = trim((string) ($data['name'] ?? ''));
        $slug = $this->generateUniqueSlug($name);

        error_log('[PRODUCT_TRACE] DB_INSERT_STARTED for Slug: ' . $slug);

        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (
                name, slug, price, description, image_url, thumbnail_url, image_path, thumbnail_path, stock, status
            ) VALUES (
                :name, :slug, :price, :description, :image_url, :thumbnail_url, :image_path, :thumbnail_path, :stock, :status
            )
        ");

        $result = $stmt->execute([
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

        if ($result) {
            error_log('[PRODUCT_TRACE] DB_INSERT_SUCCESS');
        }
        return $result;
    }

    public function update(int $id, array $data): bool
    {
        return false; // Simplified
    }

    public function delete(int $id): bool
    {
        return false; // Simplified
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