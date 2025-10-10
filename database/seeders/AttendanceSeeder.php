<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Overtime;
use App\Models\Leave;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('is_active', true)->get();
        $startDate = Carbon::now()->subDays(30); // Last 30 days
        $endDate = Carbon::now();

        foreach ($users as $user) {
            $currentDate = $startDate->copy();
            
            while ($currentDate->lte($endDate)) {
                // Skip weekends for regular attendance
                if ($currentDate->isWeekday()) {
                    $this->createAttendance($user, $currentDate->copy());
                }
                
                // Randomly create overtime records
                if (rand(1, 10) <= 3) { // 30% chance of overtime
                    $this->createOvertime($user, $currentDate->copy());
                }
                
                $currentDate->addDay();
            }
            
            // Create some leave records
            $this->createLeaveRecords($user);
        }
    }

    private function createAttendance($user, $date)
    {
        // Random variations in check-in and check-out times
        $baseCheckIn = Carbon::parse('08:00');
        $baseCheckOut = Carbon::parse('17:00');
        
        // Add some randomness
        $checkInVariation = rand(-30, 60); // -30 to +60 minutes
        $checkOutVariation = rand(-60, 120); // -60 to +120 minutes
        
        $checkIn = $baseCheckIn->copy()->addMinutes($checkInVariation);
        $checkOut = $baseCheckOut->copy()->addMinutes($checkOutVariation);
        
        // Ensure checkout is after checkin
        if ($checkOut->lte($checkIn)) {
            $checkOut = $checkIn->copy()->addHours(8);
        }
        
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $date,
            'check_in' => $checkIn->format('H:i'),
            'check_out' => $checkOut->format('H:i'),
            'break_duration' => 1.0, // 1 hour break
            'status' => 'present',
            'notes' => null,
        ]);
        
        $attendance->updateWorkHours();
        $attendance->updateStatus();
    }

    private function createOvertime($user, $date)
    {
        // Only create overtime on some days
        if (rand(1, 10) > 3) {
            return;
        }
        
        $startTime = Carbon::parse('18:00')->addMinutes(rand(0, 120));
        $duration = rand(1, 6); // 1 to 6 hours
        $endTime = $startTime->copy()->addHours($duration);
        
        $overtime = Overtime::create([
            'user_id' => $user->id,
            'date' => $date,
            'start_time' => $startTime->format('H:i'),
            'end_time' => $endTime->format('H:i'),
            'hours' => $duration,
            'reason' => $this->getRandomOvertimeReason(),
            'status' => $this->getRandomStatus(),
        ]);
        
        $overtime->updateType();
        
        // Approve by manager if user has manager
        if ($user->manager_id && $overtime->status === 'approved') {
            $overtime->update([
                'approved_by' => $user->manager_id,
                'approved_at' => $date->copy()->addHours(rand(1, 24)),
                'approval_notes' => 'Approved by manager',
            ]);
        }
    }

    private function createLeaveRecords($user)
    {
        // Create 1-3 leave records per user
        $leaveCount = rand(1, 3);
        
        for ($i = 0; $i < $leaveCount; $i++) {
            $startDate = Carbon::now()->subDays(rand(5, 25));
            $duration = rand(1, 3); // 1-3 days
            $endDate = $startDate->copy()->addDays($duration - 1);
            
            $leave = Leave::create([
                'user_id' => $user->id,
                'type' => $this->getRandomLeaveType(),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'days_requested' => $duration,
                'reason' => $this->getRandomLeaveReason(),
                'status' => $this->getRandomStatus(),
            ]);
            
            // Approve by manager if user has manager
            if ($user->manager_id && $leave->status === 'approved') {
                $leave->update([
                    'approved_by' => $user->manager_id,
                    'approved_at' => $startDate->copy()->subDays(rand(1, 3)),
                    'approval_notes' => 'Approved by manager',
                ]);
            }
        }
    }

    private function getRandomOvertimeReason()
    {
        $reasons = [
            'Menyelesaikan project urgent',
            'Meeting dengan klien internasional',
            'Maintenance sistem',
            'Deadline laporan bulanan',
            'Persiapan presentasi',
            'Bug fixing critical',
            'Data migration',
            'Training tim baru',
        ];
        
        return $reasons[array_rand($reasons)];
    }

    private function getRandomLeaveReason()
    {
        $reasons = [
            'Keperluan keluarga',
            'Liburan bersama keluarga',
            'Kondisi kesehatan',
            'Acara pernikahan keluarga',
            'Urusan pribadi',
            'Istirahat',
            'Medical check-up',
            'Liburan tahunan',
        ];
        
        return $reasons[array_rand($reasons)];
    }

    private function getRandomLeaveType()
    {
        $types = ['annual', 'sick', 'emergency'];
        return $types[array_rand($types)];
    }

    private function getRandomStatus()
    {
        $statuses = ['approved', 'pending', 'rejected'];
        $weights = [70, 20, 10]; // 70% approved, 20% pending, 10% rejected
        
        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($weights as $index => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $statuses[$index];
            }
        }
        
        return 'approved';
    }
}
