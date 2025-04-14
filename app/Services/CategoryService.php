<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    protected CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Find a category by ID.
     */
    public function find(int $id): ?Category
    {
        return $this->categoryRepository->find($id);
    }

    /**
     * Create a new category.
     */
    public function create(array $data): Category
    {
        return $this->categoryRepository->create($data);
    }

    /**
     * Update an existing category.
     */
    public function update(int $id, array $data): bool
    {
        return $this->categoryRepository->update($id, $data);
    }

    /**
     * Delete a category by ID.
     */
    public function delete(int $id): bool
    {
        return $this->categoryRepository->delete($id);
    }

    /**
     * Get all leaf categories (no children).
     */
    public function getLeafCategories(): Collection
    {
        return $this->categoryRepository->getLeafCategories();
    }

    /**
     * Get all root categories (no parent).
     */
    public function getRootCategories(): Collection
    {
        return $this->categoryRepository->getRootCategories();
    }

    /**
     * Get breadcrumb trail for a category.
     */
    public function getBreadcrumb(int $categoryId): Collection
    {
        return $this->categoryRepository->getCategoryBreadcrumb($categoryId);
    }

    /**
     * Get all descendants of a category.
     */
    public function getDescendants(int $categoryId): Collection
    {
        return $this->categoryRepository->getCategoryDescendants($categoryId);
    }

    /**
     * Get all categories as a nested tree.
     */
    public function getHierarchy(): Collection
    {
        return $this->categoryRepository->getCategoryHierarchy();
    }

    /**
     * Get products of a specific category.
     */
    public function getCategoryProducts(int $categoryId): Collection
    {
        return $this->categoryRepository->getCategoryProducts($categoryId);
    }

    /**
     * Get attributes of a specific category.
     */
    public function getCategoryAttributes(int $categoryId): Collection
    {
        return $this->categoryRepository->getCategoryAttributes($categoryId);
    }
}
