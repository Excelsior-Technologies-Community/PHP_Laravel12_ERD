<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// 👇 IMPORT MODEL
use App\Models\Post;

class Comment extends Model
{
    use HasFactory;

    /**
     * Fillable fields
     */
    protected $fillable = [
        'post_id',
        'comment',
    ];

    /**
     * Relationship: Comment belongs to Post
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}