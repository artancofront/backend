<?php

namespace App\Repositories;

use App\Models\ProductDiscount;
use Illuminate\Support\Collection;

class ProductDiscountRepository
{
    /**
     * Get all discounts for a specific product.
     */
    public function getByProductId(int $productId): Collection
    {
        return ProductDiscount::where('product_id', $productId)->get();
    }

    /**
     * Get active discount for a product.
     */
    public function getActiveByProductId(int $productId): ?ProductDiscount
    {
        return ProductDiscount::where('product_id', $productId)->active()->first();
    }

    /**
     * Get all active discounts.
     */
    public function getAllActive(): Collection
    {
        return ProductDiscount::active()->get();
    }

    /**
     * Create a new product discount.
     */
    public function create(array $data): ProductDiscount
    {
        return ProductDiscount::create($data);
    }

    /**
     * Update a product discount.
     */
    public function update(ProductDiscount $discount, array $data): bool
    {
        return $discount->update($data);
    }

    /**
     * Delete a product discount.
     */
    public function delete(ProductDiscount $discount): bool
    {
        return $discount->delete();
    }
}
