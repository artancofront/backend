<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="VariantRequest",
 *     required={"price", "sku", "stock", "is_active", "attributes"},
 *
 *     @OA\Property(property="price", type="number", format="float", example=149.99),
 *     @OA\Property(property="sku", type="string", example="VARIANT123"),
 *     @OA\Property(property="stock", type="integer", example=30),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *
 *     @OA\Property(
 *         property="attributes",
 *         type="array",
 *         @OA\Items(
 *             @OA\Property(property="category_attribute_id", type="integer", example=1),
 *             @OA\Property(property="category_attribute_value_id", type="integer", example=3)
 *         )
 *     )
 * )
 */

class VariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Add auth logic if needed
    }

    public function rules(): array
    {
        return [
            'price' => 'required|numeric',
            'sku' => 'required|string|max:255',
            'stock' => 'required|integer',
            'is_active' => 'required|boolean',
            'attributes' => 'required|array',
            'attributes.*.category_attribute_id' => 'required|integer|exists:category_attributes,id',
            'attributes.*.category_attribute_value_id' => 'required|integer|exists:category_attribute_values,id',
        ];
    }
}
