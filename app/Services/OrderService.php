<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\OrderRepository;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected OrderRepository $orderRepository;
    protected CartService $cartService;

    private const VALID_STATUSES = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'returned', 'expired'];
    private const VALID_PAYMENT_STATUSES = ['unpaid', 'paid', 'refunded'];
    private const VALID_PAYMENT_METHODS = ['at_delivery', 'P2P', 'online'];

    public function __construct(OrderRepository $orderRepository, CartService $cartService)
    {
        $this->orderRepository = $orderRepository;
        $this->cartService = $cartService;
    }

    public function getAll(): Collection
    {
        return $this->orderRepository->all();
    }

    public function find(int $id)
    {
        return $this->orderRepository->find($id);
    }

    public function findByOrderNumber(string $orderNumber)
    {
        return $this->orderRepository->findByOrderNumber($orderNumber);
    }

    public function create(array $data)
    {
        return $this->orderRepository->create($data);
    }


    public function update(Order $order, array $data): bool
    {
        return $this->orderRepository->update($order, $data);
    }

    public function delete(Order $order): bool
    {
        return $this->orderRepository->delete($order);
    }

    public function getCustomerOrders(int $customerId): Collection
    {
        return $this->orderRepository->getByCustomer($customerId);
    }

    public function getRecentOrders(int $limit = 10): Collection
    {
        return $this->orderRepository->getRecent($limit);
    }

    public function getExpiredOrders(): Collection
    {
        return $this->orderRepository->getExpired();
    }

    public function getActiveOrders(): Collection
    {
        return $this->orderRepository->getActive();
    }

    public function markAsPaid(Order $order, string $method): bool
    {
        return $this->orderRepository->update($order, [
            'payment_status' => 'paid',
            'payment_method' => $method,
            'status' => 'processing',
            'placed_at' => now(),
        ]);
    }

    public function reserveStockForOrder(Order $order): void
    {
        foreach ($order->items as $item) {
            $product = $item->product;
            if ($product->stock < $item->quantity) {
                throw new \Exception("Insufficient stock for {$product->name}");
            }
            if($item->quantity > 0){
                $product->decrement('stock', $item->quantity);
            }
        }
    }

    public function restoreStockForOrder(Order $order): void
    {
        $order->load('items');
        foreach ($order->items as $item) {
            $product = $item->product;
            if($item->quantity > 0){
                $product->increment('stock', $item->quantity);
            }
        }
    }


    public function createFromCart(int $customerId, int $shippingAddressId, ?string $notes = null): Order
    {
        $summary = $this->cartService->getCartSummary($customerId);
        $items = $summary['items'];

        if ($items->isEmpty()) {
            throw new Exception("Cart is empty.");
        }

        $totals = $this->calculateTotals($summary);

        return DB::transaction(function () use (
            $customerId,
            $shippingAddressId,
            $items,
            $totals,
            $notes
        ) {
            $order = $this->orderRepository->create([
                'customer_id' => $customerId,
                'shipping_address_id' => $shippingAddressId,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'subtotal' => $totals['subtotal'],
                'discount' => $totals['discount'],
                'tax' => $totals['tax'],
                'shipping_cost' => null,
                'total' => $totals['finalTotal'] ,
                'notes' => $notes,
            ]);

            $this->createOrderItems($order, $items);

            return $order->load('items');
        });
    }

    public function updateFromCart(Order $order, int $customerId): Order
    {
        $summary = $this->cartService->getCartSummary($customerId);
        $items = $summary['items'];

        if ($items->isEmpty()) {
            throw new Exception("Cart is empty.");
        }

        $totals = $this->calculateTotals($summary);
        $shippingCost = $order->shipping_cost ?? 0;

        return DB::transaction(function () use (
            $order,
            $items,
            $totals,
            $shippingCost
        ) {

            $this->orderRepository->update($order, [
                'subtotal' => $totals['subtotal'],
                'discount' => $totals['discount'],
                'tax' => $totals['tax'],
                'shipping_cost' => $shippingCost,
                'total' => $totals['finalTotal'] + $shippingCost,
            ]);

            $this->restoreStockForOrder($order);
            $order->items()->delete();
            $this->createOrderItems($order, $items);

            return $order->load('items');
        });
    }

    private function calculateTotals(array $summary): array
    {
        $subtotal = $summary['subtotal'];
        $discount = $summary['total_discount'];
        $tax = round($subtotal * 0, 2);
        $finalTotal = $subtotal + $tax;

        return compact('subtotal', 'discount', 'tax', 'finalTotal');
    }

    public function createOrderItems(Order $order,Collection  $items): void
    {
        foreach ($items as $item) {
            $product = $item->product;
            $finalPrice = $product->final_price ?? $product->price;

            $order->items()->create([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'price' => $finalPrice,
                'quantity' => $item->quantity,
                'total' => $finalPrice * $item->quantity,
            ]);
        }
        $this->reserveStockForOrder($order);
    }

    public function deleteOrderItem(Order $order,OrderItem $id): void
    {
        $order->items()->delete($id);

        $this->restoreStockForOrder($order);
    }

    public function updateStatus(Order $order, string $status): bool
    {
        $validStatuses = self::VALID_STATUSES;

        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException("Invalid order status: $status");
        }

        return $this->orderRepository->update($order, ['status' => $status]);
    }

    public function updatePaymentStatus(Order $order, string $status): bool
    {
        $validStatuses = self::VALID_PAYMENT_STATUSES;

        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException("Invalid payment status: $status");
        }

        return $this->orderRepository->update($order, ['payment_status' => $status]);
    }

    public function updatePaymentMethod(Order $order, string $method): bool
    {
        $validMethods = self::VALID_PAYMENT_METHODS;

        if (!in_array($method, $validMethods)) {
            throw new \InvalidArgumentException("Invalid payment method: $method");
        }

        return $this->orderRepository->update($order, ['payment_method' => $method]);
    }

}
