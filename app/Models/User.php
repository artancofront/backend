<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Schema(
 *     schema="User",
 *     required={"phone", "password", "email", "name"},
 *     @OA\Property(property="id", type="integer", description="User ID"),
 *     @OA\Property(property="phone", type="string", description="User phone number"),
 *     @OA\Property(property="email", type="string", format="email", description="User email address"),
 *     @OA\Property(property="name", type="string", description="User's name"),
 *     @OA\Property(property="password", type="string", description="User password"),
 *     @OA\Property(property="phone_verified_at", type="string", format="date-time", description="Phone verification timestamp"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", description="Email verification timestamp"),
 *     @OA\Property(property="role", ref="#/components/schemas/Role", description="User's role"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="User creation timestamp"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="User update timestamp")
 * )
 */
class User extends Model
{
    use HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'phone',
        'password',
        'email',
        'name',  // Add any other fields like name if applicable
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'phone_verified_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    /**
     * User's role (assuming a 'roles' table and foreign key 'role_id' in the 'users' table)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Set the user's password and hash it automatically.
     *
     * @param string $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
}

