<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\JobStressScale;
use App\Models\Attendance;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AnalyticsDummySeeder extends Seeder
{
    public function run()
    {
        echo "Creating dummy data for analytics...\n";
        
        // Get first 8 users for testing
        $users = User::take(8)->get();
        
        foreach ($users as $index => $user) {
            // Create Job Stress Scale data with correct column names
            $q1 = rand(1, 5);
            $q2 = rand(1, 5);
            $q3 = rand(1, 5);
            $q4 = rand(1, 5);
            $q5 = rand(1, 5);
            $q6 = rand(1, 5);
            $q7 = rand(1, 5);
            $q8 = rand(1, 5);
            $q9 = rand(1, 5);
            $q10 = rand(1, 5);
            
            $totalScore = $q1 + $q2 + $q3 + $q4 + $q5 + $q6 + $q7 + $q8 + $q9 + $q10;
            
            // Determine stress level based on total score
            if ($totalScore <= 20) {
                $stressLevel = 'low';
            } elseif ($totalScore <= 35) {
                $stressLevel = 'moderate';
            } else {
                $stressLevel = 'high';
            }
            
            JobStressScale::updateOrCreate([
                'user_id' => $user->id,
                'month' => now()->month,
                'year' => now()->year,
            ], [
                'question_1' => $q1,
                'question_2' => $q2,
                'question_3' => $q3,
                'question_4' => $q4,
                'question_5' => $q5,
                'question_6' => $q6,
                'question_7' => $q7,
                'question_8' => $q8,
                'question_9' => $q9,
                'question_10' => $q10,
                'total_score' => $totalScore,
                'stress_level' => $stressLevel,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Create some attendance data for WLB calculation
            for ($day = 1; $day <= 20; $day++) {
                $date = Carbon::create(now()->year, now()->month, $day);
                if ($date->isWeekday() && $date <= now()) {
                    $status = 'present';
                    
                    // Add some late attendance for variety
                    if ($index % 2 == 0 && rand(1, 10) <= 3) {
                        $status = 'late';
                    }
                    
                    Attendance::updateOrCreate([
                        'user_id' => $user->id,
                        'date' => $date->format('Y-m-d'),
                    ], [
                        'status' => $status,
                        'check_in' => $date->setTime(8, rand(0, 30)),
                        'check_out' => $date->setTime(17, rand(0, 30)),
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);
                }
            }
        }
        
        echo "Dummy data created successfully!\n";
        echo "Users with data: " . $users->count() . "\n";
        echo "Job Stress entries: " . JobStressScale::where('month', now()->month)->where('year', now()->year)->count() . "\n";
        echo "Attendance entries: " . Attendance::whereMonth('date', now()->month)->count() . "\n";
    }
}