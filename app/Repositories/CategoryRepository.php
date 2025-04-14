<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    /**
     * Find a category by its ID.
     *
     * @param int $id
     * @return Category|null
     */
    public function find(int $id): ?Category
    {
        return Category::find($id);
    }

    /**
     * Create a new category.
     *
     * @param array $data
     * @return Category
     */
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * Update an existing category with the given data.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $category = $this->find($id);
        return $category ? $category->update($data) : false;
    }

    /**
     * Delete a category by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $category = $this->find($id);
        return $category ? $category->delete() : false;
    }

    /**
     * Get all leaf categories (categories without any children).
     *
     * @return Collection
     */
    public function getLeafCategories(): Collection
    {
        return Category::doesntHave('children')->get();
    }

    /**
     * Get all root categories (categories without a parent).
     *
     * @return Collection
     */
    public function getRootCategories(): Collection
    {
        return Category::whereNull('parent_id')->get();
    }

    /**
     * Get the breadcrumb (ancestor trail) for a specific category.
     *
     * @param int $categoryId
     * @return Collection
     */
    public function getCategoryBreadcrumb(int $categoryId): Collection
    {
        $category = $this->find($categoryId);
        return $category ? $category->ancestors : collect();
    }

    /**
     * Get all descendant categories under a specific category.
     *
     * @param int $categoryId
     * @return Collection
     */
    public function getCategoryDescendants(int $categoryId): Collection
    {
        return Category::descendantsAndSelf($categoryId)->toTree();
    }

    /**
     * Get all categories in a nested hierarchy tree.
     *
     * @return Collection
     */
    public function getCategoryHierarchy(): Collection
    {
        return Category::defaultOrder()->get()->toTree();
    }

    /**
     * Get all products that belong to a specific category.
     *
     * @param int $categoryId
     * @return Collection
     */
    public function getCategoryProducts(int $categoryId): Collection
    {
        $category = Category::with('products')->find($categoryId);
        return $category ? $category->products : collect();
    }

    /**
     * Get all attributes assigned to a specific category.
     *
     * @param int $categoryId
     * @return Collection
     */
    public function getCategoryAttributes(int $categoryId): Collection
    {
        $category = Category::with('attributes')->find($categoryId);
        return $category ? $category->attributes : collect();
    }
}
