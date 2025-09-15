<?php

namespace App\Services;

use App\Models\Order;

class PricingService
{
    /**
     * Calculate all prices and update the order's monetary fields.
     */
    public function apply(Order $order): Order
    {
        $subtotal = $this->calculateSubtotal($order);
        $discount = $this->calculateDiscount($order);
        //$tax = $this->calculateTax($order, $subtotal - $discount);
        $shipping = $this->calculateShipping($order);
        $total = $subtotal - $discount + $shipping;

        $order->subtotal = $subtotal;
        $order->discount = $discount;
        //$order->tax = $tax;
        $order->shipping_cost = $shipping;
        $order->total = $total;

        return $order;
    }

    protected function calculateSubtotal(Order $order): float
    {
        return $order->items->sum(function ($item) {
            return $item->unit_price * $item->quantity;
        });
    }

    protected function calculateDiscount(Order $order): float
    {
        // Example: apply a fixed coupon or percentage
        return 0.0; // Implement logic here
    }

    protected function calculateTax(Order $order, float $taxableAmount): float
    {
        // Example: 9% tax
        return $taxableAmount * 0.09;
    }

    protected function calculateShipping(Order $order): float
    {
        // Example: Free shipping over 100
        return $order->subtotal >= 100 ? 0.0 : 10.0;
    }
}
