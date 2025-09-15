<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'customer_id',
        'parent_id',
        'message',
        'likes',
        'dislikes',
        'is_approved',
    ];

    /**
     * Get the product this conversation belongs to.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the Customer who posted the message.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the parent conversation (if it's a reply).
     */
    public function parent()
    {
        return $this->belongsTo(ProductConversation::class, 'parent_id');
    }

    /**
     * Get replies to this conversation.
     */
    public function replies()
    {
        return $this->hasMany(ProductConversation::class, 'parent_id');
    }

    /**
     * Get admin replies related to this comment.
     */
    public function adminReplies()
    {
        return $this->hasMany(AdminReply::class);
    }

    /**
     * Scope for only approved conversations.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
}
