<?php
declare(strict_types=1);

namespace app\services;

use app\repositories\productrepository;

class productservice
{
    private productrepository $productRepository;

    public function __construct()
    {
        $this->productRepository = new productrepository();
    }

    public function getallproducts(): array
    {
        return $this->productRepository->findall();
    }

    public function getproductbyid(int $id): ?array
    {
        return $this->productRepository->findbyid($id);
    }

    public function createproduct(array $data): bool
    {
        return $this->productRepository->create($data);
    }

    public function updateproduct(int $id, array $data): bool
    {
        return $this->productRepository->update($id, $data);
    }

    public function deleteproduct(int $id): bool
    {
        return $this->productRepository->delete($id);
    }
}