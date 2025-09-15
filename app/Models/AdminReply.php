<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\ValidationException;

class AdminReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'text',
        'product_comment_rating_id',
        'product_conversation_id',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function productCommentRating()
    {
        return $this->belongsTo(ProductCommentRating::class);
    }

    public function productConversation()
    {
        return $this->belongsTo(ProductConversation::class);
    }

    // Override save to enforce constraint
    public function save(array $options = [])
    {
        if (
            is_null($this->product_comment_rating_id) === is_null($this->product_conversation_id)
        ) {
            // Either both are null or both are set
            throw ValidationException::withMessages([
                'reference' => 'Only one of product_comment_rating_id or product_conversation_id must be set.',
            ]);
        }

        return parent::save($options);
    }
}
