<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="OrderTransaction",
 *     type="object",
 *     title="Order Transaction",
 *     description="Represents a payment transaction related to an order",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="order_id", type="integer", example=101),
 *     @OA\Property(property="transaction_id", type="string", example="trx_ABC123456"),
 *     @OA\Property(property="status", type="string", enum={"pending", "success", "failed", "refunded"}, example="success"),
 *     @OA\Property(property="payment_method", type="string", enum={"cash", "card", "online"}, example="card"),
 *     @OA\Property(property="amount", type="number", format="float", example=99.99),
 *     @OA\Property(property="gateway", type="string", example="Zarinpal"),
 *     @OA\Property(property="meta", type="object"),
 *     @OA\Property(property="payload", type="object"),
 *     @OA\Property(property="paid_at", type="string", format="date-time"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 * )
 */
class OrderTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'transaction_id',
        'status',
        'payment_method',
        'amount',
        'gateway',
        'meta',
        'payload',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'float',
        'meta' => 'array',
        'payload' => 'array',
        'paid_at' => 'datetime',
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
}
