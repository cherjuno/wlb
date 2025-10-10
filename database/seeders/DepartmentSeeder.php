<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Human Resources',
                'code' => 'HR',
                'description' => 'Mengelola sumber daya manusia, rekrutmen, dan pengembangan karyawan',
                'manager_email' => 'hr.manager@company.com',
                'is_active' => true,
            ],
            [
                'name' => 'Information Technology',
                'code' => 'IT',
                'description' => 'Mengelola infrastruktur teknologi dan pengembangan sistem',
                'manager_email' => 'it.manager@company.com',
                'is_active' => true,
            ],
            [
                'name' => 'Finance & Accounting',
                'code' => 'FIN',
                'description' => 'Mengelola keuangan, akuntansi, dan pelaporan keuangan',
                'manager_email' => 'finance.manager@company.com',
                'is_active' => true,
            ],
            [
                'name' => 'Marketing',
                'code' => 'MKT',
                'description' => 'Mengelola strategi pemasaran dan promosi produk',
                'manager_email' => 'marketing.manager@company.com',
                'is_active' => true,
            ],
            [
                'name' => 'Operations',
                'code' => 'OPS',
                'description' => 'Mengelola operasional harian dan proses bisnis',
                'manager_email' => 'operations.manager@company.com',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
