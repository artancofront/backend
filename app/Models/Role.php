<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Role",
 *     required={"name", "description", "permissions"},
 *     @OA\Property(property="name", type="string", description="Role name", example="Admin"),
 *     @OA\Property(property="description", type="string", description="Role description", example="Administrator with full access"),
 *     @OA\Property(
 *         property="permissions",
 *         type="object",
 *         description="Permissions grouped by resource like users, products, ..., actions: [full, read, create, update, delete]",
 *         example={
 *             "users": {"read", "create", "update", "delete"},
 *             "products": {"create", "delete"},
 *             "categories": {"full"},
 *         }
 *     )
 * )
 */
class Role extends Model
{
    use HasFactory;
    public const ALLOWED_ACTIONS = ['all', 'read', 'create', 'update', 'delete'];

    /**
     * The name of the table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'permissions',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'permissions' => 'array', // Casting 'permissions' to an array
    ];



    /**
     * Get the users associated with the role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Set permissions for a specific category (e.g., 'users').
     *
     * Only allows "read", "create", "update", "delete" actions.
     *
     * @param string $category
     * @param array $actions
     * @return void
     */
    public function setPermission(string $category, array $actions): void
    {
        $filteredActions = array_values(array_intersect($actions, self::ALLOWED_ACTIONS));
        $permissions = $this->permissions ?? [];

        if (empty($filteredActions)) {
            unset($permissions[$category]);
        } else {
            $permissions[$category] = $filteredActions;
        }

        $this->permissions = $permissions;
        $this->save();
    }


    /**
     * Check if the role has a specific permission in a category.
     *
     * @param string $category The category of permissions (e.g., 'users').
     * @param string $action The specific action (e.g., 'create').
     * @return bool
     */
    public function hasPermission(string $category, string $action): bool
    {
        $permissions = $this->permissions ?? [];

        return (
            (isset($permissions[$category]) && (
                    in_array($action, $permissions[$category]) ||
                    in_array('full', $permissions[$category])
                )) ||
            (isset($permissions['full']) && (
                    in_array('full', $permissions['full']) ||
                    in_array($action, $permissions['full'])
                ))
        );
    }



    /**
     * Get all permissions for the role.
     *
     * @return array
     */
    public function getPermissions(): array
    {
        // Return all permissions (categories with their respective actions)
        return $this->permissions ?? [];
    }
}

