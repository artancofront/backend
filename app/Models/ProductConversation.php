<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
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
     * Get the user who posted the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
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
     * Scope for only approved conversations.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
}
