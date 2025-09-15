<?php

namespace App\Repositories;

use App\Models\Shipment;
use App\Models\Carrier;
use Illuminate\Database\Eloquent\Collection;

class ShipmentRepository
{
    /*
    |--------------------------------------------------------------------------
    | Shipment Methods
    |--------------------------------------------------------------------------
    */

    public function allShipments(): Collection
    {
        return Shipment::with(['order', 'carrier'])->get();
    }

    public function findShipment(int $id): ?Shipment
    {
        return Shipment::with(['order', 'carrier'])->find($id);
    }

    public function createShipment(array $data): Shipment
    {
        return Shipment::create($data);
    }

    public function updateShipment(Shipment $shipment, array $data): Shipment
    {
        $shipment->update($data);
        return $shipment;
    }

    public function deleteShipment(Shipment $shipment): void
    {
        $shipment->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | Carrier Methods
    |--------------------------------------------------------------------------
    */

    public function allCarriers(): Collection
    {
        return Carrier::all();
    }

    public function findCarrier(int $id): ?Carrier
    {
        return Carrier::find($id);
    }

    public function createCarrier(array $data): Carrier
    {
        return Carrier::create($data);
    }

    public function updateCarrier(Carrier $carrier, array $data): Carrier
    {
        $carrier->update($data);
        return $carrier;
    }

    public function deleteCarrier(Carrier $carrier): void
    {
        $carrier->delete();
    }
}
