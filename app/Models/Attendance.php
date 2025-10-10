<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'break_duration',
        'work_hours',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime:H:i',
        'check_out' => 'datetime:H:i',
        'break_duration' => 'decimal:2',
        'work_hours' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods
    public function calculateWorkHours()
    {
        if (!$this->check_in || !$this->check_out) {
            return 0;
        }

        $checkIn = Carbon::parse($this->check_in);
        $checkOut = Carbon::parse($this->check_out);
        
        $totalMinutes = $checkOut->diffInMinutes($checkIn);
        $breakMinutes = $this->break_duration * 60;
        
        $workMinutes = $totalMinutes - $breakMinutes;
        
        return round($workMinutes / 60, 2);
    }

    public function updateWorkHours()
    {
        $this->work_hours = $this->calculateWorkHours();
        $this->save();
    }

    public function isLate()
    {
        if (!$this->check_in) {
            return false;
        }

        $standardStartTime = Carbon::parse('08:00');
        $checkInTime = Carbon::parse($this->check_in);
        
        return $checkInTime->gt($standardStartTime);
    }

    public function isEarlyLeave()
    {
        if (!$this->check_out) {
            return false;
        }

        $standardEndTime = Carbon::parse('17:00');
        $checkOutTime = Carbon::parse($this->check_out);
        
        return $checkOutTime->lt($standardEndTime);
    }

    public function updateStatus()
    {
        if (!$this->check_in) {
            $this->status = 'absent';
        } elseif ($this->isLate() && $this->isEarlyLeave()) {
            $this->status = 'late';
        } elseif ($this->isLate()) {
            $this->status = 'late';
        } elseif ($this->isEarlyLeave()) {
            $this->status = 'early_leave';
        } else {
            $this->status = 'present';
        }
        
        $this->save();
    }
}
