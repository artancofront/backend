<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @OA\Schema(
 *     schema="StoreProductRequest",
 *     type="object",
 *     required={"name", "slug", "stock", "sku", "price", "category_id"},
 *     @OA\Property(property="name", type="string", maxLength=255, description="Product name"),
 *     @OA\Property(property="slug", type="string", description="URL-friendly product slug"),
 *     @OA\Property(property="description", type="string", nullable=true, description="Product description"),
 *     @OA\Property(property="weight", type="number", format="float", nullable=true, description="Product weight"),
 *     @OA\Property(property="length", type="number", format="float", nullable=true, description="Product length"),
 *     @OA\Property(property="width", type="number", format="float", nullable=true, description="Product width"),
 *     @OA\Property(property="height", type="number", format="float", nullable=true, description="Product height"),
 *     @OA\Property(property="stock", type="integer", description="Product stock count"),
 *     @OA\Property(property="sku", type="string", description="Unique SKU for the product"),
 *     @OA\Property(property="price", type="number", format="float", description="Product price"),
 *     @OA\Property(property="category_id", type="integer", description="Category ID the product belongs to"),
 * )
 */

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'regex:/^[\p{Arabic}a-zA-Z0-9\-]+$/u',
                'unique:products,slug',
            ],
            'description' => 'nullable|string',
            'weight' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'stock' => 'required|integer',
            'sku' => 'required|string|unique:products,sku',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
        ];
    }
}
