<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SystemParameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * Get parameter value with proper type casting
     */
    public function getValue()
    {
        return match ($this->type) {
            'integer' => (int) $this->value,
            'boolean' => (bool) $this->value,
            'decimal' => (float) $this->value,
            default => $this->value,
        };
    }

    /**
     * Static method untuk mendapatkan parameter value by key
     */
    public static function getByKey($key, $default = null)
    {
        $param = self::where('key', $key)->first();
        return $param ? $param->getValue() : $default;
    }

    /**
     * Static method untuk update parameter value
     */
    public static function setValue($key, $value, $type = 'string', $description = null)
    {
        return self::updateOrCreate(
            ['key' => $key],
            [
                'value' => (string) $value,
                'type' => $type,
                'description' => $description,
            ]
        );
    }
}
