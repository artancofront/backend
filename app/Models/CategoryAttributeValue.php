<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryAttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_attribute_id',
        'text_value',
        'boolean_value',
        'numeric_value',
    ];

    /**
     * Get the attribute that owns this value.
     */
    public function categoryAttribute()
    {
        return $this->belongsTo(CategoryAttribute::class);
    }

    /**
     * Get the correct value based on category attribute type.
     */
    public function getValueAttribute()
    {
        switch ($this->categoryAttribute->type) {
            case 'text':
                return $this->text_value;
            case 'boolean':
                return (bool) $this->boolean_value;
            case 'number':
                return $this->numeric_value;
            default:
                return null;
        }
    }


}
