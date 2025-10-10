
# WLB Monitoring System

A modern Work-Life Balance (WLB) monitoring web application built with Laravel 12 and PHP 8.3, featuring role-based dashboards, interactive analytics, and advanced attendance, leave, and overtime management.

## Features

- **Role-based Dashboards**: Distinct, modern dashboards for Admin, Manager, and Employee roles.
- **Interactive Analytics**: Animated charts (Chart.js), glassmorphism UI, dark mode, and toast notifications.
- **Attendance Management**: Quick check-in/out, daily status, and attendance reports with filters and export (CSV).
- **Leave Management**: Apply, approve/reject, and report leave with type/status filters and export (CSV).
- **Overtime Management**: Apply, approve/reject, and report overtime with type/status filters and export (CSV).
- **Approvals**: Pending approvals for managers/admins.
- **User Management**: Admin/Manager can manage users.
- **Dark Mode**: Toggle system-wide dark mode.
- **Export**: Download filtered reports as CSV (Excel export coming soon).

## Tech Stack

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
