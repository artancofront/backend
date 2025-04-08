<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Product extends Model
{
    use HasFactory, NodeTrait;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'weight',
        'length',
        'width',
        'height',
        'stock',
        'sku',
        'price',
        'has_variants',
        'category_id',
        'is_active',
        'warranties',
        'policies',
        'specifications',
        'reviews',
        'parent_id', // for nested set
    ];

    protected $casts = [
        'warranties'=> 'array',
        'specifications'=> 'array',
        'reviews'=> 'array',
        'policies' => 'array',
        'has_variants' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $appends = ['final_price'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->children(); // provided by NodeTrait
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
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

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFinalPriceAttribute(): float
    {
        $basePrice = $this->price;

        $discount = $this->discounts()->active()->first();

        // If no product discount, check category discount
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

    public function getResolvedPoliciesAttribute()
    {
        $allPolicies = Policy::all()->keyBy('id');
        return collect($this->policies)->map(fn ($id) => $allPolicies[$id] ?? null)->filter()->values();
    }

    public function getResolvedWarrantiesAttribute()
    {
        $allWarranties = Warranty::all()->keyBy('id');
        return collect($this->warranties)->map(fn ($id) => $allWarranties[$id] ?? null)->filter()->values();
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isVariant(): bool
    {
        return !is_null($this->parent_id);
    }

    public function isParent(): bool
    {
        return is_null($this->parent_id);
    }
}
