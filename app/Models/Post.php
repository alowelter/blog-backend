<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Comment;

class Post extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'content', 'slug', 'user_id'];
    protected $appends = ['comments'];
    public function getCommentsAttribute()
    {
        return $this->hasMany(Comment::class, 'post_id', 'id')->get();
    }
}
