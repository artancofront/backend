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
//    public function getProducts2(
//        array $filters = [],
//        string $sortBy = 'price',
//        string $order = 'asc',
//        int $perPage = 15,
//        ?bool $status = null // Accepts `true` (active), `false` (inactive), or `null` (all)
//    ): LengthAwarePaginator {
//
//        $query = Product::with(['primaryImage',
//                                'category.discounts' => function ($q) {
//                                    $q->active();
//                                },
//                                'discounts' => function ($q) {
//                                    $q->active();
//                                },
//                                'variants',
//                                'category',
//                                'statistics'])
//            ->when(!is_null($status), function ($query) use ($status) {
//                $query->where('is_active', $status); // Apply 'is_active' filter only if `$status` is set
//            })
//
//            // Products with specific category
//            ->when(isset($filters['category_id']), function ($query) use ($filters) {
//                $category = $this->categoryRepository->find($filters['category_id']);
//                if ($category) {
//                    $leafCategories = $this->categoryRepository->getLeafDescendant($category)->pluck('id');
//
//                    if ($leafCategories->isNotEmpty()) {
//                        $query->whereIn('category_id', $leafCategories);
//                    } else {
//                        $query->where('category_id', $category->id);
//                    }
//                }
//            })
//            // Products with specific attributes values
//            ->when(isset($filters['attributes']), function ($query) use ($filters) {
//                foreach ($filters['attributes'] as $attribute => $value) {
//                    $query->whereHas('attributes', function ($query) use ($attribute, $value) {
//                        $query->whereHas('categoryAttribute', function ($query) use ($attribute) {
//                            $query->where('name', $attribute); // Filter by category attribute name (e.g., 'color', 'size')
//                        })
//                            ->whereHas('categoryAttributeValue', function ($query) use ($value) {
//                                // Retrieve the attribute type
//                                $attributeType = $query->with('categoryAttribute')->first()->categoryAttribute->type;
//
//                                // Filter by value based on the attribute type
//                                switch ($attributeType) {
//                                    case 'text':
//                                        $query->where('text_value', $value); // For text values
//                                        break;
//
//                                    case 'boolean':
//                                        $query->where('boolean_value', (bool) $value); // For boolean values
//                                        break;
//
//                                    case 'number':
//                                        // Check if the value is an array (for range)
//                                        if (is_array($value) && isset($value['min'], $value['max'])) {
//                                            $query->whereBetween('numeric_value', [$value['min'], $value['max']]); // For numeric values
//                                        } else {
//                                            $query->where('numeric_value', $value); // Exact match for numeric values
//                                        }
//                                        break;
//                                }
//                            });
//                    });
//                }
//            })
//            // Sort by specified feature
//            ->when($sortBy === 'sales', function ($query) use ($order) {
//                $query->orderBy('statistics.sales_count', $order);
//            })
//            ->when($sortBy === 'score', function ($query) use ($order) {
//                $query->orderBy('statistics.avg_score', $order);
//            })
//            ->when(in_array($sortBy, ['price', 'created_at']), function ($query) use ($sortBy, $order) {
//                $query->orderBy($sortBy, $order);
//            });
//
//        return $query->paginate($perPage);
//    }

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

//    public function getProducts(
//        array $filters = [],
//        string $sortBy = 'price',
//        string $order = 'asc',
//        int $perPage = 15,
//        ?bool $status = null
//    ): LengthAwarePaginator {
//        $query = Product::with([
//            'primaryImage',
//            'category.discounts' => fn($q) => $q->active(),
//            'discounts' => fn($q) => $q->active(),
//            'variants',
//            'category',
//            'statistics'
//        ])
//            ->whereNull('parent_id') // only parent products
//            ->when(!is_null($status), fn($query) => $query->where('is_active', $status))
//            ->when(isset($filters['category_id']), function ($query) use ($filters) {
//                $category = $this->categoryRepository->find($filters['category_id']);
//                if ($category) {
//                    $leafCategories = $this->categoryRepository
//                        ->getLeafDescendant($category)->pluck('id');
//                    $query->whereIn('category_id',
//                        $leafCategories->isNotEmpty() ? $leafCategories : [$category->id]);
//                }
//            });
//
//        // Filter by attributes (handling both parent & variant level)
//        if (isset($filters['attributes']) && !empty($filters['attributes'])) {
//            foreach ($filters['attributes'] as $attributeName => $value) {
//                // Retrieve the category attribute with type (cache or service could be used here for performance)
//                $attribute = CategoryAttribute::where('name', $attributeName)->first();
//                if (!$attribute) {
//                    continue;
//                }
//
//                $query->where(function ($q) use ($attribute, $value) {
//                    $attributeFilter = function ($query) use ($attribute, $value) {
//                        $query->where('category_attribute_id', $attribute->id)
//                            ->whereHas('categoryAttributeValue', function ($q) use ($attribute, $value) {
//                                switch ($attribute->type) {
//                                    case 'text':
//                                        $q->where('text_value', $value);
//                                        break;
//                                    case 'boolean':
//                                        $q->where('boolean_value', (bool) $value);
//                                        break;
//                                    case 'number':
//                                        if (is_array($value) && isset($value['min'], $value['max'])) {
//                                            $q->whereBetween('numeric_value', [$value['min'], $value['max']]);
//                                        } else {
//                                            $q->where('numeric_value', $value);
//                                        }
//                                        break;
//                                }
//                            });
//                    };
//
//                    // Apply to product attributes
//                    $q->whereHas('attributes', $attributeFilter);
//
//                    // Apply to variant attributes
//                    $q->orWhereHas('variants.attributes', $attributeFilter);
//                });
//            }
//        }
//
//        // Sorting
//        $query->when($sortBy === 'sales', fn($q) => $q->orderBy('statistics.sales_count', $order))
//            ->when($sortBy === 'score', fn($q) => $q->orderBy('statistics.avg_score', $order))
//            ->when(in_array($sortBy, ['price', 'created_at']), fn($q) => $q->orderBy($sortBy, $order));
//
//        return $query->paginate($perPage);
//    }


    /**
     * Get a product by ID with all related details.
     */
    public function getProductById(int $productId)
    {
        return Product::with([
            'images',        // All images of the product
            'category.discounts' => function ($q) {
                $q->active();
            },
            'discounts' => function ($q) {
                $q->active();
            },
            'scores',        // All scores (for rating calculation)
            'category',      // The category this product belongs to
            'orders',        // Orders related to this product (for sales stats)
            'statistics',
            'attributes.categoryAttribute', // Attribute names
            'attributes.categoryAttributeValue', // Attribute values
            'variants.attributes.categoryAttribute', // Variant attribute names
            'variants.attributes.categoryAttributeValue' // Variant attribute values
        ])->find($productId);
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

}
