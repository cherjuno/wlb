<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $managerRole = Role::create(['name' => 'manager']);
        $employeeRole = Role::create(['name' => 'employee']);

        // Create permissions
        $permissions = [
            // User management
            'manage_users',
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',

            // Department management
            'manage_departments',
            'view_departments',

            // Position management
            'manage_positions',
            'view_positions',

            // Attendance management
            'view_all_attendances',
            'view_own_attendance',
            'manage_attendance',

            // Leave management
            'view_all_leaves',
            'view_own_leaves',
            'approve_leaves',
            'reject_leaves',
            'create_leave_request',

            // Overtime management
            'view_all_overtimes',
            'view_own_overtimes',
            'approve_overtimes',
            'reject_overtimes',
            'create_overtime_request',

            // Dashboard access
            'view_admin_dashboard',
            'view_manager_dashboard',
            'view_employee_dashboard',

            // Reports
            'export_reports',
            'view_analytics',

            // Settings
            'manage_wlb_settings',
            'view_wlb_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole->givePermissionTo(Permission::all());

        $managerRole->givePermissionTo([
            'view_users',
            'view_departments',
            'view_positions',
            'view_all_attendances',
            'view_own_attendance',
            'view_all_leaves',
            'view_own_leaves',
            'approve_leaves',
            'reject_leaves',
            'create_leave_request',
            'view_all_overtimes',
            'view_own_overtimes',
            'approve_overtimes',
            'reject_overtimes',
            'create_overtime_request',
            'view_manager_dashboard',
            'export_reports',
            'view_analytics',
            'view_wlb_settings',
        ]);

        $employeeRole->givePermissionTo([
            'view_own_attendance',
            'view_own_leaves',
            'create_leave_request',
            'view_own_overtimes',
            'create_overtime_request',
            'view_employee_dashboard',
        ]);
    }
}
