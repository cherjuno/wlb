
# 🏢 WLB Monitoring System

**Work-Life Balance Monitoring System** - Sistem monitoring keseimbangan kerja-hidup karyawan dengan analisis data-driven dan sistem peringatan dini berbasis Laravel.

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)

---

## 📋 Daftar Isi

- [Overview](#-overview)
- [Fitur Utama](#-fitur-utama)
- [Algoritma WLB](#-algoritma-work-life-balance-wlb)
- [Job Stress Scale](#-job-stress-scale)
- [Update Terbaru](#-update-terbaru)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Penggunaan](#-penggunaan)
- [API Documentation](#-api-documentation)
- [Screenshots](#-screenshots)
- [Contributing](#-contributing)

---

## 🎯 Overview

WLB Monitoring System adalah aplikasi manajemen HR yang fokus pada monitoring dan analisis work-life balance karyawan. Sistem ini menggunakan pendekatan **data-driven** untuk mengidentifikasi karyawan yang berisiko mengalami burnout dan memberikan rekomendasi untuk perbaikan.

### 🏗️ Arsitektur Sistem

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Data Input    │    │   Processing    │    │     Output      │
│                 │    │                 │    │                 │
│ • Attendance    │───▶│ WLB Algorithm   │───▶│ • Dashboard     │
│ • Overtime      │    │ • Scoring       │    │ • Alerts        │
│ • Leave         │    │ • Risk Analysis │    │ • Reports       │
│ • Job Stress    │    │ • Predictions   │    │ • Recommendations│
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

---

## 🚀 Fitur Utama

### 👥 **Manajemen Pengguna**
- ✅ **CRUD User Management** untuk Admin & Manager
- ✅ **Role-based Access Control** (Admin, Manager, Employee)
- ✅ **Department & Position Management**
- ✅ **User Profile dengan WLB Statistics**
- ✅ **Team Management untuk Manager**

### ⏰ **Attendance Management**
- ✅ **Real-time Check-in/Check-out**
- ✅ **Quick Actions** (Mobile-friendly)
- ✅ **Attendance Reports & Analytics**
- ✅ **Late Detection & Monitoring**
- ✅ **Work Hours Calculation**

### 🏖️ **Leave Management**
- ✅ **Annual Leave Requests**
- ✅ **Sick Leave Tracking**
- ✅ **Leave Balance Monitoring**
- ✅ **Approval Workflow**
- ✅ **Leave Utilization Analysis**

### ⚡ **Overtime Management**
- ✅ **Overtime Request System**
- ✅ **Approval Workflow**
- ✅ **Overtime Hours Tracking**
- ✅ **Burnout Risk Analysis**
- ✅ **Detailed Overtime Reports**

### 📊 **WLB Monitoring**
- ✅ **Real-time WLB Score Calculation**
- ✅ **Red Zone Detection System**
- ✅ **Department WLB Analysis**
- ✅ **Burnout Risk Assessment**
- ✅ **Personal Recommendations**

### 🧠 **Job Stress Scale**
- ✅ **Monthly Stress Assessment**
- ✅ **Stress Level Categorization**
- ✅ **Team Stress Monitoring**
- ✅ **Stress History & Trends**
- ✅ **Administrative Oversight**

### 💰 **Salary Management**
- ✅ **Monthly Salary Information**
- ✅ **Compensation Tracking**
---

## 🧮 Algoritma Work-Life Balance (WLB)

### 🎯 **Konsep Dasar**

Sistem WLB menggunakan **scoring algorithm** yang menggabungkan multiple factors untuk menghasilkan skor 0-100:

```
WLB Score = 100 - (Penalty_BebanKerja + Penalty_CutiRendah + Penalty_Inkonsistensi)
```

### 📐 **I. Metrik Kuantitatif Beban Kerja**

#### **1. Rata-Rata Jam Kerja Mingguan**
```php
$weeklyWorkHours = $user->getWorkHoursThisWeek();
$threshold = 50; // jam per minggu

if ($weeklyWorkHours > $threshold) {
    $score -= 25; // Penalty berat
} elseif ($weeklyWorkHours > 45) {
    $score -= 10; // Penalty ringan
}
```

#### **2. Rasio Jam Lembur**
```php
$weeklyOvertimeHours = $user->getTotalOvertimeHoursThisWeek();
$redZoneThreshold = 8; // jam per minggu
$yellowZoneThreshold = 5; // jam per minggu

if ($weeklyOvertimeHours > $redZoneThreshold) {
    $score -= 30; // Penalty sangat berat
} elseif ($weeklyOvertimeHours > $yellowZoneThreshold) {
    $score -= 15; // Penalty sedang
}
```

### 🔄 **II. Metrik Pemulihan (Recovery)**

#### **1. Tingkat Pengambilan Cuti Tahunan**
```php
$leaveUtilizationRate = ($usedLeaves / $annualQuota) * 100;

if ($leaveUtilizationRate < 30%) {
    $score -= 10; // Penalty untuk tidak menggunakan cuti
}
```

#### **2. Konsistensi Jam Kerja**
```php
$workHoursVariance = calculateVariance($weeklyWorkHours);

if ($workHoursVariance > 4) {
    $score -= 10; // Penalty untuk inkonsistensi
}
```

### 🚨 **III. Zona Classification**

| Zona | Score Range | Status | Deskripsi |
|------|------------|--------|-----------|
| 🟢 **Hijau** | 80-100 | Excellent | WLB sangat baik |
| 🔵 **Biru** | 60-79 | Good | WLB baik |
| 🟡 **Kuning** | 40-59 | Warning | Perlu perhatian |
| 🔴 **Merah** | 0-39 | Critical | Zona bahaya - butuh intervensi |

### ⚙️ **IV. Konfigurasi Threshold**

Semua threshold dapat dikonfigurasi via `WlbSetting`:

```php
// Pengaturan default
'wlb_red_zone_overtime_threshold' => 8,     // jam/minggu
'wlb_yellow_zone_overtime_threshold' => 5,  // jam/minggu
'wlb_red_zone_work_hours_threshold' => 50,  // jam/minggu
'max_overtime_hours_per_month' => 40,       // jam/bulan
```

### 🎯 **V. Sistem Peringatan Dini**

```php
public static function getRedZoneEmployees()
{
    $redZoneUsers = [];
    foreach ($users as $user) {
        $score = calculateWlbScore($user->id);
        if ($score < 40) { // Zona Merah
            $redZoneUsers[] = [
                'user' => $user,
                'score' => $score,
                'burnoutRisk' => calculateBurnoutRisk($user->id),
                'recommendations' => generateRecommendations($user->id)
            ];
        }
    }
    return $redZoneUsers;
}
```

---

## 🧠 Job Stress Scale

### 📊 **Konsep & Tujuan**

Job Stress Scale adalah sistem penilaian tingkat stress kerja karyawan yang dilakukan secara **berkala (bulanan)** untuk melengkapi analisis WLB objektif dengan data subjektif dari karyawan.

### 🔄 **Workflow Pengisian**

```
Monthly Reminder → Employee Assessment → Stress Calculation → Manager Alert → HR Dashboard
```

### 📝 **Kategori Stress Level**

| Level | Score Range | Status | Indikator | Action Required |
|-------|------------|--------|-----------|-----------------|
| 🟢 **Rendah** | 0-30% | Low Stress | Kondisi optimal | Maintain |
| 🟡 **Sedang** | 31-60% | Moderate | Perlu monitoring | Watch closely |
| 🔴 **Tinggi** | 61-100% | High Stress | Intervensi diperlukan | Immediate action |

### 📊 **Form Assessment**

Karyawan mengisi kuesioner bulanan yang mencakup:

1. **Beban Kerja** (Workload pressure)
2. **Tekanan Waktu** (Time pressure) 
3. **Hubungan Interpersonal** (Workplace relationships)
4. **Kontrol Pekerjaan** (Job control)
5. **Dukungan Supervisor** (Supervisor support)
6. **Kejelasan Peran** (Role clarity)
7. **Kesempatan Pengembangan** (Development opportunities)

### 👔 **Manager Dashboard**

Manager dapat memonitor stress level tim melalui:

- **Team Stress Overview**
- **High Stress Alerts** (🔴 counter di sidebar)
- **Individual Stress History**
- **Department Comparison**
- **Trend Analysis**

### 🛡️ **Admin Oversight**

Admin memiliki akses ke:

- **Company-wide Stress Statistics**
- **Completion Rate Monitoring** (% counter di sidebar)
- **Department Stress Rankings**
- **Historical Trends & Reports**
- **Intervention Tracking**

### 🔔 **Notification System**

```php
// Auto-notification untuk manager jika subordinate stress tinggi
if ($stressLevel === 'high') {
    notifyManager($user->manager, [
        'employee' => $user->name,
        'stress_level' => 'HIGH',
        'action_required' => true
    ]);
}
```

---

## 🆕 Update Terbaru

### 🎉 **Major Updates (v2.0)**

#### **1. User Management System**
- ✅ **Complete CRUD** untuk admin & manager
- ✅ **User Detail View** dengan WLB statistics
- ✅ **Team Management** untuk manager
- ✅ **Advanced User Search & Filtering**

#### **2. Enhanced Views**
- ✅ **Profile Show Page** - comprehensive user details
- ✅ **User Edit Form** - pre-filled dengan validation
- ✅ **Overtime Detail View** - approval workflow
- ✅ **Modern UI/UX** dengan Tailwind CSS

#### **3. WLB Algorithm Improvements**
- ✅ **Refined Scoring System** 
- ✅ **Department-level Analysis**
- ✅ **Burnout Risk Calculation**
- ✅ **Personal Recommendations Engine**

#### **4. Job Stress Scale Integration**
- ✅ **Monthly Assessment System**
- ✅ **Manager Notifications**
- ✅ **Admin Completion Tracking**
- ✅ **Stress Level History**

#### **5. Enhanced Authorization**
- ✅ **Role-based Permissions**
- ✅ **Policy-driven Access Control**
- ✅ **Manager Subordinate Restrictions**

#### **6. Bug Fixes & Optimizations**
- ✅ **Annual Leave Quota** calculation fixed
- ✅ **Red Zone Detection** working properly
- ✅ **Manager Checkout** issues resolved
- ✅ **Navigation Menu** improvements
- ✅ **Database Optimization**

---

## 🛠️ Instalasi

### 📋 **Requirements**

- PHP 8.3+
- Laravel 12.x
- MySQL 8.0+
- Composer
- Node.js & NPM

### 🚀 **Quick Start**

```bash
# Clone repository
git clone https://github.com/cherjuno/wlb.git
cd wlb

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate --seed

# Asset compilation
npm run build

# Start server
php artisan serve
```

### 👤 **Default Users**

| Role | Email | Password | Access Level |
|------|-------|----------|--------------|
| Admin | admin@wlb.com | password | Full system access |
| Manager | manager@wlb.com | password | Team management |
| Employee | employee@wlb.com | password | Self-service |

---

## ⚙️ Konfigurasi

### 🔧 **WLB Settings**

Konfigurasi threshold WLB via admin panel atau database:

```sql
INSERT INTO wlb_settings (key, name, value, type) VALUES
('wlb_red_zone_overtime_threshold', 'Red Zone Overtime Threshold', '8', 'integer'),
('wlb_yellow_zone_overtime_threshold', 'Yellow Zone Overtime Threshold', '5', 'integer'),
('wlb_red_zone_work_hours_threshold', 'Red Zone Work Hours Threshold', '50', 'integer'),
('max_overtime_hours_per_month', 'Max Overtime Hours Per Month', '40', 'integer');
```

### 📊 **Job Stress Configuration**

```sql
INSERT INTO wlb_settings (key, name, value, type) VALUES
('stress_reminder_day', 'Monthly Reminder Day', '1', 'integer'),
('high_stress_threshold', 'High Stress Threshold', '60', 'integer');
```

### 🏢 **Company Settings**

```sql
INSERT INTO wlb_settings (key, name, value, type) VALUES
('company_name', 'Company Name', 'PT. Pelangi Prima Mandiri', 'string'),
('working_hours_per_day', 'Working Hours Per Day', '8', 'integer'),
('working_days_per_week', 'Working Days Per Week', '5', 'integer');
```

---

## 📖 Penggunaan

### 👤 **Employee Workflow**

1. **Daily Attendance**
   ```
   Login → Dashboard → Quick Check-in → Work → Quick Check-out
   ```

2. **Leave Request**
   ```
   Cuti → Create Request → Fill Form → Submit → Wait Approval
   ```

3. **Monthly Stress Assessment**
   ```
   Job Stress Scale → Fill Assessment → Submit → View History
   ```

### 👔 **Manager Workflow**

1. **Team Monitoring**
   ```
   Dashboard → Team Overview → Check WLB Scores → Review Alerts
   ```

2. **Approval Process**
   ```
   Notifications → Review Requests → Approve/Reject → Add Comments
   ```

3. **Stress Management**
   ```
   Stres Tim → Check High Stress Alerts → Contact Employee → Take Action
   ```

### 🛡️ **Admin Workflow**

1. **User Management**
   ```
   Kelola User → Add/Edit Users → Assign Roles → Monitor Status
   ```

2. **System Configuration**
   ```
   Settings → WLB Thresholds → Company Settings → Save Changes
   ```

3. **Analytics & Reports**
   ```
   Dashboard → Company Overview → Department Analysis → Generate Reports
   ```

---

## 📊 API Documentation

### 🔗 **WLB API Endpoints**

```php
// Get user WLB score
GET /api/wlb/score/{user_id}

// Get red zone employees
GET /api/wlb/red-zone

// Get department WLB summary
GET /api/wlb/department/{department_id}

// Get WLB recommendations
GET /api/wlb/recommendations/{user_id}
```

### 📈 **Stress Scale API**

```php
// Submit stress assessment
POST /api/stress/submit

// Get stress history
GET /api/stress/history/{user_id}

// Get team stress overview
GET /api/stress/team/{manager_id}
```

### ⏰ **Attendance API**

```php
// Quick check-in
POST /api/attendance/check-in

// Quick check-out  
POST /api/attendance/check-out

// Get today status
GET /api/attendance/today-status
```

---

## 📸 Screenshots

### 🏠 **Dashboard**
- Real-time WLB metrics
- Quick actions
- Team overview
- Notifications

### 👤 **User Management**
- Complete CRUD interface
- Advanced filtering
- Team hierarchy
- Role management

### 📊 **WLB Analytics**
- Department comparison
- Trend analysis
- Red zone alerts
- Personal recommendations

### 🧠 **Stress Management**
- Monthly assessment
- Team monitoring
- Historical trends
- Intervention tracking

---

## 🤝 Contributing

### 🔄 **Development Workflow**

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

### 📋 **Code Standards**

- Follow PSR-12 coding standards
- Write comprehensive tests
- Document all public methods
- Use meaningful commit messages

### 🐛 **Bug Reports**

Please include:
- Laravel version
- PHP version
- Steps to reproduce
- Expected vs actual behavior
- Error logs

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 🙏 Acknowledgments

- Laravel Framework
- Tailwind CSS
- Spatie Permissions
- Chart.js
- Alpine.js

---

## 📞 Support

- **Email**: support@wlb-system.com
- **Documentation**: [docs.wlb-system.com](https://docs.wlb-system.com)
- **Issues**: [GitHub Issues](https://github.com/cherjuno/wlb/issues)

---

<div align="center">

**🎯 WLB Monitoring System - Membangun lingkungan kerja yang sehat dan produktif** 

Made with ❤️ by [PT. Pelangi Prima Mandiri]()

</div>
('stress_reminder_day', 'Monthly Reminder Day', '1', 'integer'),
('high_stress_threshold', 'High Stress Threshold', '60', 'integer');
```

### 🏢 **Company Settings**

```sql
INSERT INTO wlb_settings (key, name, value, type) VALUES
('company_name', 'Company Name', 'PT. Pelangi Prima Mandiri', 'string'),
('working_hours_per_day', 'Working Hours Per Day', '8', 'integer'),
('working_days_per_week', 'Working Days Per Week', '5', 'integer');
```

---

## 📖 Penggunaan

### 👤 **Employee Workflow**

1. **Daily Attendance**
   ```
   Login → Dashboard → Quick Check-in → Work → Quick Check-out
   ```

2. **Leave Request**
   ```
   Cuti → Create Request → Fill Form → Submit → Wait Approval
   ```

3. **Monthly Stress Assessment**
   ```
   Job Stress Scale → Fill Assessment → Submit → View History
   ```

### 👔 **Manager Workflow**

1. **Team Monitoring**
   ```
   Dashboard → Team Overview → Check WLB Scores → Review Alerts
   ```

2. **Approval Process**
   ```
   Notifications → Review Requests → Approve/Reject → Add Comments
   ```

3. **Stress Management**
   ```
   Stres Tim → Check High Stress Alerts → Contact Employee → Take Action
   ```

### 🛡️ **Admin Workflow**

1. **User Management**
   ```
   Kelola User → Add/Edit Users → Assign Roles → Monitor Status
   ```

2. **System Configuration**
   ```
   Settings → WLB Thresholds → Company Settings → Save Changes
   ```

3. **Analytics & Reports**
   ```
   Dashboard → Company Overview → Department Analysis → Generate Reports
   ```

---

## 📊 API Documentation

### 🔗 **WLB API Endpoints**

```php
// Get user WLB score
GET /api/wlb/score/{user_id}

// Get red zone employees
GET /api/wlb/red-zone

// Get department WLB summary
GET /api/wlb/department/{department_id}

// Get WLB recommendations
GET /api/wlb/recommendations/{user_id}
```

### 📈 **Stress Scale API**

```php
// Submit stress assessment
POST /api/stress/submit

// Get stress history
GET /api/stress/history/{user_id}

// Get team stress overview
GET /api/stress/team/{manager_id}
```

### ⏰ **Attendance API**

```php
// Quick check-in
POST /api/attendance/check-in

// Quick check-out  
POST /api/attendance/check-out

// Get today status
GET /api/attendance/today-status
```

---

## 📸 Screenshots

### 🏠 **Dashboard**
- Real-time WLB metrics
- Quick actions
- Team overview
- Notifications

### 👤 **User Management**
- Complete CRUD interface
- Advanced filtering
- Team hierarchy
- Role management

### 📊 **WLB Analytics**
- Department comparison
- Trend analysis
- Red zone alerts
- Personal recommendations

### 🧠 **Stress Management**
- Monthly assessment
- Team monitoring
- Historical trends
- Intervention tracking

---

## 🤝 Contributing

### 🔄 **Development Workflow**

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

### 📋 **Code Standards**

- Follow PSR-12 coding standards
- Write comprehensive tests
- Document all public methods
- Use meaningful commit messages

### 🐛 **Bug Reports**

Please include:
- Laravel version
- PHP version
- Steps to reproduce
- Expected vs actual behavior
- Error logs

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 🙏 Acknowledgments

- **Backend**: Laravel 12, PHP 8.3
- **Frontend**: Blade, Tailwind CSS, Alpine.js, Chart.js
- **Database**: MySQL (default), SQLite supported
- **Authentication**: Laravel Breeze/Jetstream
- **Authorization**: Spatie Laravel Permission (roles: admin, manager, employee)

## Setup Instructions

1. **Clone the repository**
	```sh
	git clone <repo-url>
	cd wlb-monitoring
	```
2. **Install dependencies**
	```sh
	composer install
	npm install
	```
3. **Copy and configure environment**
	```sh
	cp .env.example .env
	# Edit .env for your DB and mail settings
	php artisan key:generate
	```
4. **Run migrations and seeders**
	```sh
	php artisan migrate --seed
	```
5. **Build frontend assets**
	```sh
	npm run build
	# or for development
	npm run dev
	```
6. **Start the development server**
	```sh
	php artisan serve
	```

7. **Login**
	- Default admin: `admin@wlbapp.com` / `password`
	- Manager: `sarah.johnson@wlbapp.com` / `password`, `michael.chen@wlbapp.com` / `password`
	- Change credentials after first login.

## Default Users After Fresh Install

The database seeder creates these users (all passwords: `password`):

### Admin
- **Email:** admin@wlbapp.com
- **Role:** admin

### Managers
- **Sarah Johnson** — sarah.johnson@wlbapp.com
- **Michael Chen** — michael.chen@wlbapp.com

All default users have password: `password`. You can log in as admin or manager and change the password after first login.

## Usage

- Access the app at [http://127.0.0.1:8000](http://127.0.0.1:8000)
- Use the sidebar to navigate between Dashboard, Attendance, Leave, Overtime, Approvals, Reports, and User Management.
- Use the filter bars and export buttons on report pages for analytics.
- Switch between dark and light mode using the toggle in the top bar.

## Customization

- **Roles & Permissions**: Managed via Spatie package; see `config/permission.php`.
- **UI**: Tailwind CSS and Alpine.js for rapid customization.
- **Charts**: Chart.js is loaded via CDN in the layout.

## Troubleshooting

- If you see `Call to undefined method ...::middleware()`, ensure `app/Http/Controllers/Controller.php` extends `Illuminate\Routing\Controller`.
- For asset issues, run `npm run build` or `npm run dev`.
- For database errors, check your `.env` DB settings and run migrations.

## License

MIT. See [LICENSE](LICENSE).

---

For feature requests or bug reports, please open an issue or contact the maintainer.
