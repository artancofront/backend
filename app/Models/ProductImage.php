<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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


    /**
     * Automatically delete the image file when the model is deleted.
     */
    protected static function booted()
    {
        static::deleting(function (ProductImage $image) {
            $cleanPath = Str::replaceFirst('storage/', '', $image->image_path);
            if (Storage::disk('public')->exists($cleanPath)) {
                Storage::disk('public')->delete($cleanPath);
            }
        });
    }
}

