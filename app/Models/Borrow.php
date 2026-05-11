<?php
// app/Models/Borrow.php

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
        'quantity',
        'approved_by',
        'approved_at',
        'rejected_at',
        'rejection_reason',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'late_days' => 'integer',
        'late_fee' => 'decimal:2',
        'quantity' => 'integer',
    ];

    protected $appends = ['status_color', 'status_text', 'can_borrow'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isOverdue()
    {
        return in_array($this->status, ['approved', 'borrowed']) && now()->gt($this->due_date);
    }

    public function calculateLateFee()
    {
        if ($this->isOverdue()) {
            $this->late_days = now()->diffInDays($this->due_date);
            $this->late_fee = $this->late_days * 1000; // Rp1000 per hari
            $this->save();
        }
        return $this;
    }

    // Accessors untuk status
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'approved' => 'blue',
            'borrowed' => 'orange',
            'returned' => 'green',
            'overdue' => 'red',
            'lost' => 'gray',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'borrowed' => 'Sedang Dipinjam',
            'returned' => 'Dikembalikan',
            'overdue' => 'Terlambat',
            'lost' => 'Hilang',
            'rejected' => 'Ditolak',
            default => 'Unknown'
        };
    }

    public function getCanBorrowAttribute()
    {
        if ($this->status !== 'approved') return false;
        if ($this->return_date) return false;
        return true;
    }
}