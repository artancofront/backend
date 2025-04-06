<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'stock',
        'is_active',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributes()
    {
        return $this->hasMany(ProductVariantAttribute::class);
    }

    protected $appends = ['final_price'];

    public function getFinalPriceAttribute()
    {
        $product = $this->product;

        // Check for active discount (product or category)
        $discount = $product->discounts->firstWhere(fn($d) => $d->isActive())
            ?? $product->category->discounts->firstWhere(fn($d) => $d->isActive());

        if (!$discount) {
            return $this->price;
        }

        if ($discount->discount_amount) {
            return max(0, $this->price - $discount->discount_amount);
        }

        if ($discount->discount_percentage) {
            return round($this->price * (1 - $discount->discount_percentage / 100), 2);
        }

        return $this->price;
    }

}
