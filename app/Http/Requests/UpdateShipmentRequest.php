<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UpdateShipmentRequest",
 *     @OA\Property(property="carrier_id", type="integer", example=1),
 *     @OA\Property(property="shipping_address_id", type="integer", example=1),
 *     @OA\Property(property="tracking_number", type="string", example="TRACK123456"),
 *     @OA\Property(property="cost", type="float", example=50.99),
 *     @OA\Property(property="status", type="string", example="shipped", enum={"pending", "shipped", "delivered", "cancelled"}),
 *     @OA\Property(property="shipped_at", type="string", format="date-time", example="2025-04-20T12:00:00Z"),
 *     @OA\Property(property="delivered_at", type="string", format="date-time", example="2025-04-22T16:45:00Z")
 * )
 */
class UpdateShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'carrier_id' => ['sometimes', 'integer', 'exists:carriers,id'],
            'shipping_address_id' => ['sometimes', 'integer'],
            'tracking_number' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'in:pending,shipped,delivered,cancelled'],
            'cost' => ['sometimes', 'numeric'],
            'shipped_at' => ['nullable', 'date'],
            'delivered_at' => ['nullable', 'date'],
        ];
    }
}
