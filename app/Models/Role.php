<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // Define the table name (optional if it follows Laravel's naming convention)
    protected $table = 'roles';

    // Define the mass-assignable attributes (whitelist the attributes that can be mass-assigned)
    protected $fillable = [
        'name',
        'description',
        'permissions',
    ];

    // Specify how to cast attributes (e.g., casting 'permissions' to an array)
    protected $casts = [
        'permissions' => 'array', // Casting permissions to an array
    ];



    /**
     * @var array
     */
    private $permissions;

    public function users()
    {
        return $this->hasMany(User::class);
    }
    /**
     * Set a permission for the role.
     *
     * @param string $permission
     * @param bool $value
     * @return void
     */
    public function setPermission(string $permission, bool $value)
    {
        $permissions = $this->permissions ?? []; // Get the current permissions or an empty array

        // Set or update the permission value
        $permissions[$permission] = $value;

        // Save the updated permissions array
        $this->permissions = $permissions;
        $this->save();
    }

    /**
     * Check if the role has a specific permission.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        return isset($this->permissions[$permission]) && $this->permissions[$permission] === true;
    }

    /**
     * Get all permissions for the role.
     *
     * @return array
     */
    public function getPermissions(): array
    {
        return $this->permissions ?? [];
    }

}
