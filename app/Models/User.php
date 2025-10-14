<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'employee_id',
        'phone',
        'hire_date',
        'birth_date',
        'gender',
        'address',
        'department_id',
        'position_id',
        'manager_id',
        'annual_leave_quota',
        'remaining_leave',
        'salary',
        'is_active',
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
            'hire_date' => 'date',
            'birth_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function overtimes()
    {
        return $this->hasMany(Overtime::class);
    }

    public function approvedLeaves()
    {
        return $this->hasMany(Leave::class, 'approved_by');
    }

    public function approvedOvertimes()
    {
        return $this->hasMany(Overtime::class, 'approved_by');
    }

    // Helper methods
    public function isEmployee()
    {
        return $this->hasRole('employee');
    }

    public function isManager()
    {
        return $this->hasRole('manager');
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function getFullNameAttribute()
    {
        return $this->name;
    }

    public function getTotalOvertimeHoursThisWeek()
    {
        return $this->overtimes()
            ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('status', 'approved')
            ->sum('hours');
    }

    public function getTotalOvertimeHoursThisMonth()
    {
        return $this->overtimes()
            ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])
            ->where('status', 'approved')
            ->sum('hours');
    }

    public function getWorkHoursThisWeek()
    {
        return $this->attendances()
            ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('work_hours');
    }
}
