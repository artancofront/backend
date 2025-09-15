<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryAttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_attribute_id',
        'value',
    ];

    /**
     * Get the attribute that owns this value.
     */
    public function categoryAttribute()
    {
        return $this->belongsTo(CategoryAttribute::class);
    }



}
