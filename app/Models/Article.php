<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'category',
        'featured_image',
        'author_id',
        'approved_by',
        'status',
        'rejection_reason',
        'published_at',
        'approved_at'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'approved_at' => 'datetime'
    ];

    /**
     * Get the author of the article
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the approver of the article
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope: Get published articles
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'approved')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc');
    }

    /**
     * Scope: Get pending articles
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Generate slug from title
     */
    public static function generateSlug($title)
    {
        $slug = Str::slug($title);
        $count = static::where('slug', 'LIKE', $slug . '%')->count();
        
        return $count > 0 ? $slug . '-' . ($count + 1) : $slug;
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            'draft' => 'Draft',
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak'
        ];
        
        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'draft' => 'gray',
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red'
        ];
        
        return $colors[$this->status] ?? 'gray';
    }
}

