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

    public function discounts()
    {
        return $this->hasMany(CategoryDiscount::class);
    }

    public function isLeaf()
    {
        return $this->children()->count() === 0;
    }

}
