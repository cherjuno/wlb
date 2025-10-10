<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'level',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Helper methods
    public function isManagerLevel()
    {
        return in_array($this->level, ['manager', 'director']);
    }

    public function getActiveUsersCount()
    {
        return $this->users()->where('is_active', true)->count();
    }
}
