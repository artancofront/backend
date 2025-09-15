<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Services\CustomerAddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CustomerAddressController extends Controller
{
    protected CustomerAddressService $addressService;

    public function __construct(CustomerAddressService $addressService)
    {
        $this->middleware('auth:customer');
        $this->addressService = $addressService;
    }

    /**
     * @OA\Get(
     *     path="/api/customer/addresses",
     *     summary="Get all addresses for the authenticated customer",
     *     tags={"Customer Addresses"},
     *     @OA\Response(response=200, description="List of addresses")
     * )
     */
    public function index(): JsonResponse
    {
        $customer = Auth::guard('customer')->user();
        $addresses = $this->addressService->getAddresses($customer);

        return response()->json($addresses);
    }

    /**
     * @OA\Get(
     *     path="/api/customer/addresses/{id}",
     *     summary="Get a specific address",
     *     tags={"Customer Addresses"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Address details"),
     *     @OA\Response(response=404, description="Address not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $customer = Auth::guard('customer')->user();
        $address = $this->addressService->getAddress($customer, $id);

        if (!$address) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        return response()->json($address);
    }

    /**
     * @OA\Post(
     *     path="/api/customer/addresses",
     *     summary="Store a new address",
     *     tags={"Customer Addresses"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreAddressRequest")
     *     ),
     *     @OA\Response(response=201, description="Address created")
     * )
     */
    public function store(StoreAddressRequest $request): JsonResponse
    {
        $customer = Auth::guard('customer')->user();
        $address = $this->addressService->createAddress($customer, $request->validated());

        return response()->json($address, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/customer/addresses/{id}",
     *     summary="Update an address",
     *     tags={"Customer Addresses"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateAddressRequest")
     *     ),
     *     @OA\Response(response=200, description="Address updated"),
     *     @OA\Response(response=404, description="Address not found")
     * )
     */
    public function update(UpdateAddressRequest $request, int $id): JsonResponse
    {
        $customer = Auth::guard('customer')->user();
        $address = $this->addressService->updateAddress($customer, $id, $request->validated());

        if (!$address) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        return response()->json($address);
    }

    /**
     * @OA\Delete(
     *     path="/api/customer/addresses/{id}",
     *     summary="Delete an address",
     *     tags={"Customer Addresses"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Address deleted successfully"),
     *     @OA\Response(response=404, description="Address not found")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $customer = Auth::guard('customer')->user();
        $deleted = $this->addressService->deleteAddress($customer, $id);

        if (!$deleted) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        return response()->json(['message' => 'Address deleted successfully']);
    }

    /**
     * @OA\Get(
     *     path="/api/customer/addresses/default",
     *     summary="Get the default address",
     *     tags={"Customer Addresses"},
     *     @OA\Response(response=200, description="Default address"),
     *     @OA\Response(response=404, description="No default address found")
     * )
     */
    public function default(): JsonResponse
    {
        $customer = Auth::guard('customer')->user();
        $address = $this->addressService->getDefaultAddress($customer);

        if (!$address) {
            return response()->json(['message' => 'No default address found'], 404);
        }

        return response()->json($address);
    }
}
