<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShipmentRequest;
use App\Http\Requests\UpdateShipmentRequest;
use App\Models\Carrier;
use App\Services\ShipmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Admin Shipments",
 *     description="Admin management for shipments and carriers"
 * )
 */
class ShipmentController extends Controller
{
    protected ShipmentService $shipmentService;

    public function __construct(ShipmentService $shipmentService)
    {
        $this->shipmentService = $shipmentService;
    }

    /**
     * @OA\Get(
     *     path="/api/admin/shipments",
     *     summary="Get all shipments",
     *     tags={"Admin Shipments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="List of shipments")
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json($this->shipmentService->getAllShipments());
    }

    /**
     * @OA\Get(
     *     path="/api/admin/shipments/{id}",
     *     summary="Get shipment by ID",
     *     tags={"Admin Shipments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Shipment ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Shipment details"),
     *     @OA\Response(response=404, description="Shipment not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $shipment = $this->shipmentService->getShipmentById($id);
        if (!$shipment) {
            return response()->json(['message' => 'Shipment not found'], 404);
        }

        return response()->json($shipment);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/shipments",
     *     summary="Create a new shipment",
     *     tags={"Admin Shipments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreShipmentRequest")
     *     ),
     *     @OA\Response(response=201, description="Shipment created")
     * )
     */
    public function store(StoreShipmentRequest $request): JsonResponse
    {
        $shipment = $this->shipmentService->createShipment($request->validated());
        return response()->json($shipment, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/shipments/{id}",
     *     summary="Update an existing shipment",
     *     tags={"Admin Shipments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Shipment ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateShipmentRequest")
     *     ),
     *     @OA\Response(response=200, description="Shipment updated"),
     *     @OA\Response(response=404, description="Shipment not found")
     * )
     */
    public function update(UpdateShipmentRequest $request, int $id): JsonResponse
    {
        $shipment = $this->shipmentService->updateShipment($id, $request->validated());

        if (!$shipment) {
            return response()->json(['message' => 'Shipment not found'], 404);
        }

        return response()->json($shipment);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/shipments/{id}",
     *     summary="Delete a shipment",
     *     tags={"Admin Shipments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Shipment ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Shipment deleted"),
     *     @OA\Response(response=404, description="Shipment not found")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->shipmentService->deleteShipment($id);

        if (!$deleted) {
            return response()->json(['message' => 'Shipment not found'], 404);
        }

        return response()->json([], 204);
    }




    /**
     * @OA\Get(
     *     path="/api/admin/carriers",
     *     summary="Get all carriers",
     *     tags={"Admin Shipments"},
     *     @OA\Response(
     *         response=200,
     *         description="List of all carriers",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Carrier"))
     *     )
     * )
     */
    public function getAllCarriers(): JsonResponse
    {
        $carriers = $this->shipmentService->getAllCarriers();

        return response()->json([
            'data' => $carriers
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/carriers/{id}",
     *     summary="Get carrier details by ID",
     *     tags={"Admin Shipments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the carrier"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Carrier details",
     *         @OA\JsonContent(ref="#/components/schemas/Carrier")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Carrier not found"
     *     )
     * )
     */
    public function getCarrierById(int $id): JsonResponse
    {
        $carrier = $this->shipmentService->getCarrierById($id);

        if (!$carrier) {
            return response()->json([
                'error' => 'Carrier not found'
            ], 404);
        }

        return response()->json([
            'data' => $carrier
        ], 200);
    }


    /**
     * @OA\Post(
     *     path="/api/admin/carriers",
     *     summary="Create a new carrier",
     *     tags={"Admin Shipments"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", description="Carrier name"),
     *             @OA\Property(property="tracking_url", type="string", description="Carrier tracking URL"),
     *             @OA\Property(property="contact_number", type="string", description="Carrier contact number"),
     *             @OA\Property(property="is_active", type="boolean", description="Carrier status (active or inactive)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Carrier created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Carrier")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid data provided"
     *     )
     * )
     */
    public function storeCarrier(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'tracking_url' => 'nullable|url|max:255',
            'contact_number' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        $carrier = $this->shipmentService->createCarrier($data);

        return response()->json(['message' => 'Carrier created successfully', 'data' => $carrier], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/carriers/{id}",
     *     summary="Update carrier details",
     *     tags={"Admin Shipments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the carrier"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", description="Carrier name"),
     *             @OA\Property(property="tracking_url", type="string", description="Carrier tracking URL"),
     *             @OA\Property(property="contact_number", type="string", description="Carrier contact number"),
     *             @OA\Property(property="is_active", type="boolean", description="Carrier status (active or inactive)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Carrier updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Carrier")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Carrier not found"
     *     )
     * )
     */
    public function updateCarrier(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'tracking_url' => 'nullable|url|max:255',
            'contact_number' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);
        $carrier = $this->shipmentService->updateCarrier($id,$data);

        return response()->json(['message' => 'Carrier updated successfully', 'data' => $carrier]);
    }




    /**
     * @OA\Delete(
     *     path="/api/admin/carriers/{id}",
     *     summary="Delete a carrier",
     *     tags={"Admin Shipments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the carrier"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Carrier deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Carrier not found"
     *     )
     * )
     */
    public function deleteCarrier(int $id)
    {
        $deleted = $this->shipmentService->deleteCarrier($id);

        if (!$deleted) {
            return response()->json(['error' => 'Carrier not found'], 404);
        }

        return response()->json(['message' => 'Carrier deleted successfully']);
    }


}
