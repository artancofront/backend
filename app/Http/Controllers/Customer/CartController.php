<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Customer Cart",
 *     description="Shopping cart management"
 * )
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
     *     path="/api/customer/cart",
     *     summary="Get all items in the customer's cart",
     *     tags={"Customer Cart"},
     *     @OA\Response(
     *         response=200,
     *         description="List of cart items"
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $customerId=Auth::guard('customer')->user()->id;
        $items = $this->cartService->getCartItems($customerId);
        return response()->json($items);
    }

    /**
     * @OA\Post(
     *     path="/api/customer/cart",
     *     summary="Add product to cart",
     *     tags={"Customer Cart"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id", "quantity"},
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
        $customerId=Auth::guard('customer')->user()->id;

        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = $this->cartService->addToCart(
            $customerId,
            $request->product_id,
            $request->quantity
        );

        return response()->json($item);
    }

    /**
     * @OA\Put(
     *     path="/api/customer/cart/{itemId}",
     *     summary="Update quantity of a cart item",
     *     tags={"Customer Cart"},
     *     @OA\Parameter(
     *         name="itemId",
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
        $customerId=Auth::guard('customer')->user()->id;

        $updated = $this->cartService->updateCartItem($cartId,$customerId, $request->quantity);
        return response()->json(['success' => $updated]);
    }

    /**
     * @OA\Delete(
     *     path="/api/customer/cart/{itemId}",
     *     summary="Remove item from cart",
     *     tags={"Customer Cart"},
     *     @OA\Parameter(
     *         name="itemId",
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
        $customerId=Auth::guard('customer')->user()->id;
        $deleted = $this->cartService->removeFromCart($cartId, $customerId);
        return response()->json(['success' => $deleted]);
    }

    /**
     * @OA\Delete(
     *     path="/api/customer/cart",
     *     summary="Clear all items from the customer's cart",
     *     tags={"Customer Cart"},
     *     @OA\Response(
     *         response=200,
     *         description="Cart cleared"
     *     )
     * )
     */
    public function clear(): JsonResponse
    {
        $customerId=Auth::guard('customer')->user()->id;
        $cleared = $this->cartService->clearCart($customerId);
        return response()->json(['success' => $cleared]);
    }

    /**
     * @OA\Get(
     *     path="/api/customer/cart/summary",
     *     summary="Get cart subtotal and total quantity",
     *     tags={"Customer Cart"},
     *     @OA\Response(
     *         response=200,
     *         description="Cart summary"
     *     )
     * )
     */
    public function summary(): JsonResponse
    {

        $customerId=Auth::guard('customer')->user()->id;

        $summary = $this->cartService->getCartSummary($customerId);
        return response()->json($summary);
    }
}
