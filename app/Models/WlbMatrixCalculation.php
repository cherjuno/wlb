<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WlbMatrixCalculation extends Model
{
    protected $fillable = [
        'user_id',
        'calculation_date',
        'period_type',
        'period_start',
        'period_end',
        'wlb_score',
        'wlb_level',
        'overtime_hours',
        'late_count',
        'leave_days',
        'attendance_rate',
        'jss_score',
        'stress_level',
        'quadrant',
        'quadrant_name',
        'recommendation',
        'detailed_metrics',
        'calculated_at'
    ];

    protected $casts = [
        'calculation_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'wlb_score' => 'decimal:2',
        'attendance_rate' => 'decimal:2',
        'jss_score' => 'decimal:2',
        'detailed_metrics' => 'array',
        'calculated_at' => 'datetime'
    ];

    /**
     * Get the user that owns the calculation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for specific period type
     */
    public function scopePeriodType($query, $type)
    {
        return $query->where('period_type', $type);
    }

    /**
     * Scope for specific quadrant
     */
    public function scopeQuadrant($query, $quadrant)
    {
        return $query->where('quadrant', $quadrant);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $start, $end)
    {
        return $query->whereBetween('calculation_date', [$start, $end]);
    }

    /**
     * Get quadrant name with icon
     */
    public function getQuadrantWithIconAttribute()
    {
        $icons = [
            1 => '🟢', // Green - Optimal
            2 => '🟡', // Yellow - Warning  
            3 => '🔵', // Blue - Low engagement
            4 => '🔴'  // Red - Critical
        ];

        return $icons[$this->quadrant] . ' ' . $this->quadrant_name;
    }

    /**
     * Get status color based on quadrant
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            1 => 'success',
            2 => 'warning',
            3 => 'secondary', 
            4 => 'danger'
        ];

        return $colors[$this->quadrant] ?? 'secondary';
    }

    /**
     * Get WLB level badge class
     */
    public function getWlbBadgeClassAttribute()
    {
        return $this->wlb_level === 'high' ? 'bg-success' : 'bg-danger';
    }

    /**
     * Get stress level badge class
     */
    public function getStressBadgeClassAttribute()
    {
        return $this->stress_level === 'low' ? 'bg-success' : 'bg-warning';
    }
}