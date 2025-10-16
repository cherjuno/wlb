<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobStressScale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'month',
        'year',
        'question_1',
        'question_2',
        'question_3',
        'question_4',
        'question_5',
        'question_6',
        'question_7',
        'question_8',
        'question_9',
        'question_10',
        'total_score',
        'stress_level',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'question_1' => 'integer',
        'question_2' => 'integer',
        'question_3' => 'integer',
        'question_4' => 'integer',
        'question_5' => 'integer',
        'question_6' => 'integer',
        'question_7' => 'integer',
        'question_8' => 'integer',
        'question_9' => 'integer',
        'question_10' => 'integer',
        'total_score' => 'integer',
    ];

    /**
     * Relationship dengan User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate total score from individual questions
     */
    public function calculateTotalScore(): int
    {
        return $this->question_1 + $this->question_2 + $this->question_3 + 
               $this->question_4 + $this->question_5 + $this->question_6 + 
               $this->question_7 + $this->question_8 + $this->question_9 + 
               $this->question_10;
    }

    /**
     * Determine stress level based on total score
     */
    public function determineStressLevel(): string
    {
        $score = $this->total_score;
        
        if ($score >= 10 && $score <= 20) {
            return 'low';
        } elseif ($score >= 21 && $score <= 35) {
            return 'moderate';
        } elseif ($score >= 36 && $score <= 50) {
            return 'high';
        }
        
        return 'moderate'; // default fallback
    }

    /**
     * Get stress level in Indonesian
     */
    public function getStressLevelIndonesian(): string
    {
        return match($this->stress_level) {
            'low' => 'Stres Rendah',
            'moderate' => 'Stres Sedang',
            'high' => 'Stres Tinggi',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Get month name in Indonesian
     */
    public function getMonthNameAttribute(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $months[$this->month] ?? 'Unknown';
    }

    /**
     * Get questions with translations
     */
    public static function getQuestions(): array
    {
        return [
            1 => [
                'en' => 'I have too much work to do in too little time.',
                'id' => 'Saya memiliki terlalu banyak pekerjaan untuk diselesaikan dalam waktu yang singkat.'
            ],
            2 => [
                'en' => 'I often have to work very fast.',
                'id' => 'Saya sering harus bekerja dengan sangat cepat.'
            ],
            3 => [
                'en' => 'I feel overloaded with the amount of work I must do.',
                'id' => 'Saya merasa terbebani dengan banyaknya pekerjaan yang harus saya lakukan.'
            ],
            4 => [
                'en' => 'The deadlines set for my job are usually too tight.',
                'id' => 'Batas waktu yang ditetapkan dalam pekerjaan saya biasanya terlalu ketat.'
            ],
            5 => [
                'en' => 'My job requires working under constant time pressure.',
                'id' => 'Pekerjaan saya menuntut saya untuk bekerja dalam tekanan waktu yang terus-menerus.'
            ],
            6 => [
                'en' => 'I rarely have enough time to finish everything I need to do at work.',
                'id' => 'Saya jarang memiliki cukup waktu untuk menyelesaikan semua yang perlu saya lakukan di tempat kerja.'
            ],
            7 => [
                'en' => 'The pace of work in my job is very high.',
                'id' => 'Kecepatan kerja dalam pekerjaan saya sangat tinggi.'
            ],
            8 => [
                'en' => 'I have to do more work than I can handle comfortably.',
                'id' => 'Saya harus melakukan lebih banyak pekerjaan daripada yang dapat saya tangani dengan nyaman.'
            ],
            9 => [
                'en' => 'I often have to work extra hours to meet the demands of my job.',
                'id' => 'Saya sering harus bekerja lembur untuk memenuhi tuntutan pekerjaan saya.'
            ],
            10 => [
                'en' => 'I feel that the demands of my job interfere with the quality of my work.',
                'id' => 'Saya merasa tuntutan pekerjaan saya mengganggu kualitas pekerjaan saya.'
            ]
        ];
    }

    /**
     * Scope untuk filter berdasarkan bulan dan tahun
     */
    public function scopeForMonth($query, $month, $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    /**
     * Scope untuk filter berdasarkan tahun
     */
    public function scopeForYear($query, $year)
    {
        return $query->where('year', $year);
    }
}