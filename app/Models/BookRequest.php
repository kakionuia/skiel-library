<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'petugas_id',
        'title',
        'author',
        'publisher',
        'year',
        'isbn',
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

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}