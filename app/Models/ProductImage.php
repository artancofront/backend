<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image_path',
        'order',
        'is_primary',
    ];

    /**
     * Get the product that owns this image.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope to get only the primary image.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}

