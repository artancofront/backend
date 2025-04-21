<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RestoreStockForExpiredOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function handle()
    {
        // Get all expired orders
        $expiredOrders = $this->orderService->getExpiredOrders();
        foreach ($expiredOrders as $order) {
            // Restore stock for the expired order
            $this->orderService->restoreStockForOrder($order);

            // Optionally mark the order as expired
            $this->orderService->update($order, ['status' => 'expired']);
        }
    }
}
