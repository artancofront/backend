<?php

namespace App\Services;

use App\Exceptions\Cart\StockUnavailableException;
use App\Models\Cart;
use App\Models\Product;
use App\Repositories\CartRepository;
use Illuminate\Support\Collection;

class CartService
{
    protected CartRepository $cartRepository;

    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    public function getCartItems(int $customerId): Collection
    {
        return $this->cartRepository->getCustomerCartItems($customerId);
    }

    public function addToCart(int $customerId, int $productId, int $quantity): Cart
    {
        $product = Product::findOrFail($productId);
        $existingItem = $this->cartRepository->getItem($customerId, $productId);
        $totalQuantity = $existingItem
            ? $existingItem->quantity + $quantity
            : $quantity;

        $this->checkStockAvailability($product,$totalQuantity);


        return $this->cartRepository->addOrUpdate($customerId, $productId, $totalQuantity);
    }


    public function updateCartItem(int $cartId, int $quantity): bool
    {
        $cartItem = Cart::with('product')->findOrFail($cartId);
        $product = $cartItem->product;

        $this->checkStockAvailability($product,$quantity);

        return $this->cartRepository->updateQuantity($cartId, $quantity);
    }

    private function checkStockAvailability(Product $product, int $quantity): void
    {
        if (!$product->is_active || $product->stock < $quantity) {
            throw new StockUnavailableException("Insufficient stock for product: {$product->name}");
        }
    }


    public function removeFromCart(int $cartId): bool
    {
        return $this->cartRepository->removeItem($cartId);
    }

    public function clearCart(int $customerId): bool
    {
        return $this->cartRepository->clearCustomerCart($customerId);
    }

    public function getCartSummary(int $customerId): array
    {
        $items = $this->getCartItems($customerId);

        $subtotal = 0; // based on final_price
        $total = 0;    // based on original price
        $totalQuantity = 0;

        foreach ($items as $item) {
            $price = $item->product->price;
            $finalPrice = $item->product->final_price ?? $price;

            $quantity = $item->quantity;
            $total += $price * $quantity;
            $subtotal += $finalPrice * $quantity;
            $totalQuantity += $quantity;
        }

        return [
            'items' => $items,
            'total' => round($total, 2),
            'subtotal' => round($subtotal, 2),
            'total_discount' => round($total - $subtotal, 2),
            'total_quantity' => $totalQuantity,
        ];
    }

}
