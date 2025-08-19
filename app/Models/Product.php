<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;
/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     title="Product",
 *     description="Product model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Red T-Shirt"),
 *     @OA\Property(property="description", type="string", example="Comfortable cotton t-shirt"),
 *     @OA\Property(property="price", type="number", format="float", example=19.99),
 *     @OA\Property(property="stock", type="integer", example=10),
 *     @OA\Property(property="category_id", type="integer", example=3),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-04-15T12:34:56Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-04-15T12:34:56Z"),
 * )
 */

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
        'expert_review',
        'parent_id', // for nested set
    ];

    protected $casts = [
        'warranties'=> 'array',
        'specifications'=> 'array',
        'expert_review'=> 'array',
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
    public function parentProduct()
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }
    public function getParentAttribute()
    {
        return $this->parentProduct;
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

    public function commentRatings()
    {
        return $this->hasMany(ProductCommentRating::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
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
        $basePrice = (float) ($this->price ?? 0);

        $discount = $this->discounts()->active()->first()
            ?? $this->category?->discounts()->active()->first();

        if ($discount) {
            $percentage = (float) ($discount->discount_percentage ?? 0);
            $amount = (float) ($discount->discount_amount ?? 0);

            if ($percentage > 0) {
                return round($basePrice * (1 - $percentage / 100), 2);
            }

            if ($amount > 0) {
                return max(0, round($basePrice - $amount, 2));
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

    public function getResolvedProductAttribute(): Product
    {
        return $this->isVariant() ? $this->parent()->with(['attributes'])->first() ?? $this : $this;
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
