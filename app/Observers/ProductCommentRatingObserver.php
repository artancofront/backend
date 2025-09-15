<?php
namespace App\Observers;

namespace App\Observers;

use App\Models\ProductCommentRating;
use App\Repositories\ProductStatisticRepository;

class ProductCommentRatingObserver
{
    protected $productStatisticRepository;

    public function __construct(ProductStatisticRepository $productStatisticRepository)
    {
        $this->productStatisticRepository = $productStatisticRepository;
    }

    /**
     * Handle the ProductCommentRating "created" event.
     *
     * @param  \App\Models\ProductCommentRating  $productCommentRating
     * @return void
     */
    public function created(ProductCommentRating $productCommentRating)
    {
        // Update statistics for the product
        $this->productStatisticRepository->updateCommentCount($productCommentRating->product);
        $this->productStatisticRepository->updateRatingCount($productCommentRating->product);
        $this->productStatisticRepository->updateAvgRating($productCommentRating->product);
    }

    /**
     * Handle the ProductCommentRating "updated" event.
     *
     * @param  \App\Models\ProductCommentRating  $productCommentRating
     * @return void
     */
    public function updated(ProductCommentRating $productCommentRating)
    {
        // Update statistics for the product
        $this->productStatisticRepository->updateCommentCount($productCommentRating->product);
        $this->productStatisticRepository->updateRatingCount($productCommentRating->product);
        $this->productStatisticRepository->updateAvgRating($productCommentRating->product);
    }

    /**
     * Handle the ProductCommentRating "deleted" event.
     *
     * @param  \App\Models\ProductCommentRating  $productCommentRating
     * @return void
     */
    public function deleted(ProductCommentRating $productCommentRating)
    {
        // Update statistics for the product
        $this->productStatisticRepository->updateCommentCount($productCommentRating->product);
        $this->productStatisticRepository->updateRatingCount($productCommentRating->product);
        $this->productStatisticRepository->updateAvgRating($productCommentRating->product);
    }

    /**
     * Handle the ProductCommentRating "restored" event.
     *
     * @param  \App\Models\ProductCommentRating  $productCommentRating
     * @return void
     */
    public function restored(ProductCommentRating $productCommentRating)
    {
        // Update statistics for the product
        $this->productStatisticRepository->updateCommentCount($productCommentRating->product);
        $this->productStatisticRepository->updateRatingCount($productCommentRating->product);
        $this->productStatisticRepository->updateAvgRating($productCommentRating->product);
    }

    /**
     * Handle the ProductCommentRating "force deleted" event.
     *
     * @param  \App\Models\ProductCommentRating  $productCommentRating
     * @return void
     */
    public function forceDeleted(ProductCommentRating $productCommentRating)
    {
        // Update statistics for the product
        $this->productStatisticRepository->updateCommentCount($productCommentRating->product);
        $this->productStatisticRepository->updateRatingCount($productCommentRating->product);
        $this->productStatisticRepository->updateAvgRating($productCommentRating->product);
    }
}

