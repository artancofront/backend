<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreShipmentRequest",
 *     required={"order_id", "carrier_id","shipping_address_id", "tracking_number", "cost"},
 *     @OA\Property(property="order_id", type="integer", example=123),
 *     @OA\Property(property="carrier_id", type="integer", example=1),
 *     @OA\Property(property="shipping_address_id", type="integer", example=1),
 *     @OA\Property(property="tracking_number", type="string", example="TRACK123456"),
 *     @OA\Property(property="cost", type="float", example=50.99),
 *     @OA\Property(property="shipped_at", type="string", format="date-time", example="2025-04-20T12:00:00Z"),
 *     @OA\Property(property="delivered_at", type="string", format="date-time", example="2025-04-22T16:45:00Z")
 * )
 */
class StoreShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'carrier_id' => ['required', 'integer', 'exists:carriers,id'],
            'shipping_address_id' => ['required', 'integer'],
            'tracking_number' => ['required', 'string', 'max:255'],
            'cost' => ['required', 'numeric'],
            'shipped_at' => ['nullable', 'date'],
            'delivered_at' => ['nullable', 'date'],
        ];
    }
}
