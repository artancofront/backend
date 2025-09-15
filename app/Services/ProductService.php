<?php

namespace App\Services;

use App\Models\ProductImage;
use App\Repositories\ProductImageRepository;
use App\Repositories\ProductRepository;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class ProductService
{
    protected ProductRepository $productRepository;
    protected ProductImageRepository $imageRepository;


    public function __construct(
        ProductRepository $productRepository,
        ProductImageRepository $imageRepository,
    ) {
        $this->productRepository = $productRepository;
        $this->imageRepository = $imageRepository;

    }

    /**
     * Fetch products based on filters, sorting, and pagination.
     *
     * @param array $filters
     * @param string $sortBy
     * @param string $order
     * @param int $perPage
     * @param bool|null $status
     * @return LengthAwarePaginator
     */
    public function getProductList(array $filters = [], string $sortBy = 'price', string $order = 'asc', int $perPage = 15, ?bool $status = null)
    {
        return $this->productRepository->getProducts($filters, $sortBy, $order, $perPage, $status);
    }

    /**
     * Fetch the detailed information of a product by ID.
     *
     * @param int $productId
     * @return Product|null
     */
    public function getProductById(int $productId): ?Product
    {
        return $this->productRepository->getProductById($productId);
    }

    /**
     * Create a new product based on the given data.
     *
     * @param array $productData
     * @return Product
     */
    public function createBasicProduct(array $productData): Product
    {
        return $this->productRepository->createProduct($productData);
    }

    /**
     * Update Product with its relations.
     *
     * @param int $id
     * @param array $productData
     * @return void
     */
    public function updateProductWithRelations(int $id, array $productData): void
    {

        // Update product's own fields
        $this->productRepository->updateProduct($id,Arr::only($productData, [
            'name', 'slug', 'description', 'weight', 'length',
            'width', 'height', 'stock', 'sku', 'price',
            'category_id', 'specifications', 'expert_reviews', 'is_active',
            'warranties', 'policies', 'specifications', 'expert_review',
        ]));

        // Sync Relations
        if (isset($productData['attributes'])) {
            $this->productRepository->syncAttributes($id, $productData['attributes']);
        }

        if (isset($productData['discount'])) {
            $this->productRepository->syncDiscount($id, $productData['discount']);
        }

    }

    public function deleteProduct(int $id): bool
    {
        return $this->productRepository->deleteProduct($id);
    }

    /**
     * Attach images to the product.
     *
     * @param int $productId
     * @param array $imageData
     * @return void
     * @throws \Exception
     */
    public function saveProductImage(int $productId, array $imageData): void
    {

        $image = $this->imageRepository->create([
            'product_id' => $productId,
            'image_path'       => $imageData['path'],
            'order'      => $imageData['order'] ?? null,
            'is_primary' => $imageData['is_primary'] ?? false,
        ]);

        if (!empty($imageData['is_primary'])) {
            $this->imageRepository->setAsPrimary($image->id);
        }
    }

    public function updateImage(int $id, array $data)
    {
        if (!empty($data['is_primary'])) {
            $this->imageRepository->setAsPrimary($id);
        }
        return $this->imageRepository->update($id, $data['order']);
    }

    public function deleteImage(int $id)
    {
        return $this->imageRepository->delete($id);
    }

    public function addVariant(int $parentId, array $data)
    {
        return $this->productRepository->addVariant($parentId, $data);
    }

    public function updateVariant(int $variantId, array $data)
    {
        return $this->productRepository->updateVariant($variantId, $data);
    }

    public function deleteVariant(int $variantId): void
    {
        $this->productRepository->deleteVariant($variantId);
    }


}
