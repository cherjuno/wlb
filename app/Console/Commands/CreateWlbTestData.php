<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\WlbMatrixCalculation;
use Illuminate\Console\Command;

class CreateWlbTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:wlb-test-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create WLB test data for analytics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating WLB test data...');
        
        $users = User::take(8)->get();
        
        foreach ($users as $user) {
            $wlbScore = rand(40, 95);
            $jssScore = rand(150, 450) / 100; // 1.5 to 4.5
            
            // Determine levels
            $wlbLevel = $wlbScore >= 70 ? 'high' : 'low';
            $stressLevel = $jssScore >= 3.0 ? 'high' : 'low';
            
            // Determine quadrant
            if ($wlbLevel === 'high' && $stressLevel === 'low') {
                $quadrant = 1;
                $quadrantName = 'Optimal Performance';
            } elseif ($wlbLevel === 'high' && $stressLevel === 'high') {
                $quadrant = 2;
                $quadrantName = 'High Stress Alert';
            } elseif ($wlbLevel === 'low' && $stressLevel === 'low') {
                $quadrant = 3;
                $quadrantName = 'Low Engagement';
            } else {
                $quadrant = 4;
                $quadrantName = 'Critical Risk';
            }
            
            WlbMatrixCalculation::updateOrCreate([
                'user_id' => $user->id,
                'calculation_date' => now()->format('Y-m-d'),
                'period_type' => 'monthly',
            ], [
                'period_start' => now()->startOfMonth()->format('Y-m-d'),
                'period_end' => now()->endOfMonth()->format('Y-m-d'),
                'wlb_score' => $wlbScore,
                'wlb_level' => $wlbLevel,
                'jss_score' => $jssScore,
                'stress_level' => $stressLevel,
                'quadrant' => $quadrant,
                'quadrant_name' => $quadrantName,
                'overtime_hours' => rand(0, 40),
                'late_count' => rand(0, 8),
                'leave_days' => rand(0, 5),
                'attendance_rate' => rand(85, 100),
                'calculated_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        $this->info("WLB matrix data created for " . $users->count() . " users");
        return 0;
    }
}
