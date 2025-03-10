<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'email' => 'nullable|string|email|max:255|unique:users,email'. $this->route('id'),  // Email is optional but must be unique except for the current user.
            'password' => 'nullable|string|min:8', // Password is optional and should be a string.
            'phone' => 'required|string|min:10|max:15|unique:users,phone'. $this->route('id'),  // Phone is required and must be unique except for the current user.
            'role_id' => 'nullable|exists:roles,id',  // Role ID is optional but should exist in the `roles` table.

        ];

    }

}
