<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Cart",
 *     description="Shopping cart management"
 */
class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @OA\Get(
     *     path="/api/cart",
     *     summary="Get all items in the customer's cart",
     *     tags={"Cart"},
     *     @OA\Parameter(
     *         name="customer_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of cart items"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $customerId = (int) $request->query('customer_id');
        $items = $this->cartService->getCartItems($customerId);
        return response()->json($items);
    }

    /**
     * @OA\Post(
     *     path="/api/cart",
     *     summary="Add product to cart",
     *     tags={"Cart"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"customer_id", "product_id", "quantity"},
     *             @OA\Property(property="customer_id", type="integer"),
     *             @OA\Property(property="product_id", type="integer"),
     *             @OA\Property(property="quantity", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product added to cart"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => 'required|integer',
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = $this->cartService->addToCart(
            $request->customer_id,
            $request->product_id,
            $request->quantity
        );

        return response()->json($item);
    }

    /**
     * @OA\Put(
     *     path="/api/cart/{cartId}",
     *     summary="Update quantity of a cart item",
     *     tags={"Cart"},
     *     @OA\Parameter(
     *         name="cartId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"quantity"},
     *             @OA\Property(property="quantity", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart item updated"
     *     )
     * )
     */
    public function update(Request $request, int $cartId): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $updated = $this->cartService->updateCartItem($cartId, $request->quantity);
        return response()->json(['success' => $updated]);
    }

    /**
     * @OA\Delete(
     *     path="/api/cart/{cartId}",
     *     summary="Remove item from cart",
     *     tags={"Cart"},
     *     @OA\Parameter(
     *         name="cartId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item removed"
     *     )
     * )
     */
    public function destroy(int $cartId): JsonResponse
    {
        $deleted = $this->cartService->removeFromCart($cartId);
        return response()->json(['success' => $deleted]);
    }

    /**
     * @OA\Delete(
     *     path="/api/cart",
     *     summary="Clear all items from the customer's cart",
     *     tags={"Cart"},
     *     @OA\Parameter(
     *         name="customer_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart cleared"
     *     )
     * )
     */
    public function clear(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => 'required|integer'
        ]);

        $cleared = $this->cartService->clearCart($request->customer_id);
        return response()->json(['success' => $cleared]);
    }

    /**
     * @OA\Get(
     *     path="/api/cart/summary",
     *     summary="Get cart subtotal and total quantity",
     *     tags={"Cart"},
     *     @OA\Parameter(
     *         name="customer_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart summary"
     *     )
     * )
     */
    public function summary(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => 'required|integer'
        ]);

        $summary = $this->cartService->getCartSummary($request->customer_id);
        return response()->json($summary);
    }
}
