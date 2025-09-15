<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'category_attribute_id',
        'category_attribute_value_id',
    ];

    /**
     * Get the product that owns the attribute.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the category attribute that the product attribute belongs to.
     */
    public function categoryAttribute()
    {
        return $this->belongsTo(CategoryAttribute::class);
    }

    /**
     * Get the predefined category attribute value (if any) for the product attribute.
     */
    public function categoryAttributeValue()
    {
        return $this->belongsTo(CategoryAttributeValue::class);
    }


}
