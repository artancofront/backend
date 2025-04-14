<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="UpdateProductRequest",
 *     @OA\Property(property="name", type="string", example="Sample Product"),
 *     @OA\Property(property="slug", type="string", example="sample-product"),
 *     @OA\Property(property="description", type="string", example="Product description here."),
 *     @OA\Property(property="weight", type="number", format="float", example=1.5),
 *     @OA\Property(property="length", type="number", format="float", example=10),
 *     @OA\Property(property="width", type="number", format="float", example=5),
 *     @OA\Property(property="height", type="number", format="float", example=2),
 *     @OA\Property(property="stock", type="integer", example=50),
 *     @OA\Property(property="sku", type="string", example="SKU12345"),
 *     @OA\Property(property="price", type="number", format="float", example=199.99),
 *     @OA\Property(property="category_id", type="integer", example=3),
 *
 *     @OA\Property(
 *         property="attributes",
 *         type="array",
 *         @OA\Items(
 *             @OA\Property(property="category_attribute_id", type="integer", example=1),
 *             @OA\Property(property="category_attribute_value_id", type="integer", example=10)
 *         )
 *     ),
 *
 *     @OA\Property(
 *         property="variants",
 *         type="array",
 *         @OA\Items(
 *             @OA\Property(property="price", type="number", example=99.99),
 *             @OA\Property(property="sku", type="string", example="VAR123"),
 *             @OA\Property(property="stock", type="integer", example=20),
 *             @OA\Property(property="is_active", type="boolean", example=true),
 *             @OA\Property(
 *                 property="attributes",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="category_attribute_id", type="integer", example=2),
 *                     @OA\Property(property="category_attribute_value_id", type="integer", example=5)
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Property(
 *         property="discount",
 *         type="object",
 *         @OA\Property(property="discount_amount", type="number", example=10),
 *         @OA\Property(property="discount_percentage", type="number", example=5),
 *         @OA\Property(property="start_date", type="string", format="date", example="2025-04-01"),
 *         @OA\Property(property="end_date", type="string", format="date", example="2025-04-30")
 *     ),
 *
 *     @OA\Property(property="warranties", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="policies", type="array", @OA\Items(type="string")),
 *
 *     @OA\Property(
 *         property="images",
 *         type="array",
 *         @OA\Items(
 *             @OA\Property(property="image_path", type="string", example="/images/product1.jpg"),
 *             @OA\Property(property="order", type="integer", example=1),
 *             @OA\Property(property="is_primary", type="boolean", example=true)
 *         )
 *     ),
 *
 *     @OA\Property(property="specifications", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="expert_review", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="is_active", type="boolean", example=true)
 * )
 */

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('id');

        return [
            'name' => 'sometimes|required|string|max:255',
            'slug' => "sometimes|required|string|unique:products,slug,{$productId}",
            'description' => 'nullable|string',
            'weight' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'stock' => 'sometimes|required|integer',
            'sku' => "sometimes|required|string|unique:products,sku,{$productId}",
            'price' => 'sometimes|required|numeric',
            'category_id' => 'sometimes|required|exists:categories,id',

            'attributes' => 'nullable|array',
            'attributes.*.category_attribute_id' => 'required|integer|exists:category_attributes,id',
            'attributes.*.category_attribute_value_id' => 'required|integer|exists:category_attribute_values,id',

            'variants' => 'nullable|array',
            'variants.*.price' => 'required|numeric',
            'variants.*.sku' => 'nullable|string',
            'variants.*.stock' => 'nullable|integer',
            'variants.*.is_active' => 'nullable|boolean',
            'variants.*.attributes' => 'nullable|array',
            'variants.*.attributes.*.category_attribute_id' => 'required|integer|exists:category_attributes,id',
            'variants.*.attributes.*.category_attribute_value_id' => 'required|integer|exists:category_attribute_values,id',

            'discount' => 'nullable|array',
            'discount.discount_amount' => 'nullable|numeric|min:0',
            'discount.discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount.start_date' => 'nullable|date',
            'discount.end_date' => 'nullable|date|after_or_equal:discount.start_date',

            'warranties' => 'nullable|array',
            'policies' => 'nullable|array',

            'images' => 'nullable|array',
            'images.*.image_path' => 'required|string',
            'images.*.order' => 'nullable|integer',
            'images.*.is_primary' => 'nullable|boolean',

            'specifications' => 'nullable|array',
            'expert_review' => 'nullable|array',

            'is_active' => 'nullable|boolean',
        ];
    }

}
