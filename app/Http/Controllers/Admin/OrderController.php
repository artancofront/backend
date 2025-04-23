<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Orders",
 *     description="Operations related to orders"
 * )
 */
class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * @OA\Get(
     *     path="/admin/orders",
     *     summary="Display a listing of all orders",
     *     tags={"Orders"},
     *     @OA\Response(
     *         response=200,
     *         description="List of orders",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Order"))
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $orders = $this->orderService->getAll();
        return response()->json($orders);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/orders/{id}",
     *     summary="Display the specified order by ID",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order details",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $order = $this->orderService->find($id);
        return response()->json($order);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/orders/order-number/{orderNumber}",
     *     summary="Display the specified order by order number",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="orderNumber",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order details",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     )
     * )
     */
    public function showByOrderNumber(string $orderNumber): JsonResponse
    {
        $order = $this->orderService->findByOrderNumber($orderNumber);
        return response()->json($order);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/orders",
     *     summary="Create a new order",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/OrderRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $order = $this->orderService->create($data);
        return response()->json($order, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/orders/{id}",
     *     summary="Update the specified order",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/OrderRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order updated",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to update order"
     *     )
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->all();
        $order = $this->orderService->find($id);
        $updated = $this->orderService->update($order, $data);

        if ($updated) {
            return response()->json($order);
        }

        return response()->json(['message' => 'Failed to update order'], 400);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/orders/{id}",
     *     summary="Delete the specified order",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order deleted"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to delete order"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $order = $this->orderService->find($id);
        $deleted = $this->orderService->delete($order);

        if ($deleted) {
            return response()->json(['message' => 'Order deleted']);
        }

        return response()->json(['message' => 'Failed to delete order'], 400);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/orders/customer/{customerId}",
     *     summary="Get orders for a specific customer",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="customerId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customer's orders",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Order"))
     *     )
     * )
     */
    public function getCustomerOrders(int $customerId): JsonResponse
    {
        $orders = $this->orderService->getCustomerOrders($customerId);
        return response()->json($orders);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/orders/recent/{limit}",
     *     summary="Get the most recent orders",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="path",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of recent orders",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Order"))
     *     )
     * )
     */
    public function getRecentOrders(int $limit = 10): JsonResponse
    {
        $orders = $this->orderService->getRecentOrders($limit);
        return response()->json($orders);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/orders/expired",
     *     summary="Get expired orders",
     *     tags={"Orders"},
     *     @OA\Response(
     *         response=200,
     *         description="List of expired orders",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Order"))
     *     )
     * )
     */
    public function getExpiredOrders(): JsonResponse
    {
        $orders = $this->orderService->getExpiredOrders();
        return response()->json($orders);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/orders/active",
     *     summary="Get active orders",
     *     tags={"Orders"},
     *     @OA\Response(
     *         response=200,
     *         description="List of active orders",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Order"))
     *     )
     * )
     */
    public function getActiveOrders(): JsonResponse
    {
        $orders = $this->orderService->getActiveOrders();
        return response()->json($orders);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/orders/{id}/paid",
     *     summary="Mark an order as paid",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(type="object", @OA\Property(property="payment_method", type="string"))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order marked as paid"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to mark order as paid"
     *     )
     * )
     */
    public function markAsPaid(Request $request, int $id): JsonResponse
    {
        $method = $request->input('payment_method');
        $order = $this->orderService->find($id);
        $success = $this->orderService->markAsPaid($order, $method);

        if ($success) {
            return response()->json(['message' => 'Order marked as paid']);
        }

        return response()->json(['message' => 'Failed to mark order as paid'], 400);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/orders/{orderId}/items",
     *     summary="Create order items from cart items",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="orderId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(type="object", @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/OrderItem")))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order items created"
     *     )
     * )
     */
    public function createOrderItems(Request $request, int $orderId): JsonResponse
    {
        $order = $this->orderService->find($orderId);
        $items = $request->input('items');
        $this->orderService->createOrderItems($order, collect($items));
        return response()->json(['message' => 'Order items created']);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/orders/{orderId}/items/{itemId}",
     *     summary="Delete an order item",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="orderId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="itemId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order item deleted"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order item not found"
     *     )
     * )
     */
    public function deleteOrderItem(int $orderId, int $itemId): JsonResponse
    {
        $order = $this->orderService->find($orderId);
        $orderItem = $order->items()->find($itemId);

        if (!$orderItem) {
            return response()->json(['message' => 'Order item not found'], 404);
        }

        $this->orderService->deleteOrderItem($order, $orderItem);
        return response()->json(['message' => 'Order item deleted']);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/orders/{id}/status",
     *     summary="Update the status of an order",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(type="object", @OA\Property(property="status", type="string"))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order status updated"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to update order status"
     *     )
     * )
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $status = $request->input('status');
        $order = $this->orderService->find($id);
        $success = $this->orderService->updateStatus($order, $status);

        if ($success) {
            return response()->json(['message' => 'Order status updated']);
        }

        return response()->json(['message' => 'Failed to update order status'], 400);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/orders/{id}/payment-status",
     *     summary="Update the payment status of an order",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(type="object", @OA\Property(property="payment_status", type="string"))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment status updated"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to update payment status"
     *     )
     * )
     */
    public function updatePaymentStatus(Request $request, int $id): JsonResponse
    {
        $status = $request->input('payment_status');
        $order = $this->orderService->find($id);
        $success = $this->orderService->updatePaymentStatus($order, $status);

        if ($success) {
            return response()->json(['message' => 'Payment status updated']);
        }

        return response()->json(['message' => 'Failed to update payment status'], 400);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/orders/{id}/payment-method",
     *     summary="Update the payment method of an order",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(type="object", @OA\Property(property="payment_method", type="string"))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment method updated"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to update payment method"
     *     )
     * )
     */
    public function updatePaymentMethod(Request $request, int $id): JsonResponse
    {
        $method = $request->input('payment_method');
        $order = $this->orderService->find($id);
        $success = $this->orderService->updatePaymentMethod($order, $method);

        if ($success) {
            return response()->json(['message' => 'Payment method updated']);
        }

        return response()->json(['message' => 'Failed to update payment method'], 400);
    }
}
