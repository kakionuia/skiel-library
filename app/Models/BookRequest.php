<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'petugas_id',
        'user_id',
        'title',
        'author',
        'isbn',
        'publisher',
        'year',
        'category',
        'description',
        'quantity',
        'status',
        'admin_note',
        'reviewed_by',
    ];

    protected $casts = [
        'year' => 'integer',
        'quantity' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
