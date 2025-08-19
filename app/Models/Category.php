<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Category extends Model
{
    use HasFactory, NodeTrait;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
    ];
    protected $appends = ['all_products_count'];

    public function getAllProductsCountAttribute()
    {
        $categoryIds = $this->descendants()->pluck('id')->push($this->id);
        return Product::whereIn('category_id', $categoryIds)->count();
    }

    public function discounts()
    {
        return $this->hasMany(CategoryDiscount::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function attributes()
    {
        return $this->hasMany(CategoryAttribute::class);
    }

}
