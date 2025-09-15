<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Admin - Customers",
 *     description="Customer management endpoints for the admin panel"
 * )
 */
class CustomerController extends Controller
{
    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * @OA\Get(
     *     path="/api/admin/customers",
     *     summary="Get all customers",
     *     tags={"Admin - Customers"},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Number of customers per page",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of customers",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Customer"))
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $customers = $this->customerService->paginate($request->query('per_page', 10));

        return response()->json(['success' => true, 'data' => $customers], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/customers/{id}",
     *     summary="Get single customer by ID",
     *     tags={"Admin - Customers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customer found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Customer")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Customer not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $customer = $this->customerService->findById($id);

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['success' => true, 'data' => $customer], Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/customers/{id}",
     *     summary="Update customer",
     *     tags={"Admin - Customers"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/Customer")),
     *     @OA\Response(
     *         response=200,
     *         description="Customer updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Customer updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Customer")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Customer not found")
     * )
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $customer = $this->customerService->findById($id);

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        $updatedCustomer = $this->customerService->update($customer, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully',
            'data' => $updatedCustomer
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/customers/{id}",
     *     summary="Delete customer",
     *     tags={"Admin - Customers"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Customer deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Customer deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Customer not found")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $customer = $this->customerService->findById($id);

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        $this->customerService->delete($customer);

        return response()->json(['success' => true, 'message' => 'Customer deleted successfully'], Response::HTTP_OK);
    }
}
