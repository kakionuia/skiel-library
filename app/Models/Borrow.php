<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrow extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
        'late_days',
        'late_fee',
        'notes',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'late_days' => 'integer',
        'late_fee' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function isOverdue()
    {
        return $this->status === 'borrowed' && now()->gt($this->due_date);
    }

    public function calculateLateFee()
    {
        if ($this->isOverdue()) {
            $this->late_days = now()->diffInDays($this->due_date);
            $this->late_fee = $this->late_days * 1000; // 1000 per day
            $this->save();
        }
        return $this;
    }
}