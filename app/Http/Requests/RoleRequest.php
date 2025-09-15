<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
/**
 * @OA\RequestBody(
 *     request="StoreOrUpdateRoleRequest",
 *     required=true,
 *     description="Role creation or update payload",
 *     @OA\JsonContent(
 *         required={"name"},
 *         @OA\Property(
 *             property="name",
 *             type="string",
 *             example="Admin",
 *             description="Role name"
 *         ),
 *         @OA\Property(
 *             property="description",
 *             type="string",
 *             example="Administrator with full access",
 *             description="Description of the role"
 *         ),
 *         @OA\Property(
 *             property="permissions",
 *             type="object",
 *             description="Permissions grouped by resource",
 *             @OA\AdditionalProperties(
 *                 type="array",
 *                 @OA\Items(
 *                     type="string",
 *                     enum={"read", "create", "update", "delete", "full"}
 *                 )
 *             ),
 *             example={
 *                 "users": {"read", "create", "update"},
 *                 "products": {"delete"},
 *                 "categories": {"full"}
 *             }
 *         )
 *     )
 * )
 */

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Update as needed for authorization logic
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['array'],
            'permissions.*.*' => ['string', Rule::in(['read', 'create', 'update', 'delete', 'full'])],
        ];
    }

    public function messages(): array
    {
        return [
            'permissions.*.*.in' => 'Each permission action must be one of: read, create, update, delete, full.',
        ];
    }
}
