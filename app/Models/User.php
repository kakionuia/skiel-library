<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'nisn',
        'kelas',
        'jurusan',
        'borrow_restricted',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'borrow_restricted' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPetugas(): bool
    {
        return $this->role === 'petugas';
    }

    public function isUser(): bool
    {
        return $this->role === 'anggota';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    // ========== RELATIONSHIPS ==========
    
    public function borrows()
    {
        return $this->hasMany(\App\Models\Borrow::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Alias untuk loans (biar konsisten)
    public function loans()
    {
        return $this->transactions();
    }

    // Peminjaman aktif
    public function activeLoans()
    {
        return $this->transactions()->whereIn('status', ['approved', 'borrowed']);
    }

    // Peminjaman menunggu
    public function pendingLoans()
    {
        return $this->transactions()->where('status', 'pending');
    }

    // Riwayat peminjaman
    public function loanHistory()
    {
        return $this->transactions()->where('status', 'returned');
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class);
    }

    public function complaints()
    {
        return $this->hasMany(\App\Models\Complaint::class);
    }

    public function suggestions()
    {
        return $this->hasMany(\App\Models\Suggestion::class);
    }

    public function bookRequests()
    {
        return $this->hasMany(\App\Models\BookRequest::class, 'petugas_id');
    }

    public function activities()
    {
        return $this->hasMany(\App\Models\Activity::class);
    }

    public function lateBorrows()
    {
        return $this->hasMany(\App\Models\Borrow::class)
            ->whereIn('status', ['approved', 'borrowed'])
            ->where('due_date', '<', now());
    }

    public function totalLateFee()
    {
        return $this->borrows()
            ->where('status', '!=', 'returned')
            ->where('due_date', '<', now())
            ->sum('late_fee');
    }

    public function recentBorrows($limit = 3)
    {
        return $this->borrows()
            ->with('book')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}