<?php

namespace App\Repositories;

use App\Models\CategoryDiscount;
use Illuminate\Support\Collection;

class CategoryDiscountRepository
{
    /**
     * Get all discounts for a specific category.
     */
    public function getByCategoryId(int $categoryId): Collection
    {
        return CategoryDiscount::where('category_id', $categoryId)->get();
    }

    /**
     * Get active discount for a category.
     */
    public function getActiveByCategoryId(int $categoryId): ?CategoryDiscount
    {
        return CategoryDiscount::where('category_id', $categoryId)->active()->first();
    }

    /**
     * Get all active category discounts.
     */
    public function getAllActive(): Collection
    {
        return CategoryDiscount::active()->get();
    }

    /**
     * Create a new category discount.
     */
    public function create(array $data): CategoryDiscount
    {
        return CategoryDiscount::create($data);
    }

    /**
     * Update a category discount.
     */
    public function update(CategoryDiscount $discount, array $data): bool
    {
        return $discount->update($data);
    }

    /**
     * Delete a category discount.
     */
    public function delete(CategoryDiscount $discount): bool
    {
        return $discount->delete();
    }
}
