<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Overtime extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'hours',
        'type',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'approval_notes',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'hours' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Helper methods
    public function calculateHours()
    {
        if (!$this->start_time || !$this->end_time) {
            return 0;
        }

        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);
        
        // If end time is before start time, assume it's next day
        if ($end->lt($start)) {
            $end->addDay();
        }
        
        return round($end->diffInMinutes($start) / 60, 2);
    }

    public function updateHours()
    {
        $this->hours = $this->calculateHours();
        $this->save();
    }

    public function determineType()
    {
        $date = Carbon::parse($this->date);
        
        if ($date->isWeekend()) {
            return 'weekend';
        }
        
        // Check if it's a holiday (you can expand this logic)
        // For now, just check weekends
        return 'weekday';
    }

    public function updateType()
    {
        $this->type = $this->determineType();
        $this->save();
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function canBeApproved()
    {
        return $this->isPending();
    }

    public function approve($approverId, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approverId,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);
    }

    public function reject($approverId, $notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $approverId,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    public function getTypeDisplayAttribute()
    {
        return match($this->type) {
            'weekday' => 'Hari Kerja',
            'weekend' => 'Akhir Pekan',
            'holiday' => 'Hari Libur',
            default => ucfirst($this->type)
        };
    }

    public function getMultiplierRate()
    {
        return match($this->type) {
            'weekday' => 1.5,
            'weekend' => 2.0,
            'holiday' => 3.0,
            default => 1.0
        };
    }
}
