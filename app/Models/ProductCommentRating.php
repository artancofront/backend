<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCommentRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'customer_id',
        'comment',
        'likes',
        'dislikes',
        'is_approved',
        'rating',
    ];

    /**
     * Get the product that this comment belongs to.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the Customer who made the comment.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get admin replies related to this comment.
     */
    public function adminReplies()
    {
        return $this->hasMany(AdminReply::class);
    }

    /**
     * Accessor to get approval status in human-readable format.
     */
    public function getApprovalStatusAttribute()
    {
        return $this->is_approved ? 'Approved' : 'Pending';
    }
}
