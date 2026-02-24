# Student Mentoring Management System

Core PHP + MySQL mentoring platform for Admin, Mentor, and Student roles.

## Default Login
- Admin: `admin@college.edu` / `Admin@123`
- Mentor: `mentor1@college.edu` / `Mentor@123`
- Student: `student1@college.edu` / `Student@123`

## Setup (XAMPP)
1. Copy this project to: `xampp/htdocs/mentoring-system`
2. Start **Apache** and **MySQL** in XAMPP.
3. Create DB and seed data:
   ```bash
   mysql -u root < database.sql
   ```
4. (Optional for PDF export) install TCPDF:
   ```bash
   composer install
   ```
5. Open `http://localhost/mentoring-system`

## Features
- Role-based authentication + authorization
- CSRF protection and prepared statements
- Student risk engine with analytics charts
- Mentor assignment and counseling logs
- Excel/PDF/print report exports


## UI Screenshot
![Login UI](assets/screenshots/ui-login.png)


## Where to see the UI output
- Open: `http://localhost/mentoring-system/` (public landing page).
- Click **Open Login Page** then sign in using demo users above.
- Role dashboards:
  - Admin: `/admin/dashboard.php`
  - Mentor: `/mentor/dashboard.php`
  - Student: `/student/dashboard.php`
