<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WlbSetting;

class WlbSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Work hours settings
            [
                'key' => 'standard_work_hours_per_day',
                'name' => 'Jam Kerja Standar per Hari',
                'description' => 'Jumlah jam kerja standar dalam sehari',
                'value' => '8',
                'type' => 'number',
                'category' => 'work_hours',
            ],
            [
                'key' => 'standard_work_hours_per_week',
                'name' => 'Jam Kerja Standar per Minggu',
                'description' => 'Jumlah jam kerja standar dalam seminggu',
                'value' => '40',
                'type' => 'number',
                'category' => 'work_hours',
            ],
            [
                'key' => 'standard_start_time',
                'name' => 'Jam Masuk Standar',
                'description' => 'Waktu masuk kerja standar',
                'value' => '08:00',
                'type' => 'string',
                'category' => 'work_hours',
            ],
            [
                'key' => 'standard_end_time',
                'name' => 'Jam Pulang Standar',
                'description' => 'Waktu pulang kerja standar',
                'value' => '17:00',
                'type' => 'string',
                'category' => 'work_hours',
            ],

            // Overtime settings
            [
                'key' => 'max_overtime_hours_per_week',
                'name' => 'Maksimal Lembur per Minggu',
                'description' => 'Batas maksimal jam lembur dalam seminggu',
                'value' => '10',
                'type' => 'number',
                'category' => 'overtime',
            ],
            [
                'key' => 'max_overtime_hours_per_month',
                'name' => 'Maksimal Lembur per Bulan',
                'description' => 'Batas maksimal jam lembur dalam sebulan',
                'value' => '40',
                'type' => 'number',
                'category' => 'overtime',
            ],
            [
                'key' => 'overtime_weekday_multiplier',
                'name' => 'Multiplier Lembur Hari Kerja',
                'description' => 'Pengali tarif lembur untuk hari kerja',
                'value' => '1.5',
                'type' => 'number',
                'category' => 'overtime',
            ],
            [
                'key' => 'overtime_weekend_multiplier',
                'name' => 'Multiplier Lembur Akhir Pekan',
                'description' => 'Pengali tarif lembur untuk akhir pekan',
                'value' => '2.0',
                'type' => 'number',
                'category' => 'overtime',
            ],

            // Leave settings
            [
                'key' => 'annual_leave_quota',
                'name' => 'Kuota Cuti Tahunan',
                'description' => 'Jumlah cuti tahunan yang diberikan per tahun',
                'value' => '12',
                'type' => 'number',
                'category' => 'leave',
            ],
            [
                'key' => 'max_consecutive_leave_days',
                'name' => 'Maksimal Cuti Berturut-turut',
                'description' => 'Maksimal hari cuti yang dapat diambil berturut-turut',
                'value' => '5',
                'type' => 'number',
                'category' => 'leave',
            ],

            // WLB threshold settings
            [
                'key' => 'wlb_red_zone_overtime_threshold',
                'name' => 'Ambang Batas Zona Merah WLB (Lembur)',
                'description' => 'Jam lembur per minggu yang menandakan zona merah WLB',
                'value' => '8',
                'type' => 'number',
                'category' => 'wlb_threshold',
            ],
            [
                'key' => 'wlb_yellow_zone_overtime_threshold',
                'name' => 'Ambang Batas Zona Kuning WLB (Lembur)',
                'description' => 'Jam lembur per minggu yang menandakan zona kuning WLB',
                'value' => '5',
                'type' => 'number',
                'category' => 'wlb_threshold',
            ],
            [
                'key' => 'wlb_red_zone_work_hours_threshold',
                'name' => 'Ambang Batas Zona Merah WLB (Jam Kerja)',
                'description' => 'Total jam kerja per minggu yang menandakan zona merah WLB',
                'value' => '50',
                'type' => 'number',
                'category' => 'wlb_threshold',
            ],

            // Notification settings
            [
                'key' => 'enable_overtime_alerts',
                'name' => 'Aktifkan Alert Lembur',
                'description' => 'Mengirim notifikasi ketika lembur melebihi batas',
                'value' => 'true',
                'type' => 'boolean',
                'category' => 'notification',
            ],
            [
                'key' => 'enable_leave_balance_alerts',
                'name' => 'Aktifkan Alert Saldo Cuti',
                'description' => 'Mengirim notifikasi tentang saldo cuti',
                'value' => 'true',
                'type' => 'boolean',
                'category' => 'notification',
            ],

            // Company settings
            [
                'key' => 'company_name',
                'name' => 'Nama Perusahaan',
                'description' => 'Nama perusahaan yang ditampilkan di sistem',
                'value' => 'Perusahaan A',
                'type' => 'string',
                'category' => 'company',
            ],
            [
                'key' => 'company_address',
                'name' => 'Alamat Perusahaan',
                'description' => 'Alamat lengkap perusahaan',
                'value' => 'Jakarta, Indonesia',
                'type' => 'string',
                'category' => 'company',
            ],
        ];

        foreach ($settings as $setting) {
            WlbSetting::create($setting);
        }
    }
}
