
# 🏢 WLB Monitoring System

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com/)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php)](https://php.net/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css)](https://tailwindcss.com/)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](LICENSE)

> **Sistem monitoring Work-Life Balance berbasis Laravel dengan analisis stress dan dashboard analytics komprehensif**

## 📋 Daftar Isi

- [🎯 Tentang Sistem](#-tentang-sistem)
- [✨ Fitur Utama](#-fitur-utama)  
- [👥 Manajemen Role](#-manajemen-role)
- [📊 Indikator WLB](#-indikator-wlb)
- [🧮 Perhitungan Work-Life Balance](#-perhitungan-work-life-balance)
- [📈 Job Stress Scale](#-job-stress-scale)
- [🔧 Instalasi](#-instalasi)
- [🚀 Penggunaan](#-penggunaan)
- [📱 Antarmuka Pengguna](#-antarmuka-pengguna)
- [🛠️ Konfigurasi](#️-konfigurasi)
- [📖 API Documentation](#-api-documentation)
- [🤝 Kontribusi](#-kontribusi)
- [📄 Lisensi](#-lisensi)

---

## 🎯 Tentang Sistem

**WLB Monitoring System** adalah aplikasi web berbasis Laravel yang dirancang untuk memantau dan menganalisis **Work-Life Balance** karyawan di perusahaan. Sistem ini mengintegrasikan manajemen absensi, cuti, lembur, dan penilaian tingkat stres kerja untuk memberikan insight komprehensif tentang kesejahteraan karyawan.

### � Teknologi yang Digunakan

- **Backend**: Laravel 12.x (PHP 8.3+)
- **Frontend**: Blade Templates + Alpine.js
- **Styling**: Tailwind CSS 3.x
- **Database**: MySQL/SQLite
- **Charts**: Chart.js
- **Authentication**: Laravel Breeze + Spatie Roles

---

## ✨ Fitur Utama

### � **Manajemen Absensi**
- ✅ Clock-in/Clock-out otomatis dengan timestamp
- 📍 Tracking lokasi dan IP address
- 📊 Laporan absensi harian, mingguan, bulanan
- ⏰ Monitoring keterlambatan dan jam kerja
- 📈 Dashboard analytics kehadiran

### 📅 **Manajemen Cuti**
- 🏖️ Pengajuan cuti dengan berbagai kategori:
  - Cuti Tahunan
  - Cuti Sakit  
  - Cuti Melahirkan
  - Cuti Darurat
- 🔄 Workflow approval multi-level
- 📋 History dan tracking status cuti
- 📊 Saldo cuti otomatis

### � **Manajemen Lembur**
- ⏱️ Pengajuan lembur dengan durasi fleksibel (1-4 jam)
- 🕐 Penjadwalan waktu mulai dan selesai
- 📝 Dokumentasi alasan dan deskripsi pekerjaan
- 💰 Kalkulasi otomatis kompensasi lembur
- ✅ Sistem approval dengan notifikasi

### 📊 **Job Stress Scale Assessment**
- 🧠 Kuesioner standar untuk mengukur tingkat stres kerja
- 📈 Scoring otomatis berdasarkan metodologi ilmiah
- 📅 Assessment bulanan wajib untuk semua karyawan  
- 🚨 Alert untuk tingkat stres tinggi
- 📋 History dan trend analysis

### � **WLB Analytics Dashboard**
- 🎯 **WLB-Stress Matrix**: Visualisasi 2D distribusi karyawan
- 📊 Real-time charts dan graphs
- 🔍 Filter berdasarkan periode, departemen, role
- 📱 Export data dalam format Excel/PDF
- 🎨 Interactive heatmaps dan scatter plots

### 👤 **Manajemen Profil & Gaji**
- 👨‍💼 Profile management lengkap
- 💰 Informasi gaji bulanan dan tunjangan
- 🏆 Achievement dan performance metrics  
- 📸 Photo upload dan bio personal
- 🔐 Password dan security settings
---

## 👥 Manajemen Role

Sistem mengimplementasikan **3 tingkat akses** dengan hak dan tanggung jawab yang berbeda:

### 🔴 **ADMINISTRATOR**
> **Akses Penuh** - Super User dengan kontrol sistem menyeluruh

#### 🛡️ **Hak Akses**:
- ✅ **User Management**: Create, Read, Update, Delete semua user
- ✅ **Analytics Global**: Akses semua data analytics perusahaan
- ✅ **Job Stress Administration**: Monitoring stress level semua karyawan
- ✅ **System Settings**: Konfigurasi sistem dan company settings
- ✅ **Approval Authority**: Approve/reject semua pengajuan cuti dan lembur
- ✅ **Report Generation**: Generate laporan komprehensif semua modul
- ✅ **User Role Management**: Assign dan modify roles karyawan

#### 📋 **Dashboard Admin**:
```
┌─ Total Employees: XXX        ┌─ Pending Approvals: XX
├─ Active Users: XXX           ├─ High Stress Alerts: XX  
├─ Monthly Attendance: XX%     ├─ System Health: ✅
└─ Completion Rate: XX%        └─ Last Backup: XX hours ago
```

---

### � **MANAGER**  
> **Team Management** - Mengelola tim dan approval workflow

#### 🛡️ **Hak Akses**:
- ✅ **Team Management**: Akses data tim langsung (subordinates)
- ✅ **Approval Authority**: Approve/reject pengajuan tim
- ✅ **Team Analytics**: Dashboard analytics khusus tim
- ✅ **Stress Monitoring**: Monitoring tingkat stres anggota tim
- ✅ **Report Team**: Generate laporan tim dan individual
- ❌ **User Creation**: Tidak dapat membuat user baru
- ❌ **System Settings**: Tidak dapat mengubah konfigurasi sistem

#### 📋 **Dashboard Manager**:
```
┌─ Team Members: XX            ┌─ Team Pending: XX
├─ Team Attendance: XX%        ├─ High Stress Team: XX
├─ Team Performance: ⭐⭐⭐⭐    ├─ Team WLB Score: XX/100
└─ Active Projects: XX         └─ Monthly Target: XX%
```

#### 👥 **Subordinate Management**:
- View dan manage direct reports
- Approve leave requests dari anggota tim
- Monitor team stress levels dan workload
- Generate team performance reports

---

### 🟢 **EMPLOYEE**
> **Personal Data** - Akses terbatas pada data pribadi

#### 🛡️ **Hak Akses**:
- ✅ **Personal Attendance**: Clock-in/out dan view history pribadi
- ✅ **Leave Application**: Mengajukan cuti dan tracking status
- ✅ **Overtime Request**: Mengajukan lembur dan monitoring approval
- ✅ **Job Stress Assessment**: Mengisi assessment stress bulanan
- ✅ **Profile Management**: Update informasi pribadi dan password
- ✅ **Personal Analytics**: View personal WLB score dan trends
- ❌ **Team Data**: Tidak dapat melihat data karyawan lain
- ❌ **Approval Rights**: Tidak dapat approve/reject pengajuan

#### 📋 **Dashboard Employee**:
```
┌─ Today's Attendance: ✅/❌   ┌─ Personal WLB: XX/100
├─ This Month Leaves: XX       ├─ Stress Level: Low/Med/High
├─ Overtime Hours: XX          ├─ Salary: Rp. XX.XXX.XXX
└─ Pending Requests: XX        └─ Next Assessment: XX days
```

---

## � Indikator WLB

Sistem menggunakan **8 indikator utama** untuk mengukur Work-Life Balance:

### 1. 🕒 **Attendance Rate (Tingkat Kehadiran)**
```
Formula: (Jumlah Hari Hadir / Total Hari Kerja) × 100%

Bobot: 15%
Excellent: > 95%
Good: 85-95%  
Average: 75-85%
Poor: < 75%
```

### 2. ⏰ **Punctuality Score (Skor Ketepatan Waktu)**
```
Formula: (Hari Tepat Waktu / Total Hari Hadir) × 100%

Bobot: 10%
Excellent: > 90%
Good: 80-90%
Average: 70-80%  
Poor: < 70%
```

### 3. 🌙 **Overtime Hours (Jam Lembur)**
```
Formula: Total Jam Lembur per Bulan

Bobot: 20%
Excellent: 0-10 jam
Good: 11-20 jam
Average: 21-30 jam
Poor: > 30 jam
```

### 4. 🏖️ **Leave Utilization (Pemanfaatan Cuti)**
```
Formula: (Cuti Digunakan / Cuti Tersedia) × 100%

Bobot: 15%
Excellent: 60-80%
Good: 40-60% atau 80-100%
Average: 20-40%
Poor: 0-20% atau > 100%
```

### 5. 📈 **Job Stress Level (Tingkat Stres Kerja)**
```
Formula: Skor dari Job Stress Scale Questionnaire

Bobot: 25% (Tertinggi)
Low Stress: 1-2.5
Moderate Stress: 2.6-3.5
High Stress: 3.6-5.0
```

### 6. 💼 **Workload Balance (Keseimbangan Beban Kerja)**
```
Formula: (Task Completion Rate + Meeting Hours + Project Involvement)

Bobot: 10%
Balanced: Skor optimal berdasarkan role
Overloaded: > 120% kapasitas
Underloaded: < 60% kapasitas
```

### 7. 🎯 **Goal Achievement (Pencapaian Target)**
```
Formula: (Target Tercapai / Total Target) × 100%

Bobot: 3%
Excellent: > 100%
Good: 90-100%
Average: 80-90%
Poor: < 80%
```

### 8. 🤝 **Team Collaboration (Kolaborasi Tim)**
```
Formula: Peer Assessment + Meeting Participation + Communication

Bobot: 2%
Excellent: Aktif berkolaborasi
Good: Kolaborasi standar
Average: Kolaborasi minimal
Poor: Kurang kolaborasi
```

---

## 🧮 Perhitungan Work-Life Balance

### 📐 **Formula Master WLB Score**

```mathematical
WLB Score = Σ(Indikator × Bobot) / 100

Dimana:
- Attendance Rate × 15%
- Punctuality × 10%  
- Overtime (Inverse) × 20%
- Leave Utilization × 15%
- Stress Level (Inverse) × 25%
- Workload Balance × 10%
- Goal Achievement × 3%
- Team Collaboration × 2%
```

### 🎯 **Kategori WLB Score**

| Score Range | Kategori | Status | Deskripsi |
|-------------|----------|--------|-----------|
| **90-100** | 🟢 **EXCELLENT** | ✅ Optimal | Work-life balance sangat baik, karyawan sehat dan produktif |
| **75-89** | 🔵 **GOOD** | ✅ Sehat | Work-life balance baik dengan ruang perbaikan kecil |
| **60-74** | 🟡 **AVERAGE** | ⚠️ Perlu Perhatian | Beberapa area perlu diperbaiki untuk WLB optimal |
| **45-59** | 🟠 **POOR** | ⚠️ Risiko Tinggi | Memerlukan intervensi segera dan action plan |
| **0-44** | 🔴 **CRITICAL** | 🚨 Darurat | Risiko burnout tinggi, butuh immediate action |

### 📊 **WLB-Stress Matrix**

Sistem menggunakan **2D Matrix** untuk visualisasi hubungan antara WLB Score dan Stress Level:

```
       │ Low Stress │ Moderate   │ High Stress │
       │ (1.0-2.5)  │ (2.6-3.5)  │ (3.6-5.0)   │
───────┼────────────┼────────────┼─────────────┤
HIGH   │ � STAR     │ ⚡ ACHIEVER │ 🔥 BURNOUT   │
WLB    │ PERFORMER  │ AT RISK    │ CANDIDATE   │
75-100 │            │            │             │
───────┼────────────┼────────────┼─────────────┤
MED    │ 😌 BALANCED │ ⚠️ WATCH    │ 🚨 HIGH      │
WLB    │ EMPLOYEE   │ CLOSELY    │ RISK        │
50-74  │            │            │             │
───────┼────────────┼────────────┼─────────────┤
LOW    │ 😴 UNDER-   │ 📉 POOR     │ 💀 CRITICAL  │
WLB    │ PERFORMER  │ PERFORMER  │ STATE       │
0-49   │            │            │             │
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
