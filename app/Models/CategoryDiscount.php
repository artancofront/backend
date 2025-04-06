<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class CategoryDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'discount_amount',
        'discount_percentage',
        'start_date',
        'end_date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function isActive()
    {
        $now = Carbon::now();
        return $this->start_date <= $now && ($this->end_date === null || $this->end_date >= $now);
    }

    public function scopeActive($query)
    {
        $now = Carbon::now();
        return $query->where('start_date', '<=', $now)
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            });
    }
}
