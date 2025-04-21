<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class OrderRepository
{
    public function all(): Collection
    {
        return Order::all();
    }

    public function find(int $id)
    {
        return Order::find($id);
    }

    public function findByOrderNumber(string $orderNumber)
    {
        return Order::where('order_number', $orderNumber)->first();
    }

    public function create(array $data): Order
    {
        $order = Order::create($data);
        $order->update(['order_number' => $this->generateOrderNumber(),
                                 'expires_at' => now()->addMinutes(15)]);
        return $order;
    }

    private function generateOrderNumber(): string
    {
        return 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }

    public function update(Order $order, array $data): bool
    {
        return $order->update($data);
    }

    public function delete(Order $order): bool
    {
        return $order->delete();
    }

    public function getByCustomer(int $customerId): Collection
    {
        return Order::with('items')->where('customer_id', $customerId)
            ->get()
            ->groupBy('status');
    }

    public function getRecent(int $limit = 10): Collection
    {
        return Order::orderByDesc('created_at')->limit($limit)->get();
    }

    public function getExpired(): Collection
    {
        return Order::where('expires_at', '<', now()) // Expiry date has passed
        ->where('payment_method', 'online')       // Payment method is 'online'
        ->where('payment_status', 'unpaid')      // Payment status is 'unpaid'
        ->get();
    }

    public function getActive(): Collection
    {
        return Order::whereNotIn('status', ['delivered', 'cancelled', 'returned', 'expired'])
            ->where(function ($query) {
                $query->where('expires_at', '>', now())
                    ->orWhere('payment_method', '!=', 'online')
                    ->orWhere('payment_status', '!=', 'unpaid');
            })
            ->get();
    }


}
