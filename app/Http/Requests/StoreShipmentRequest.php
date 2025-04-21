<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreShipmentRequest",
 *     required={"order_id", "carrier_id", "tracking_number", "status"},
 *     @OA\Property(property="order_id", type="integer", example=123),
 *     @OA\Property(property="carrier_id", type="integer", example=1),
 *     @OA\Property(property="tracking_number", type="string", example="TRACK123456"),
 *     @OA\Property(property="status", type="string", example="pending", enum={"pending", "shipped", "delivered", "cancelled"}),
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
            'tracking_number' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:pending,shipped,delivered,cancelled'],
            'shipped_at' => ['nullable', 'date'],
            'delivered_at' => ['nullable', 'date'],
        ];
    }
}
