<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
    'book_id',
    'title', 
    'author', 
    'publisher', 
    'year', 
    'isbn', 
    'category', 
    'quantity', 
    'available_quantity', 
    'description', 
    'cover_image',
    'status',
    'created_by',
    'approved_by',
    'approved_at',
];

    protected $casts = [
        'year' => 'integer',
        'quantity' => 'integer',
        'available_quantity' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            // Generate unique book_id if not provided
            if (!$model->book_id) {
                $model->book_id = 'BK-' . date('YmdHis') . '-' . Str::random(4);
            }
        });
    }

    public function borrows()
    {
        return $this->hasMany(Borrow::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function averageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }
}