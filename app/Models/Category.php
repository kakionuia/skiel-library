<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->slug = Str::slug($model->name);
        });
    }

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}

