<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// 👇 IMPORTANT: Import Post Model
use App\Models\Post;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Fillable fields
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Hidden fields
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relationship: User has many Posts
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}