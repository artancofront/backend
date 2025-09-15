<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="OrderRequest",
 *     required={
 *         "customer_id", "shipping_address_id", "status", "payment_status",
 *         "payment_method", "subtotal", "shipping_cost", "total"
 *     },
 *     @OA\Property(property="customer_id", type="integer", example=1),
 *     @OA\Property(property="shipping_address_id", type="integer", example=10),
 *     @OA\Property(property="order_number", type="string", example="ORD-20250421-XYZ"),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="payment_status", type="string", example="unpaid"),
 *     @OA\Property(property="payment_method", type="string", example="online"),
 *     @OA\Property(property="subtotal", type="number", format="float", example=49.99),
 *     @OA\Property(property="discount", type="number", format="float", example=5.00),
 *     @OA\Property(property="tax", type="number", format="float", example=2.50),
 *     @OA\Property(property="shipping_cost", type="number", format="float", example=4.99),
 *     @OA\Property(property="total", type="number", format="float", example=52.48),
 *     @OA\Property(property="notes", type="string", example="Leave at front door."),
 *     @OA\Property(property="placed_at", type="string", format="date-time", example="2025-04-21T12:00:00Z"),
 *     @OA\Property(property="expires_at", type="string", format="date-time", example="2025-04-22T12:00:00Z"),
 *     @OA\Property(
 *         property="items",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/OrderItem")
 *     )
 * )
 */
class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'shipping_address_id' => ['required', 'integer', 'exists:customer_addresses,id'],
            'order_number' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string'],
            'payment_status' => ['required', 'string'],
            'payment_method' => ['required', 'string'],
            'subtotal' => ['required', 'numeric'],
            'discount' => ['nullable', 'numeric'],
            'tax' => ['nullable', 'numeric'],
            'shipping_cost' => ['required', 'numeric'],
            'total' => ['required', 'numeric'],
            'notes' => ['nullable', 'string'],
            'placed_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
            'items' => ['nullable', 'array'],
            'items.*.product_id' => ['required_with:items', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.price' => ['required_with:items', 'numeric'],
        ];
    }
}
