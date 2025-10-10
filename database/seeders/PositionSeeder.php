<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            // Staff level
            [
                'name' => 'Junior Staff',
                'code' => 'JS',
                'description' => 'Posisi entry level untuk fresh graduate',
                'level' => 'staff',
                'is_active' => true,
            ],
            [
                'name' => 'Senior Staff',
                'code' => 'SS',
                'description' => 'Staff dengan pengalaman 2-4 tahun',
                'level' => 'staff',
                'is_active' => true,
            ],
            [
                'name' => 'Specialist',
                'code' => 'SPE',
                'description' => 'Spesialis dengan keahlian khusus',
                'level' => 'staff',
                'is_active' => true,
            ],

            // Supervisor level
            [
                'name' => 'Team Lead',
                'code' => 'TL',
                'description' => 'Memimpin tim kecil 3-5 orang',
                'level' => 'supervisor',
                'is_active' => true,
            ],
            [
                'name' => 'Supervisor',
                'code' => 'SUP',
                'description' => 'Mengawasi operasional harian tim',
                'level' => 'supervisor',
                'is_active' => true,
            ],

            // Manager level
            [
                'name' => 'Assistant Manager',
                'code' => 'AM',
                'description' => 'Asisten manajer departemen',
                'level' => 'manager',
                'is_active' => true,
            ],
            [
                'name' => 'Manager',
                'code' => 'MGR',
                'description' => 'Manajer departemen',
                'level' => 'manager',
                'is_active' => true,
            ],

            // Director level
            [
                'name' => 'Director',
                'code' => 'DIR',
                'description' => 'Direktur divisional',
                'level' => 'director',
                'is_active' => true,
            ],
        ];

        foreach ($positions as $position) {
            Position::create($position);
        }
    }
}
