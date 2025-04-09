<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Schema(
 *     schema="User",
 *     required={"phone", "password", "email", "name"},
 *     @OA\Property(property="phone", type="string", description="User phone number", example="1234567890"),
 *     @OA\Property(property="email", type="string", format="email", description="User email address", example="user@example.com"),
 *     @OA\Property(property="name", type="string", description="User's name", example="John Doe"),
 *     @OA\Property(property="password", type="string", description="User password", example="secret123"),
 *     @OA\Property(property="role", ref="#/components/schemas/Role"),
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
        'phone_verified_at',
        'role_id',
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

