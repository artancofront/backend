<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * @OA\Schema(
 *     schema="Order",
 *     type="object",
 *     title="Order",
 *     description="Order model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="order_number", type="string", example="ORD-20250417-001"),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="payment_status", type="string", example="unpaid"),
 *     @OA\Property(property="payment_method", type="string", example="card"),
 *     @OA\Property(property="subtotal", type="number", format="float", example=150.00),
 *     @OA\Property(property="discount", type="number", format="float", example=10.00),
 *     @OA\Property(property="tax", type="number", format="float", example=12.00),
 *     @OA\Property(property="shipping_cost", type="number", format="float", example=5.00),
 *     @OA\Property(property="total", type="number", format="float", example=157.00),
 *     @OA\Property(property="notes", type="string", example="Leave at the front door"),
 *     @OA\Property(property="placed_at", type="string", format="date-time", example="2025-04-17T14:00:00Z"),
 *     @OA\Property(property="expires_at", type="string", format="date-time", example="2025-04-17T14:15:00Z"),
 *     @OA\Property(property="is_expired", type="boolean", example=false),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 * )
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'shipping_address_id',
        'order_number',
        'status',
        'payment_status',
        'payment_method',
        'subtotal',
        'discount',
        'tax',
        'shipping_cost',
        'total',
        'notes',
        'placed_at',
        'expires_at',
    ];

    protected $casts = [
        'subtotal' => 'float',
        'discount' => 'float',
        'tax' => 'float',
        'shipping_cost' => 'float',
        'total' => 'float',
        'placed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected $appends = ['is_expired'];

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsExpiredAttribute(): bool
    {
        return $this->payment_status == 'unpaid' &&
            $this->payment_method == 'online' &&
            now()->greaterThan($this->expires_at);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(CustomerAddress::class, 'shipping_address_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transactions()
    {
        return $this->hasMany(OrderTransaction::class);
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }
}
