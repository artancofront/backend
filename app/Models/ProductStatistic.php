<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'variant_count',
        'sales_count',
        'conversation_count',
        'comment_count',
        'score_count',
        'avg_score',
        'min_price',
        'max_price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
