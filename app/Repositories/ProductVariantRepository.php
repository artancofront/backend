<?php

namespace App\Repositories;

use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Collection;

class ProductVariantRepository
{

    /**
     * Find a product variant by ID.
     */
    public function find(int $id): ?ProductVariant
    {
        return ProductVariant::with(['attributes.categoryAttribute', 'attributes.categoryAttributeValue'])->find($id);
    }

    /**
     * Get product variants for a specific product.
     */
    public function getByProductId(int $productId, bool $status=null): Collection
    {
        return ProductVariant::where('product_id', $productId)
            ->when(!is_null($status), function ($query) use ($status) {
                $query->where('is_active', $status); // Apply 'is_active' filter only if `$status` is set
            })
            ->with(['attributes.categoryAttribute', 'attributes.categoryAttributeValue'])->get();
    }

    /**
     * Create a new product variant.
     */
    public function create(array $data): ProductVariant
    {
        return ProductVariant::create($data);
    }

    /**
     * Update an existing product variant.
     */
    public function update(int $id, array $data): bool
    {
        $variant = $this->find($id);
        return $variant ? $variant->update($data) : false;
    }

    /**
     * Delete a product variant.
     */
    public function delete(int $id): bool
    {
        $variant = $this->find($id);
        return $variant ? $variant->delete() : false;
    }

    public function assignAttributes(int $variantId, array $attributes): void
    {
        $variant = ProductVariant::findOrFail($variantId);

        foreach ($attributes as $attribute) {
            $variant->attributes()->updateOrCreate(
                [
                    'category_attribute_id' => $attribute['category_attribute_id'],
                ],
                [
                    'category_attribute_value_id' => $attribute['category_attribute_value_id'],
                ]
            );
        }
    }



}
