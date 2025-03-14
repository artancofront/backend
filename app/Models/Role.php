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
 *         description="Permissions grouped by resource",
 *         example={
 *             "users": {"create", "edit", "delete"},
 *             "posts": {"publish", "unpublish", "delete"},
 *             "projects": {"create", "update", "assign"}
 *         }
 *     )
 * )
 */

class Role extends Model
{
    use HasFactory;

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
     * @param string $category The category of permissions (e.g., 'users').
     * @param array $actions The actions associated with the category (e.g., ['read', 'create']).
     * @return void
     */
    public function setPermission(string $category, array $actions)
    {
        // Get the current permissions or initialize an empty array
        $permissions = $this->permissions ?? [];

        // Set the actions for the specified category
        $permissions[$category] = $actions;

        // Save the updated permissions array
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
        // Check if the category exists and if the action is within that category
        return isset($this->permissions[$category]) && in_array($action, $this->permissions[$category]);
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

