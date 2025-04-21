<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Schema(
 *     schema="Customer",
 *     required={"phone", "password", "email", "name"},
 *     @OA\Property(property="phone", type="string", description="Customer phone number", example="1234567890"),
 *     @OA\Property(property="email", type="string", format="email", description="Customer email address", example="user@example.com"),
 *     @OA\Property(property="name", type="string", description="Customer's name", example="John Doe"),
 *     @OA\Property(property="password", type="string", description="Customer password", example="secret123"),
 * )
 */
class Customer extends Model
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
     * Set the Customer's password and hash it automatically.
     *
     * @param string $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}

