<?php

namespace App\Services;

use App\Repositories\CategoryAttributeRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Models\CategoryAttribute;
use App\Models\CategoryAttributeValue;

class CategoryAttributeService
{
    protected CategoryAttributeRepository $repository;

    public function __construct(CategoryAttributeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAll(): Collection
    {
        return $this->repository->getAll();
    }

    public function getById(int $id): ?CategoryAttribute
    {
        return $this->repository->getById($id);
    }

    public function getByCategoryId(int $categoryId): Collection
    {
        return $this->repository->getByCategoryId($categoryId);
    }

    public function create(array $data): CategoryAttribute
    {
        // Add any business logic before creation here
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        // Add any business logic before update here
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getValuesForAttribute(int $attributeId): Collection
    {
        return $this->repository->getValuesForAttribute($attributeId);
    }

    public function createValue(array $data): CategoryAttributeValue
    {
        return $this->repository->createValue($data);
    }

    public function updateValue(int $id, array $data): bool
    {
        return $this->repository->updateValue($id, $data);
    }

    public function deleteValue(int $id): bool
    {
        return $this->repository->deleteValue($id);
    }
}
