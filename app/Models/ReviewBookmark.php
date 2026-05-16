<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewBookmark extends Model
{
    protected $table = 'review_bookmarks';

    protected $fillable = [
        'user_id',
        'review_id',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function review()
    {
        return $this->belongsTo(Review::class);
    }
}
