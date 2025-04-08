<?php

namespace App\Repositories;

use App\Models\CategoryAttribute;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductConversation;
use App\Models\ProductCommentScore;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class ProductRepository
{
    protected CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }


    /**
     * Get all products with filtering and sorting options.
     */
    public function getProducts(
        array $filters = [],
        string $sortBy = 'price',
        string $order = 'asc',
        int $perPage = 15,
        ?bool $status = null
    ): LengthAwarePaginator {
        $cacheKey = 'products_' . md5(json_encode([
                'filters' => $filters,
                'sortBy' => $sortBy,
                'order' => $order,
                'perPage' => $perPage,
                'status' => $status,
            ]));

        return Cache::tags(['product_filters'])->remember($cacheKey, now()->addMinutes(10), function () use ($filters, $sortBy, $order, $perPage, $status) {
            return $this->buildProductQuery($filters, $sortBy, $order, $status)->paginate($perPage);
        });
    }

    /**
     * Build the product query used in 'getProducts' method.
     */
    public function buildProductQuery(
        array $filters = [],
        string $sortBy = 'price',
        string $order = 'asc',
        int $perPage = 15,
        ?bool $status = null
    ): LengthAwarePaginator {
        $query = Product::with([
            'primaryImage',
            'category.discounts' => fn($q) => $q->active(),
            'discounts' => fn($q) => $q->active(),
            'variants',
            'category',
            'statistics'
        ])
            ->whereNull('parent_id') // only parent products
            ->when(!is_null($status), fn($query) => $query->where('is_active', $status))
            // Filter by Category whether its leaf category or parent one.
            ->when(isset($filters['category_id']), function ($query) use ($filters) {
                $category = $this->categoryRepository->find($filters['category_id']);
                if ($category) {
                    $leafCategories = $this->categoryRepository
                        ->getLeafDescendant($category)->pluck('id');
                    $query->whereIn('category_id',
                        $leafCategories->isNotEmpty() ? $leafCategories : [$category->id]);
                }
            });

        // Filter by attributes (handling both parent & variant level)
        if (isset($filters['attributes']) && !empty($filters['attributes'])) {
            foreach ($filters['attributes'] as $attributeId => $filter) {
                $type = $filter['type'];
                $value = $filter['value'];
                if (!isset($value) || $value === '' || (is_array($value) && empty($value))) {
                    continue;
                }
                $query->where(function ($q) use ($attributeId, $type, $value) {
                    $attributeFilter = function ($query) use ($attributeId, $type, $value) {
                        $query->where('category_attribute_id', $attributeId)
                            ->whereHas('categoryAttributeValue', function ($q) use ($type, $value) {
                                switch ($type) {
                                    case 'text':
                                        $q->where('text_value', $value);
                                        break;
                                    case 'boolean':
                                        $q->where('boolean_value', filter_var($value, FILTER_VALIDATE_BOOLEAN));
                                        break;
                                    case 'number':
                                        if (is_array($value) && isset($value['min'], $value['max'])) {
                                            $q->whereBetween('numeric_value', [$value['min'], $value['max']]);
                                        } else {
                                            $q->where('numeric_value', $value);
                                        }
                                        break;
                                }
                            });
                    };

                    // Apply to product attributes
                    $q->whereHas('attributes', $attributeFilter);

                    // Apply to variant attributes
                    $q->orWhereHas('variants.attributes', $attributeFilter);
                });
            }
        }

        // Filter by price range
        if (isset($filters['price_range']) && is_array($filters['price_range'])) {
            $min = $filters['price_range']['min'] ?? null;
            $max = $filters['price_range']['max'] ?? null;

            if (!is_null($min) && !is_null($max)) {
                $query->whereBetween('price', [$min, $max]);
            } elseif (!is_null($min)) {
                $query->where('price', '>=', $min);
            } elseif (!is_null($max)) {
                $query->where('price', '<=', $max);
            }
        }

        // Sorting
        $query->when($sortBy === 'sales', fn($q) => $q->orderBy('statistics.sales_count', $order))
            ->when($sortBy === 'score', fn($q) => $q->orderBy('statistics.avg_score', $order))
            ->when(in_array($sortBy, ['price', 'created_at']), fn($q) => $q->orderBy($sortBy, $order));

        return $query;
    }


    /**
     * Get a product by ID with all related details.
     */

    public function getProductById(int $productId)
    {
        $cacheKey = 'product_' . $productId;

        return Cache::tags(['product_details'])->remember($cacheKey,
            now()->addMinutes(10), function () use ($productId) {
            return Product::with([
                'images',        // All images of the product
                'category.discounts' => function ($q) {
                    $q->active();
                },
                'discounts' => function ($q) {
                    $q->active();
                },
                'category',      // The category this product belongs to
                'orders',        // Orders related to this product (for sales stats)
                'statistics',
                'attributes.categoryAttribute', // Attribute names
                'attributes.categoryAttributeValue', // Attribute values
            ])->find($productId)->append('resolved_policies')->append('resolved_warranties');
        });
    }



    public function createProduct(array $data): Product
    {
        return Product::create($data);
    }


    public function updateProduct(int $id, array $data): bool
    {
        $product = Product::findOrFail($id);
        return $product->update($data);
    }

    public function deleteProduct(int $id): bool
    {
        $product = Product::findOrFail($id);
        return $product->delete();
    }

    public function assignAttributes(int $productId, array $attributes): void
    {
        $product = Product::findOrFail($productId);

        foreach ($attributes as $attribute) {
            $product->attributes()->updateOrCreate(
                [
                    'category_attribute_id' => $attribute['category_attribute_id'],
                ],
                [
                    'category_attribute_value_id' => $attribute['category_attribute_value_id'],
                ]
            );
        }
    }

    public function addVariant(int $parentId, array $data): Product
    {
        $parent = Product::whereNull('parent_id')->findOrFail($parentId);

        $variant = $parent->variants()->create([
            'parent_id' => $parent->id,
            'price' => $data['price'],
            'sku' => $data['sku'] ?? null,
            'stock' => $data['stock'] ?? 0,
            'is_active' => $data['is_active'] ?? true,
        ]);

        if (!empty($data['attributes'])) {
            foreach ($data['attributes'] as $attribute) {
                $variant->attributes()->create([
                    'category_attribute_id' => $attribute['category_attribute_id'],
                    'category_attribute_value_id' => $attribute['category_attribute_value_id'],
                ]);
            }
        }

        return $variant;
    }


}
