<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="CategoryRequest",
 *     required={"name", "slug"},
 *     @OA\Property(property="name", type="string", maxLength=255, example="Electronics"),
 *     @OA\Property(property="slug", type="string", maxLength=255, example="electronics"),
 *     @OA\Property(property="parent_id", type="integer", nullable=true, example=1)
 * )
 */



class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Adjust authorization logic as needed
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'regex:/^[\p{Arabic}a-zA-Z0-9\-]+$/u', 'unique:categories,slug' . ($this->route('id') ? ',' . $this->route('id') : '')],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ];
    }

    /**
     * Customize error messages (optional).
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The category name is required.',
            'slug.required' => 'The slug is required.',
            'slug.unique' => 'This slug is already in use.',
            'parent_id.exists' => 'The selected parent category does not exist.',
        ];
    }
}
