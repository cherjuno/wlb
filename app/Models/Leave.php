<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'start_date',
        'end_date',
        'days_requested',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'approval_notes',
        'documents',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'documents' => 'array',
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
    public function calculateDays()
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }

        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        
        // Calculate business days (excluding weekends)
        $days = 0;
        while ($start->lte($end)) {
            if ($start->isWeekday()) {
                $days++;
            }
            $start->addDay();
        }
        
        return $days;
    }

    public function updateDaysRequested()
    {
        $this->days_requested = $this->calculateDays();
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

        // Update user's remaining leave if it's annual leave
        if ($this->type === 'annual' && $this->user) {
            $this->user->decrement('remaining_leave', $this->days_requested);
        }
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
            'annual' => 'Cuti Tahunan',
            'sick' => 'Cuti Sakit',
            'maternity' => 'Cuti Melahirkan',
            'emergency' => 'Cuti Darurat',
            'unpaid' => 'Cuti Tanpa Gaji',
            default => ucfirst($this->type)
        };
    }
}
