<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = ['user_id', 'type', 'description', 'metadata'];

    // Cast metadata agar bisa menyimpan array/JSON
    protected $casts = [
        'metadata' => 'array',
    ];

    // Jembatan untuk relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Fungsi LOG inilah yang dicari oleh PetugasController
    public static function log($userId, $type, $description, $metadata = [])
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }
}