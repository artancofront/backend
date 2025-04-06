<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'category_attribute_id',
        'category_attribute_value_id',
    ];

    /**
     * Get the product variant that owns this attribute.
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Get the category attribute associated with this product variant attribute.
     */
    public function categoryAttribute()
    {
        return $this->belongsTo(CategoryAttribute::class);
    }

    /**
     * Get the category attribute value associated with this product variant attribute.
     */
    public function categoryAttributeValue()
    {
        return $this->belongsTo(CategoryAttributeValue::class);
    }

    /**
     * Get the correct value of the attribute.
     */
    public function getValueAttribute()
    {
        return $this->categoryAttributeValue->value;
    }
}
