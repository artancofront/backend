<?php

namespace App\Observers;

use App\Models\Product;
use App\Repositories\ProductStatisticRepository;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    protected $productStatisticRepository;

    public function __construct(ProductStatisticRepository $productStatisticRepository)
    {
        $this->productStatisticRepository = $productStatisticRepository;
    }

    /**
     * Handle the Product "created" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function created(Product $product)
    {
        $this->flushProductFilterCache();
        // Update statistics when a new product or its variant is created
        $this->updateStatistics($product);
    }

    /**
     * Handle the Product "updated" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function updated(Product $product)
    {
        $this->flushProductCache($product->id);
        $this->flushProductFilterCache();
        // Update statistics when a product or its variant is updated
        $this->updateStatistics($product);
    }

    /**
     * Handle the Product "deleted" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function deleted(Product $product)
    {
        $this->flushProductCache($product->id);
        $this->flushProductFilterCache();
        // Update statistics when a product or its variant is deleted
        $this->updateStatistics($product);
    }

    /**
     * Handle the Product "restored" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function restored(Product $product)
    {
        $this->flushProductCache($product->id);
        $this->flushProductFilterCache();
        // Update statistics when a product or its variant is restored
        $this->updateStatistics($product);
    }

    /**
     * Handle the Product "force deleted" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function forceDeleted(Product $product)
    {
        $this->flushProductCache($product->id);
        $this->flushProductFilterCache();
        // Update statistics when a product or its variant is force deleted
        $this->updateStatistics($product);
    }

    protected function updateStatistics(Product $product)
    {
        $parentOrSelf = $product->isLeaf() ? $product->parent : $product;

        if ($parentOrSelf) {
            $this->productStatisticRepository->updateVariantCount($parentOrSelf);
            $this->productStatisticRepository->updateMinMaxPrice($parentOrSelf);
        }
    }

    protected function flushProductFilterCache(): void
    {
        Cache::tags(['product_filters'])->flush();
    }
    protected function flushProductCache(int $productId): void
    {
        Cache::tags(['product_details'])->forget('product_' . $productId);
    }
}

