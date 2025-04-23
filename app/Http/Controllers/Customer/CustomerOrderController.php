<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(name="Customer Orders")
 */
class CustomerOrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->middleware('auth:customer');
        $this->orderService = $orderService;
    }

    /**
     * @OA\Get(
     *     path="/api/customer/orders",
     *     summary="Get all orders for the authenticated customer",
     *     tags={"Customer Orders"},
     *     security={{"customer":{}}},
     *     @OA\Response(response=200, description="List of customer's orders")
     * )
     */
    public function index(): JsonResponse
    {
        $customerId = Auth::guard('customer')->id();
        $orders = $this->orderService->getCustomerOrders($customerId);
        return response()->json($orders);
    }

    /**
     * @OA\Post(
     *     path="/api/customer/orders",
     *     summary="Create an order from the customer's cart",
     *     tags={"Customer Orders"},
     *     security={{"customer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"shipping_address_id"},
     *             @OA\Property(property="shipping_address_id", type="integer", example=1),
     *             @OA\Property(property="notes", type="string", example="Leave at the front door")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Order created from cart"),
     *     @OA\Response(response=400, description="Cart is empty or invalid input")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'shipping_address_id' => 'required|integer|exists:customer_addresses,id',
            'notes' => 'nullable|string',
        ]);

        $customerId = Auth::guard('customer')->id();
        $shippingAddressId = $request->input('shipping_address_id');
        $notes = $request->input('notes');

        try {
            $order = $this->orderService->createFromCart($customerId, $shippingAddressId, $notes);
            return response()->json($order, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/customer/orders/update-from-cart/{orderId}",
     *     summary="Update an order to match the customer's current cart",
     *     tags={"Customer Orders"},
     *     security={{"customer":{}}},
     *     @OA\Parameter(
     *         name="orderId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Order updated from cart"),
     *     @OA\Response(response=400, description="Cart is empty or update failed"),
     *     @OA\Response(response=403, description="Unauthorized")
     * )
     */
    public function update(int $orderId): JsonResponse
    {
        $customerId = Auth::guard('customer')->id();
        $order = $this->orderService->find($orderId);

        if (!$order || $order->customer_id !== $customerId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $order = $this->orderService->updateFromCart($order, $customerId);
            return response()->json($order);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
