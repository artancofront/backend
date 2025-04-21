<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\Carrier;
use App\Models\Order;
use App\Repositories\ShipmentRepository;
use Illuminate\Database\Eloquent\Collection;

class ShipmentService
{
    protected ShipmentRepository $shipmentRepository;

    public function __construct(ShipmentRepository $shipmentRepository)
    {
        $this->shipmentRepository = $shipmentRepository;
    }

    /*
    |--------------------------------------------------------------------------
    | Shipment Methods
    |--------------------------------------------------------------------------
    */

    public function getAllShipments(): Collection
    {
        return $this->shipmentRepository->allShipments();
    }

    public function getShipmentById(int $id): ?Shipment
    {
        return $this->shipmentRepository->findShipment($id);
    }

    public function createShipment(array $data): Shipment
    {
        $shipment = $this->shipmentRepository->createShipment($data);

        $this->updateOrderShipmentCost($shipment->order_id);

        return $shipment;
    }

    public function updateShipment(int $id, array $data): ?Shipment
    {
        $shipment = $this->shipmentRepository->findShipment($id);
        if (!$shipment) {
            return null;
        }

        $updated = $this->shipmentRepository->updateShipment($shipment, $data);

        $this->updateOrderShipmentCost($shipment->order_id);

        return $updated;
    }

    public function deleteShipment(int $id): bool
    {
        $shipment = $this->shipmentRepository->findShipment($id);
        if (!$shipment) {
            return false;
        }

        $orderId = $shipment->order_id;

        $this->shipmentRepository->deleteShipment($shipment);

        $this->updateOrderShipmentCost($orderId);

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Carrier Methods
    |--------------------------------------------------------------------------
    */

    public function getAllCarriers(): Collection
    {
        return $this->shipmentRepository->allCarriers();
    }

    public function getCarrierById(int $id): ?Carrier
    {
        return $this->shipmentRepository->findCarrier($id);
    }

    public function createCarrier(array $data): Carrier
    {
        return $this->shipmentRepository->createCarrier($data);
    }

    public function updateCarrier(int $id, array $data): ?Carrier
    {
        $carrier = $this->shipmentRepository->findCarrier($id);
        if (!$carrier) {
            return null;
        }

        return $this->shipmentRepository->updateCarrier($carrier, $data);
    }

    public function deleteCarrier(int $id): bool
    {
        $carrier = $this->shipmentRepository->findCarrier($id);
        if (!$carrier) {
            return false;
        }

        $this->shipmentRepository->deleteCarrier($carrier);
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Order Shipment Status Update
    |--------------------------------------------------------------------------
    */

    protected function updateOrderShipmentCost(int $orderId): void
    {
        $order = Order::with('shipments')->find($orderId);

        if (!$order) {
            return;
        }

        $order->shipment_cost = $order->shipment? $order->shipment->cost: null;
        $order->save();
    }
}
