<?php

namespace App\Repositories;

use App\Models\CategoryAttribute;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductConversation;
use App\Models\ProductCommentRating;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Kalnoy\Nestedset\QueryBuilder;

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
        ?bool $status = null
    ): QueryBuilder {
        $query = Product::with([
            'primaryImage',
            'category.discounts' => fn($q) => $q->active(),
            'discounts' => fn($q) => $q->active(),
            'category',
            'statistics'
        ])
            ->whereNull('parent_id') // only parent products
            ->when(!is_null($status), fn($query) => $query->where('is_active', $status))
            // Filter by Category whether it's leaf category or parent one.
            ->when(isset($filters['category_id']), function ($query) use ($filters) {
                $category = $this->categoryRepository->find($filters['category_id']);
                if ($category) {
                    $leafCategories = $this->categoryRepository
                        ->getLeafDescendant($category)->pluck('id');
                    $query->whereIn('category_id',
                        $leafCategories->isNotEmpty() ? $leafCategories : [$category->id]);
                }
            });

        // Product with search query in title or description
        if(isset($filters['search'])) {
            $searchQuery=$filters['search'];
            $query->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', "%$searchQuery%")
                    ->orWhere('description', 'like', "%$searchQuery%");
            });
        }


        // Filter by attributes (handling both parent & variant level using value IDs)
        if (isset($filters['attributes']) && !empty($filters['attributes'])) {
            foreach ($filters['attributes'] as $attributeId => $valueIds) {
                if (empty($valueIds) || !is_array($valueIds)) {
                    continue;
                }

                $query->where(function ($q) use ($attributeId, $valueIds) {
                    $attributeFilter = fn($q) =>
                    $q->where('category_attribute_id', $attributeId)
                        ->whereIn('category_attribute_value_id', $valueIds);

                    $q->whereHas('attributes', $attributeFilter)
                        ->orWhereHas('variants.attributes', $attributeFilter);
                });
            }
        }

        // Filter by price range
        $min= $filters['price_min']?? null;
        $max = $filters['price_max'] ?? null;
        if (!is_null($min) && !is_null($max)) {
            $query->whereBetween('price', [$min, $max]);
        } elseif (!is_null($min)) {
            $query->where('price', '>=', $min);
        } elseif (!is_null($max)) {
            $query->where('price', '<=', $max);
        }


        // Sorting
        $query->when($sortBy === 'sales', fn($q) => $q->orderBy('statistics.sales_count', $order))
            ->when($sortBy === 'rating', fn($q) => $q->orderBy('statistics.avg_rating', $order))
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
                'variants',
                'variants.attributes.categoryAttribute',
                'variants.attributes.categoryAttributeValue',
                'category',      // The category this product belongs to
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


    public function syncAttributes(int $productId, array $attributes): void
    {
        $product = Product::findOrFail($productId);

        // Prepare a map of new attributes: [category_attribute_id => category_attribute_value_id]
        $newAttributes = collect($attributes)->keyBy('category_attribute_id')->map(fn ($item) => $item['category_attribute_value_id']);

        // Get current attributes in the same format
        $currentAttributes = $product->attributes()
            ->get()
            ->keyBy('category_attribute_id')
            ->map(fn ($item) => $item->category_attribute_value_id);

        // Attributes to delete
        $toDelete = $currentAttributes->diffKeys($newAttributes)->keys();
        if ($toDelete->isNotEmpty()) {
            $product->attributes()->whereIn('category_attribute_id', $toDelete)->delete();
        }

        // Attributes to update or create
        foreach ($newAttributes as $categoryAttributeId => $valueId) {
            $product->attributes()->updateOrCreate(
                ['category_attribute_id' => $categoryAttributeId],
                ['category_attribute_value_id' => $valueId]
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

    public function updateVariant(int $variantId, array $data): Product
    {
        $variant = Product::whereNotNull('parent_id')->findOrFail($variantId);

        $variant->update([
            'price' => $data['price'],
            'sku' => $data['sku'] ?? null,
            'stock' => $data['stock'] ?? 0,
            'is_active' => $data['is_active'] ?? true,
        ]);

        if (isset($data['attributes'])) {
            // Delete existing attributes
            $variant->attributes()->delete();

            // Re-add new ones
            foreach ($data['attributes'] as $attribute) {
                $variant->attributes()->create([
                    'category_attribute_id' => $attribute['category_attribute_id'],
                    'category_attribute_value_id' => $attribute['category_attribute_value_id'],
                ]);
            }
        }

        return $variant;
    }

    public function deleteVariant(int $variantId): void
    {
        $variant = Product::whereNotNull('parent_id')->findOrFail($variantId);

        // Delete related attributes first (if not using cascade)
        $variant->attributes()->delete();

        // Delete the variant itself
        $variant->delete();
    }

    public function syncDiscount(int $productId, array $discountData): void
    {
        $product = Product::findOrFail($productId);

        // Remove existing discounts if no valid data is provided
        if (empty($discountData['discount_amount']) && empty($discountData['discount_percentage'])) {
            $product->discounts()->delete();
            return;
        }

        $existingDiscount = $product->discounts()->first();

        $data = [
            'product_id' => $productId,
            'discount_amount' => $discountData['discount_amount'] ?? null,
            'discount_percentage' => $discountData['discount_percentage'] ?? null,
            'start_date' => $discountData['start_date'] ?? null,
            'end_date' => $discountData['end_date'] ?? null,
        ];

        if ($existingDiscount) {
            $existingDiscount->update($data);
        } else {
            $product->discounts()->create($data);
        }
    }


}
