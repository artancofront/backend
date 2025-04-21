<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Collection;

class CartRepository
{
    public function getCustomerCartItems(int $customerId): Collection
    {
        return Cart::with('product')->where('customer_id', $customerId)->get();
    }

    public function getItem(int $customerId, int $productId): ?Cart
    {
        return Cart::where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->first();
    }

    public function addOrUpdate(int $customerId, int $productId, int $quantity): Cart
    {
        $cartItem = $this->getItem($customerId, $productId);

        if ($cartItem) {
            $cartItem->quantity = $quantity;
            $cartItem->save();
        } else {
            $cartItem = Cart::create([
                'customer_id' => $customerId,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        return $cartItem->load('product');
    }

    public function updateQuantity(int $cartId, int $quantity): bool
    {
        return Cart::where('id', $cartId)->update(['quantity' => $quantity]);
    }

    public function removeItem(int $cartId): bool
    {
        return Cart::where('id', $cartId)->delete();
    }

    public function clearCustomerCart(int $customerId): bool
    {
        return Cart::where('customer_id', $customerId)->delete();
    }
}
