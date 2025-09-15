<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'title',
        'first_name',
        'last_name',
        'phone',
        'email',
        'country',
        'province',
        'city',
        'postal_code',
        'address_line_1',
        'address_line_2',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
