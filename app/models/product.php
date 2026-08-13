<?php
declare(strict_types=1);

namespace app\models;

class product extends basemodel
{
    public int $id;
    public ?int $category_id = null;
    public string $name;
    public string $slug;
    public float $price;
    public int $stock;
    public ?string $description = null;
    public string $status = 'active';
    public ?string $created_at = null;
    public ?string $updated_at = null;
}