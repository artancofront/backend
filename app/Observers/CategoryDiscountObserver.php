<?php
namespace App\Observers;

use App\Models\CategoryDiscount;

class CategoryDiscountObserver
{
    public function creating(CategoryDiscount $discount)
    {
        if (!$discount->category->isLeaf()) {
            throw new \Exception('Cannot apply discount to a non-leaf category.');
        }
    }
}
