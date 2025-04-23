<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\ShipmentService;
use App\Http\Requests\StoreShipmentRequest;
use App\Http\Requests\UpdateShipmentRequest;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Shipments",
 *     description="Operations related to customer shipments"
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
     *     path="/api/shipments/{shipmentId}",
     *     summary="Get shipment details",
     *     tags={"Shipments"},
     *     @OA\Parameter(
     *         name="shipmentId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the shipment"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Shipment details",
     *         @OA\JsonContent(ref="#/components/schemas/Shipment")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="You do not have permission to view this shipment.")
     *         )
     *     )
     * )
     */
    public function show($shipmentId)
    {
        $shipment = $this->shipmentService->getShipmentById($shipmentId);

        if (!$shipment || $shipment->order->customer_id !== auth('customer')->id()) {
            return response()->json(['error' => 'You do not have permission to view this shipment.'], 403);
        }

        return response()->json($shipment);
    }

    /**
     * @OA\Post(
     *     path="/api/shipments",
     *     summary="Create a new shipment",
     *     tags={"Shipments"},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/StoreShipmentRequest")
     *      ),
     *      @OA\Response(response=201, description="Shipment created")
     *  )
 */
    public function store(StoreShipmentRequest $request)
    {
        $data = $request->validated();

        if ($request->user('customer')->id !== $request->order->customer_id) {
            return response()->json(['error' => 'You do not have permission to create a shipment for this order.'], 403);
        }

        $shipment = $this->shipmentService->createShipment($data);

        return response()->json(['message' => 'Shipment created successfully', 'data' => $shipment], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/shipments/{shipmentId}",
     *     summary="Update shipment details",
     *     tags={"Shipments"},
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
    public function update(UpdateShipmentRequest $request, $shipmentId)
    {
        $data = $request->validated();

        $shipment = $this->shipmentService->getShipmentById($shipmentId);

        if (!$shipment || $shipment->order->customer_id !== auth('customer')->id()) {
            return response()->json(['error' => 'You do not have permission to update this shipment.'], 403);
        }

        $updated = $this->shipmentService->updateShipment($shipmentId, $data);

        return response()->json(['message' => 'Shipment updated successfully', 'data' => $updated]);
    }

    /**
     * @OA\Get(
     *     path="/api/carriers",
     *     summary="Get all carriers",
     *     tags={"Shipments"},
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
}
