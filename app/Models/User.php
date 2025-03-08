<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasApiTokens;
    // Mass assignable fields
    protected $fillable = [
        'phone',
        'password',
        'email',
        'name',  // Add any other fields like name if applicable
    ];
    // Hide sensitive data
    protected $hidden = [
        'password',
        'remember_token',
    ];
    // Cast attributes
    protected $casts = [
        'phone_verified_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    // User's role (assuming a 'roles' table and foreign key 'role_id' in the 'users' table)
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Hash password automatically when setting it
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
}
