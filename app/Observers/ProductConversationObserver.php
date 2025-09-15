<?php
namespace App\Observers;

use App\Models\ProductConversation;
use App\Repositories\ProductStatisticRepository;

class ProductConversationObserver
{
    protected $productStatisticRepository;

    public function __construct(ProductStatisticRepository $productStatisticRepository)
    {
        $this->productStatisticRepository = $productStatisticRepository;
    }

    /**
     * Handle the ProductConversation "created" event.
     *
     * @param  \App\Models\ProductConversation  $productConversation
     * @return void
     */
    public function created(ProductConversation $productConversation)
    {
        // Update statistics when a new conversation is created
        $this->productStatisticRepository->updateConversationCount($productConversation->product);
    }

    /**
     * Handle the ProductConversation "updated" event.
     *
     * @param  \App\Models\ProductConversation  $productConversation
     * @return void
     */
    public function updated(ProductConversation $productConversation)
    {
        // Update statistics when a conversation is updated
        $this->productStatisticRepository->updateConversationCount($productConversation->product);
    }

    /**
     * Handle the ProductConversation "deleted" event.
     *
     * @param  \App\Models\ProductConversation  $productConversation
     * @return void
     */
    public function deleted(ProductConversation $productConversation)
    {
        // Update statistics when a conversation is deleted
        $this->productStatisticRepository->updateConversationCount($productConversation->product);
    }

    /**
     * Handle the ProductConversation "restored" event.
     *
     * @param  \App\Models\ProductConversation  $productConversation
     * @return void
     */
    public function restored(ProductConversation $productConversation)
    {
        // Update statistics when a conversation is restored
        $this->productStatisticRepository->updateConversationCount($productConversation->product);
    }

    /**
     * Handle the ProductConversation "force deleted" event.
     *
     * @param  \App\Models\ProductConversation  $productConversation
     * @return void
     */
    public function forceDeleted(ProductConversation $productConversation)
    {
        // Update statistics when a conversation is force deleted
        $this->productStatisticRepository->updateConversationCount($productConversation->product);
    }
}
