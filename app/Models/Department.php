<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'manager_email',
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

    public function manager()
    {
        return $this->users()->whereHas('roles', function ($query) {
            $query->where('name', 'manager');
        })->first();
    }

    // Helper methods
    public function getActiveUsersCount()
    {
        return $this->users()->where('is_active', true)->count();
    }

    public function getAverageOvertimeHoursThisMonth()
    {
        $users = $this->users()->where('is_active', true)->get();
        $totalHours = 0;
        $userCount = $users->count();

        if ($userCount === 0) {
            return 0;
        }

        foreach ($users as $user) {
            $totalHours += $user->getTotalOvertimeHoursThisMonth();
        }

        return round($totalHours / $userCount, 2);
    }

    public function getAverageWorkHoursThisWeek()
    {
        $users = $this->users()->where('is_active', true)->get();
        $totalHours = 0;
        $userCount = $users->count();

        if ($userCount === 0) {
            return 0;
        }

        foreach ($users as $user) {
            $totalHours += $user->getWorkHoursThisWeek();
        }

        return round($totalHours / $userCount, 2);
    }
}
