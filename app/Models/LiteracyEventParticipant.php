<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiteracyEventParticipant extends Model
{
    protected $fillable = [
        'literacy_event_id',
        'user_id',
        'submission_path',
        'submitted_at',
        'is_winner',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'is_winner' => 'boolean',
    ];

    protected $appends = ['submission_url'];

    public function event()
    {
        return $this->belongsTo(LiteracyEvent::class, 'literacy_event_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSubmissionUrlAttribute(): ?string
    {
        return $this->submission_path ? '/storage/' . $this->submission_path : null;
    }
}
