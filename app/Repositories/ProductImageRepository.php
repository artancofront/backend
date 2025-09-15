<?php

namespace App\Repositories;

use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class ProductImageRepository
{
    /**
     * Get all product images.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return ProductImage::all();
    }

    /**
     * Get all images for a specific product.
     *
     * @param int $productId
     * @return Collection
     */
    public function getByProduct(int $productId): Collection
    {
        return ProductImage::where('product_id', $productId)
            ->orderBy('order')
            ->get();
    }

    /**
     * Get the primary image for a product.
     *
     * @param int $productId
     * @return ProductImage|null
     */
    public function getPrimaryImage(int $productId): ?ProductImage
    {
        return ProductImage::where('product_id', $productId)
            ->primary()
            ->first();
    }

    /**
     * Find a product image by its ID.
     *
     * @param int $id
     * @return ProductImage
     * @throws Exception
     */
    public function find(int $id): ProductImage
    {
        $image = ProductImage::find($id);

        if (!$image) {
            throw new Exception("Product image not found.");
        }

        return $image;
    }

    /**
     * Create a new product image.
     *
     * @param array $data
     * @return ProductImage
     */
    public function create(array $data): ProductImage
    {
        return ProductImage::create($data);
    }

    /**
     * Update an existing product image.
     *
     * @param int $id
     * @param array $data
     * @return ProductImage
     * @throws Exception
     */
    public function update(int $id, array $data): ProductImage
    {
        $image = $this->find($id);
        $image->update($data);
        return $image;
    }

    /**
     * Delete a product image.
     *
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        $image = $this->find($id);
        return $image->delete();
    }

    /**
     * Set an image as the primary one for its product.
     *
     * @param int $imageId
     * @return ProductImage
     * @throws Exception
     */
    public function setAsPrimary(int $imageId): ProductImage
    {
        $image = $this->find($imageId);

        // Unset other primary images for the same product
        ProductImage::where('product_id', $image->product_id)
            ->update(['is_primary' => false]);

        $image->is_primary = true;
        $image->save();

        return $image;
    }
}
