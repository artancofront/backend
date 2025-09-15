<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="StoreAddressRequest",
 *     type="object",
 *     required={
 *         "title",
 *         "first_name",
 *         "last_name",
 *         "phone",
 *         "email",
 *         "country",
 *         "province",
 *         "city",
 *         "postal_code",
 *         "address_line_1"
 *     },
 *     @OA\Property(property="title", type="string", maxLength=100, example="Home"),
 *     @OA\Property(property="first_name", type="string", maxLength=100, example="John"),
 *     @OA\Property(property="last_name", type="string", maxLength=100, example="Doe"),
 *     @OA\Property(property="phone", type="string", maxLength=20, example="+1234567890"),
 *     @OA\Property(property="email", type="string", format="email", maxLength=255, example="john.doe@example.com"),
 *     @OA\Property(property="country", type="string", maxLength=100, example="USA"),
 *     @OA\Property(property="province", type="string", maxLength=100, example="California"),
 *     @OA\Property(property="city", type="string", maxLength=100, example="Los Angeles"),
 *     @OA\Property(property="postal_code", type="string", maxLength=20, example="90001"),
 *     @OA\Property(property="address_line_1", type="string", maxLength=255, example="123 Main St"),
 *     @OA\Property(property="address_line_2", type="string", maxLength=255, nullable=true, example="Apt 4B"),
 *     @OA\Property(property="is_default", type="boolean", example=true)
 * )
 */

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('customer')->check();
    }

    public function rules(): array
    {
        return [
            'title'          => 'required|string|max:100',
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'phone'          => 'required|string|max:20',
            'email'          => 'required|email|max:255',
            'country'        => 'required|string|max:100',
            'province'       => 'required|string|max:100',
            'city'           => 'required|string|max:100',
            'postal_code'    => 'required|string|max:20',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'is_default'     => 'boolean',
        ];
    }
}
