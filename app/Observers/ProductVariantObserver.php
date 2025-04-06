<?php

namespace App\Observers;

use App\Models\ProductVariant;
use App\Repositories\ProductStatisticRepository;

class ProductVariantObserver
{
    protected $productStatisticRepository;

    public function __construct(ProductStatisticRepository $productStatisticRepository)
    {
        $this->productStatisticRepository = $productStatisticRepository;
    }

    /**
     * Handle the ProductVariant "created" event.
     *
     * @param  \App\Models\ProductVariant  $productVariant
     * @return void
     */
    public function created(ProductVariant $productVariant)
    {
        // Update statistics when a new variant is created
        $this->productStatisticRepository->updateVariantCount($productVariant->product);
        $this->productStatisticRepository->updateMinMaxPrice($productVariant->product);
    }

    /**
     * Handle the ProductVariant "updated" event.
     *
     * @param  \App\Models\ProductVariant  $productVariant
     * @return void
     */
    public function updated(ProductVariant $productVariant)
    {
        // Update statistics when a variant is updated
        $this->productStatisticRepository->updateVariantCount($productVariant->product);
        $this->productStatisticRepository->updateMinMaxPrice($productVariant->product);
    }

    /**
     * Handle the ProductVariant "deleted" event.
     *
     * @param  \App\Models\ProductVariant  $productVariant
     * @return void
     */
    public function deleted(ProductVariant $productVariant)
    {
        // Update statistics when a variant is deleted
        $this->productStatisticRepository->updateVariantCount($productVariant->product);
        $this->productStatisticRepository->updateMinMaxPrice($productVariant->product);
    }

    /**
     * Handle the ProductVariant "restored" event.
     *
     * @param  \App\Models\ProductVariant  $productVariant
     * @return void
     */
    public function restored(ProductVariant $productVariant)
    {
        // Update statistics when a variant is restored
        $this->productStatisticRepository->updateVariantCount($productVariant->product);
        $this->productStatisticRepository->updateMinMaxPrice($productVariant->product);
    }

    /**
     * Handle the ProductVariant "force deleted" event.
     *
     * @param  \App\Models\ProductVariant  $productVariant
     * @return void
     */
    public function forceDeleted(ProductVariant $productVariant)
    {
        // Update statistics when a variant is force deleted
        $this->productStatisticRepository->updateVariantCount($productVariant->product);
        $this->productStatisticRepository->updateMinMaxPrice($productVariant->product);
    }
}

