<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
    ];

    /**
     * Get the category that owns the attribute.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the valid values for this attribute (if applicable).
     */
    public function values()
    {
        return $this->hasMany(CategoryAttributeValue::class);
    }

}
