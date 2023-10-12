<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'thumbnail',
        'completed',
        'visible',
        'user_id'
    ];

    public function images() {
        return $this->hasMany(Image::class);
    }

    public function categories() {
        return $this->belongsToMany(Category::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
