<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'cover_image',
        'author_id',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    // Author relationship (assuming a User model)
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Optional: scope to filter published blogs
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
