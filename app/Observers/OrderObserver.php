<?php
namespace App\Observers;

use App\Models\Order;
use App\Repositories\ProductStatisticRepository;

class OrderObserver
{
    protected $productStatisticRepository;

    public function __construct(ProductStatisticRepository $productStatisticRepository)
    {
        $this->productStatisticRepository = $productStatisticRepository;
    }

    /**
     * Handle the Order "created" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function created(Order $order)
    {
        // Update sales count for the product associated with the order
        $this->productStatisticRepository->updateSalesCount($order->product);
    }

    /**
     * Handle the Order "updated" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function updated(Order $order)
    {
        // Update sales count for the product associated with the order
        $this->productStatisticRepository->updateSalesCount($order->product);
    }

    /**
     * Handle the Order "deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function deleted(Order $order)
    {
        // Update sales count for the product associated with the order
        $this->productStatisticRepository->updateSalesCount($order->product);
    }

    /**
     * Handle the Order "restored" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function restored(Order $order)
    {
        // Update sales count for the product associated with the order
        $this->productStatisticRepository->updateSalesCount($order->product);
    }

    /**
     * Handle the Order "force deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        // Update sales count for the product associated with the order
        $this->productStatisticRepository->updateSalesCount($order->product);
    }
}
