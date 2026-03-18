<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// 👇 IMPORT MODELS
use App\Models\User;
use App\Models\Comment;

class Post extends Model
{
    use HasFactory;

    /**
     * Fillable fields
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
    ];

    /**
     * Relationship: Post belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Post has many Comments
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}