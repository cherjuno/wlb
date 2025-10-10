<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WlbSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
        'value',
        'type',
        'category',
    ];

    // Helper methods
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        return static::castValue($setting->value, $setting->type);
    }

    public static function set($key, $value)
    {
        $setting = static::where('key', $key)->first();
        
        if ($setting) {
            $setting->update(['value' => $value]);
        } else {
            static::create([
                'key' => $key,
                'name' => $key,
                'value' => $value,
                'type' => static::guessType($value),
            ]);
        }
    }

    private static function castValue($value, $type)
    {
        return match($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($value) ? (float) $value : $value,
            'json' => json_decode($value, true),
            default => $value
        };
    }

    private static function guessType($value)
    {
        if (is_bool($value)) {
            return 'boolean';
        }
        
        if (is_numeric($value)) {
            return 'number';
        }
        
        if (is_array($value) || is_object($value)) {
            return 'json';
        }
        
        return 'string';
    }

    public function getValueAttribute($value)
    {
        return static::castValue($value, $this->type);
    }

    public function setValueAttribute($value)
    {
        if ($this->type === 'json') {
            $this->attributes['value'] = json_encode($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }
}
