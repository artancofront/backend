<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
/**
 * @OA\Schema(
 *     schema="Carrier",
 *     required={"name", "is_active"},
 *     @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *     @OA\Property(property="name", type="string", example="DHL"),
 *     @OA\Property(property="tracking_url", type="string", nullable=true, example="https://dhl.com/track?number={tracking_number}"),
 *     @OA\Property(property="contact_number", type="string", nullable=true, example="+1-800-123-4567"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", readOnly=true, example="2025-04-21T14:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", readOnly=true, example="2025-04-21T15:00:00Z")
 * )
 */

class Carrier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tracking_url',
        'contact_number',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
}
