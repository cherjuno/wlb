<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Get departments and positions
        $hrDept = Department::where('code', 'HR')->first();
        $itDept = Department::where('code', 'IT')->first();
        $finDept = Department::where('code', 'FIN')->first();
        $mktDept = Department::where('code', 'MKT')->first();
        $opsDept = Department::where('code', 'OPS')->first();

        $managerPos = Position::where('code', 'MGR')->first();
        $seniorStaffPos = Position::where('code', 'SS')->first();
        $juniorStaffPos = Position::where('code', 'JS')->first();
        $specialistPos = Position::where('code', 'SPE')->first();
        $supervisorPos = Position::where('code', 'SUP')->first();

        // Create Admin
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@wlbapp.com',
            'password' => Hash::make('password'),
            'employee_id' => 'EMP001',
            'phone' => '08123456789',
            'hire_date' => '2024-01-01',
            'birth_date' => '1990-05-15',
            'gender' => 'male',
            'address' => 'Jakarta, Indonesia',
            'department_id' => $hrDept->id,
            'position_id' => $managerPos->id,
            'annual_leave_quota' => 12,
            'remaining_leave' => 12,
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Create Managers
        $managers = [
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@wlbapp.com',
                'employee_id' => 'EMP002',
                'department_id' => $itDept->id,
                'phone' => '08123456790',
                'birth_date' => '1985-03-20',
                'gender' => 'female',
            ],
            [
                'name' => 'Michael Chen',
                'email' => 'michael.chen@wlbapp.com',
                'employee_id' => 'EMP003',
                'department_id' => $finDept->id,
                'phone' => '08123456791',
                'birth_date' => '1983-07-10',
                'gender' => 'male',
            ],
        ];

        foreach ($managers as $managerData) {
            $manager = User::create(array_merge($managerData, [
                'password' => Hash::make('password'),
                'hire_date' => '2024-01-01',
                'address' => 'Jakarta, Indonesia',
                'position_id' => $managerPos->id,
                'annual_leave_quota' => 12,
                'remaining_leave' => 10,
                'is_active' => true,
            ]));
            $manager->assignRole('manager');
        }

        // Create Employees
        $employees = [
            // IT Department
            [
                'name' => 'John Doe',
                'email' => 'john.doe@wlbapp.com',
                'employee_id' => 'EMP004',
                'department_id' => $itDept->id,
                'position_id' => $seniorStaffPos->id,
                'manager_id' => 2, // Sarah Johnson
                'phone' => '08123456792',
                'birth_date' => '1992-08-15',
                'gender' => 'male',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@wlbapp.com',
                'employee_id' => 'EMP005',
                'department_id' => $itDept->id,
                'position_id' => $juniorStaffPos->id,
                'manager_id' => 2,
                'phone' => '08123456793',
                'birth_date' => '1995-12-03',
                'gender' => 'female',
            ],
            [
                'name' => 'David Wilson',
                'email' => 'david.wilson@wlbapp.com',
                'employee_id' => 'EMP006',
                'department_id' => $itDept->id,
                'position_id' => $specialistPos->id,
                'manager_id' => 2,
                'phone' => '08123456794',
                'birth_date' => '1990-04-22',
                'gender' => 'male',
            ],

            // Finance Department
            [
                'name' => 'Emily Brown',
                'email' => 'emily.brown@wlbapp.com',
                'employee_id' => 'EMP007',
                'department_id' => $finDept->id,
                'position_id' => $seniorStaffPos->id,
                'manager_id' => 3, // Michael Chen
                'phone' => '08123456795',
                'birth_date' => '1991-09-18',
                'gender' => 'female',
            ],
            [
                'name' => 'Robert Davis',
                'email' => 'robert.davis@wlbapp.com',
                'employee_id' => 'EMP008',
                'department_id' => $finDept->id,
                'position_id' => $juniorStaffPos->id,
                'manager_id' => 3,
                'phone' => '08123456796',
                'birth_date' => '1994-01-07',
                'gender' => 'male',
            ],

            // Marketing Department
            [
                'name' => 'Lisa Anderson',
                'email' => 'lisa.anderson@wlbapp.com',
                'employee_id' => 'EMP009',
                'department_id' => $mktDept->id,
                'position_id' => $supervisorPos->id,
                'phone' => '08123456797',
                'birth_date' => '1988-11-25',
                'gender' => 'female',
            ],
            [
                'name' => 'Tom Miller',
                'email' => 'tom.miller@wlbapp.com',
                'employee_id' => 'EMP010',
                'department_id' => $mktDept->id,
                'position_id' => $seniorStaffPos->id,
                'manager_id' => 7, // Lisa Anderson
                'phone' => '08123456798',
                'birth_date' => '1993-06-14',
                'gender' => 'male',
            ],

            // Operations Department
            [
                'name' => 'Anna Taylor',
                'email' => 'anna.taylor@wlbapp.com',
                'employee_id' => 'EMP011',
                'department_id' => $opsDept->id,
                'position_id' => $seniorStaffPos->id,
                'phone' => '08123456799',
                'birth_date' => '1989-02-28',
                'gender' => 'female',
            ],
            [
                'name' => 'Mark Johnson',
                'email' => 'mark.johnson@wlbapp.com',
                'employee_id' => 'EMP012',
                'department_id' => $opsDept->id,
                'position_id' => $juniorStaffPos->id,
                'manager_id' => 9, // Anna Taylor
                'phone' => '08123456800',
                'birth_date' => '1996-10-05',
                'gender' => 'male',
            ],

            // HR Department
            [
                'name' => 'Jennifer White',
                'email' => 'jennifer.white@wlbapp.com',
                'employee_id' => 'EMP013',
                'department_id' => $hrDept->id,
                'position_id' => $specialistPos->id,
                'manager_id' => 1, // Admin
                'phone' => '08123456801',
                'birth_date' => '1987-08-12',
                'gender' => 'female',
            ],
        ];

        foreach ($employees as $employeeData) {
            $employee = User::create(array_merge($employeeData, [
                'password' => Hash::make('password'),
                'hire_date' => '2024-01-15',
                'address' => 'Jakarta, Indonesia',
                'annual_leave_quota' => 12,
                'remaining_leave' => rand(8, 12),
                'is_active' => true,
            ]));
            
            // Assign role based on position level
            if ($employee->position && in_array($employee->position->level, ['manager', 'director'])) {
                $employee->assignRole('manager');
            } else {
                $employee->assignRole('employee');
            }
        }
    }
}
