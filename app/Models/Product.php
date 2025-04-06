<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'weight',
        'length',
        'width',
        'height',
        'stock',
        'sku',
        'price',
        'has_variants',
        'category_id', // category with no child
        'is_active',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->where('is_primary', false);
    }

    public function discounts()
    {
        return $this->hasMany(ProductDiscount::class);
    }

    public function statistics()
    {
        return $this->hasOne(ProductStatistic::class);
    }

    public function commentScores()
    {
        return $this->hasMany(ProductCommentScore::class);
    }

    public function conversations()
    {
        return $this->hasMany(ProductConversation::class);
    }
    protected $appends = ['final_price'];

    public function getFinalPriceAttribute()
    {
        $basePrice = $this->price;

        // Use active product discount if exists
        $discount = $this->discounts()->active()->first();

        // If no product-level discount, check category discount
        if (!$discount && $this->category) {
            $discount = $this->category->discounts()->active()->first();
        }

        if ($discount) {
            if ($discount->discount_percentage) {
                return round($basePrice * (1 - $discount->discount_percentage / 100), 2);
            }

            if ($discount->discount_amount) {
                return max(0, round($basePrice - $discount->discount_amount, 2));
            }
        }

        return $basePrice;
    }

}
