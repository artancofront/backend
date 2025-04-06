<?php
namespace App\Observers;

namespace App\Observers;

use App\Models\ProductCommentScore;
use App\Repositories\ProductStatisticRepository;

class ProductCommentScoreObserver
{
    protected $productStatisticRepository;

    public function __construct(ProductStatisticRepository $productStatisticRepository)
    {
        $this->productStatisticRepository = $productStatisticRepository;
    }

    /**
     * Handle the ProductCommentScore "created" event.
     *
     * @param  \App\Models\ProductCommentScore  $productCommentScore
     * @return void
     */
    public function created(ProductCommentScore $productCommentScore)
    {
        // Update statistics for the product
        $this->productStatisticRepository->updateCommentCount($productCommentScore->product);
        $this->productStatisticRepository->updateScoreCount($productCommentScore->product);
        $this->productStatisticRepository->updateAvgScore($productCommentScore->product);
    }

    /**
     * Handle the ProductCommentScore "updated" event.
     *
     * @param  \App\Models\ProductCommentScore  $productCommentScore
     * @return void
     */
    public function updated(ProductCommentScore $productCommentScore)
    {
        // Update statistics for the product
        $this->productStatisticRepository->updateCommentCount($productCommentScore->product);
        $this->productStatisticRepository->updateScoreCount($productCommentScore->product);
        $this->productStatisticRepository->updateAvgScore($productCommentScore->product);
    }

    /**
     * Handle the ProductCommentScore "deleted" event.
     *
     * @param  \App\Models\ProductCommentScore  $productCommentScore
     * @return void
     */
    public function deleted(ProductCommentScore $productCommentScore)
    {
        // Update statistics for the product
        $this->productStatisticRepository->updateCommentCount($productCommentScore->product);
        $this->productStatisticRepository->updateScoreCount($productCommentScore->product);
        $this->productStatisticRepository->updateAvgScore($productCommentScore->product);
    }

    /**
     * Handle the ProductCommentScore "restored" event.
     *
     * @param  \App\Models\ProductCommentScore  $productCommentScore
     * @return void
     */
    public function restored(ProductCommentScore $productCommentScore)
    {
        // Update statistics for the product
        $this->productStatisticRepository->updateCommentCount($productCommentScore->product);
        $this->productStatisticRepository->updateScoreCount($productCommentScore->product);
        $this->productStatisticRepository->updateAvgScore($productCommentScore->product);
    }

    /**
     * Handle the ProductCommentScore "force deleted" event.
     *
     * @param  \App\Models\ProductCommentScore  $productCommentScore
     * @return void
     */
    public function forceDeleted(ProductCommentScore $productCommentScore)
    {
        // Update statistics for the product
        $this->productStatisticRepository->updateCommentCount($productCommentScore->product);
        $this->productStatisticRepository->updateScoreCount($productCommentScore->product);
        $this->productStatisticRepository->updateAvgScore($productCommentScore->product);
    }
}

