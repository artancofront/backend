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
     * Get all categories.
     */
    public function getAllCategories(): Collection
    {
        return $this->categoryRepository->all();
    }

    /**
     * Find a category by ID.
     */
    public function getCategoryById(int $id): ?Category
    {
        return $this->categoryRepository->find($id);
    }

    /**
     * Create a new category.
     */
    public function createCategory(array $data): Category
    {
        return $this->categoryRepository->create($data);
    }

    /**
     * Update an existing category.
     */
    public function updateCategory(int $id, array $data): bool
    {
        return $this->categoryRepository->update($id, $data);
    }

    /**
     * Delete a category.
     */
    public function deleteCategory(int $id): bool
    {
        return $this->categoryRepository->delete($id);
    }

    /**
     * Get all leaf categories.
     */
    public function getLeafCategories(): Collection
    {
        return $this->categoryRepository->getLeafCategories();
    }

    /**
     * Get all parent categories up to the root.
     */
    public function getParentCategories(Category $category): Collection
    {
        return $this->categoryRepository->getParentCategories($category);
    }

    /**
     * Get all root categories.
     */
    public function getRootCategories(): Collection
    {
        return $this->categoryRepository->getRootCategories();
    }

    /**
     * Get all leaf categories under a specific parent category.
     */
    public function getLeafCategoriesUnder(Category $parentCategory): Collection
    {
        return $this->categoryRepository->getLeafCategoriesUnder($parentCategory);
    }

    /**
     * Get all categories in a nested hierarchy.
     */
    public function getCategoryHierarchy(): array
    {
        return $this->categoryRepository->getCategoryHierarchy();
    }
}
