<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CategoryRepository
{
    /**
     * Get all categories.
     */
    public function all(): Collection
    {
        return Category::all();
    }

    /**
     * Get category hierarchy under a specific category.
     */
    public function getCategoryDescendants(int $categoryId): array
    {
        $category = Category::with('descendants')->find($categoryId);

        return $category ? $category->descendants->toArray() : [];
    }


    /**
     * Find a category by ID.
     */
    public function find(int $id): ?Category
    {
        return Category::find($id);
    }

    /**
     * Create a new category.
     */
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * Update an existing category.
     */
    public function update(int $id, array $data): bool
    {
        $category = $this->find($id);
        return $category ? $category->update($data) : false;
    }

    /**
     * Delete a category.
     */
    public function delete(int $id): bool
    {
        $category = $this->find($id);
        return $category ? $category->delete() : false;
    }

    /**
     * Get all leaf categories (categories without children).
     */
    public function getLeafCategories(): Collection
    {
        return Category::doesntHave('children')->get();
    }


    /**
     * Get all leaf categories under a specific parent category.
     */
    public function getLeafDescendant(Category $parentCategory): Collection
    {
        return Category::whereDescendantOf($parentCategory)->doesntHave('children')->get();
    }

    /**
     * Get breadcrumb trail for a category.
     */
    public function getCategoryBreadcrumb(int $categoryId): array
    {
        $category = Category::find($categoryId);
        return $category->ancestors->toArray();  // Directly return the ancestors as the breadcrumb

    }


    /**
     * Get all root categories (categories with no parent).
     */
    public function getRootCategories(): Collection
    {
        return Category::whereNull('parent_id')->get();
    }

}
