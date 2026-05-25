<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LiteracyEvent extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'terms',
        'category',
        'starts_at',
        'ends_at',
        'image_path',
        'status',
        'winner_user_id',
        'ended_at',
        'created_by',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'ended_at' => 'datetime',
    ];

    protected $appends = [
        'image_url',
        'category_label',
        'status_label',
        'is_running',
    ];

    public function participants()
    {
        return $this->hasMany(LiteracyEventParticipant::class);
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? '/storage/' . $this->image_path : null;
    }

    public function getCategoryLabelAttribute(): string
    {
        return $this->category === 'karya' ? 'Karya' : 'Poin';
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->status === 'ended' || $this->winner_user_id) {
            return 'Tidak Aktif';
        }

        if (now()->toDateString() > $this->ends_at?->toDateString()) {
            return 'Selesai';
        }

        if (now()->toDateString() < $this->starts_at?->toDateString()) {
            return 'Belum Mulai';
        }

        return 'Aktif';
    }

    public function getIsRunningAttribute(): bool
    {
        $today = now()->toDateString();

        return $this->status === 'active'
            && !$this->winner_user_id
            && $today >= $this->starts_at?->toDateString()
            && $today <= $this->ends_at?->toDateString();
    }

    public static function generateSlug(string $title): string
    {
        $slug = Str::slug($title);
        $count = static::where('slug', 'LIKE', $slug . '%')->count();

        return $count > 0 ? $slug . '-' . ($count + 1) : $slug;
    }
}
