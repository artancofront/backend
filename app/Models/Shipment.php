<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
/**
 * @OA\Schema(
 *     schema="Shipment",
 *     required={"order_id", "carrier_id", "shipping_address_id", "status"},
 *     @OA\Property(property="id", type="integer", readOnly=true, example=101),
 *     @OA\Property(property="order_id", type="integer", example=25),
 *     @OA\Property(property="carrier_id", type="integer", example=3),
 *     @OA\Property(property="shipping_address_id", type="integer", example=88),
 *     @OA\Property(property="tracking_number", type="string", nullable=true, example="1Z999AA10123456784"),
 *     @OA\Property(property="status", type="string", example="shipped"),
 *     @OA\Property(property="cost", type="number", format="float", example=12.99),
 *     @OA\Property(property="shipped_at", type="string", format="date-time", nullable=true, example="2025-04-21T10:30:00Z"),
 *     @OA\Property(property="delivered_at", type="string", format="date-time", nullable=true, example="2025-04-23T16:00:00Z"),
 *     @OA\Property(property="notes", type="string", nullable=true, example="Left at the front desk"),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly=true, example="2025-04-21T09:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly=true, example="2025-04-21T09:30:00Z")
 * )
 */

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'carrier_id',
        'shipping_address_id',
        'tracking_number',
        'status',
        'cost',
        'shipped_at',
        'delivered_at',
        'notes',
    ];

    protected $casts = [
        'cost' => 'float',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }
}
