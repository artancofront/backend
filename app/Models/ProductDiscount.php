<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProductDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'discount_amount',
        'discount_percentage',
        'start_date',
        'end_date',
    ];

    /**
     * Get the product that this discount applies to.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if the discount is currently active.
     */
    public function isActive()
    {
        $now = Carbon::now();
        return $this->start_date <= $now && ($this->end_date === null || $this->end_date >= $now);
    }

    /**
     * Scope to get only active discounts.
     */
    public function scopeActive($query)
    {
        $now = Carbon::now();
        return $query->where('start_date', '<=', $now)
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            });
    }
}
