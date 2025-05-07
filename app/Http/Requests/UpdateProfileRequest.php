<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="UpdateProfileRequest",
 *     type="object",
 *     title="Update Profile Request",
 *     description="Request body for updating user profile",
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         maxLength=255,
 *         nullable=true,
 *         example="John Doe"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         maxLength=255,
 *         nullable=true,
 *         example="john@example.com"
 *     )
 * )
 */

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // By default, we return true so any authenticated user can update their data.
        // You can modify this for role-based authorization if needed.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',  // Name is optional and should be a string.
            'email' => 'nullable|string|email|max:255|unique:users,email,'. $this->route('id'),  // Email is optional but must be unique except for the current user.

        ];

    }

}
