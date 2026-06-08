# BPKMCH Nursing College — Website

Clean, responsive website for **BPKMCH Nursing College, Cancer Gate, Bharatpur**.
Built with **PHP + MySQL + Bootstrap 5 + jQuery (AJAX)**.

## Tech Stack
- PHP 7.4+ (works with 8.x)
- MySQL / MariaDB
- Bootstrap 5.3 + Bootstrap Icons (CDN)
- jQuery 3.7 (CDN) for AJAX contact form
- Google Fonts: Poppins + Merriweather

## Setup (XAMPP / WAMP / LAMP)

1. **Copy the project** into your web root:
   - XAMPP (Windows): `C:\xampp\htdocs\bpkmch-nursing\`
   - WAMP: `C:\wamp64\www\bpkmch-nursing\`
   - LAMP (Linux): `/var/www/html/bpkmch-nursing/`

2. **Start Apache and MySQL** from the XAMPP/WAMP control panel.

3. **Create the database**:
   - Open `http://localhost/phpmyadmin`
   - Click **Import** and upload `sql/database.sql`
   - Or run from terminal:
     ```bash
     mysql -u root -p < sql/database.sql
     ```

4. **Configure DB credentials** in `includes/db.php` if your MySQL user/password is different.
   Defaults: host=`localhost`, user=`root`, password=`` (empty), db=`bpkmch_nursing`.

5. **Open in browser**: `http://localhost/bpkmch-nursing/`

## Pages

| URL | Page |
|---|---|
| `index.php` | Home (hero, stats, programs, notices, CTA) |
| `about.php` | About / vision / mission |
| `programs.php` | All academic programs (from DB) |
| `faculty.php` | Faculty list (from DB) |
| `admissions.php` | Admission process + eligibility |
| `gallery.php` | Photo gallery |
| `notices.php` | Notices/news (from DB) |
| `contact.php` | Contact form (AJAX) + map |

## AJAX Contact Form

- Frontend: `assets/js/main.js` (jQuery `$.ajax`)
- Endpoint: `ajax/contact_submit.php`
- Stores messages in `contact_messages` table.
- Validation on both client (HTML5 `required`) and server (PHP).
- Uses prepared statements (`mysqli_stmt`) to prevent SQL injection.

## Customizing

- **Colors / fonts**: edit CSS variables at the top of `assets/css/style.css`.
- **Header/footer**: `includes/header.php`, `includes/footer.php` (shared).
- **Add notice/program/faculty**: insert into respective MySQL table (or extend with an admin panel).

## File Structure
```
bpkmch-nursing/
├── index.php
├── about.php
├── programs.php
├── faculty.php
├── admissions.php
├── gallery.php
├── notices.php
├── contact.php
├── includes/
│   ├── db.php
│   ├── header.php
│   └── footer.php
├── ajax/
│   └── contact_submit.php
├── assets/
│   ├── css/style.css
│   ├── js/main.js
│   └── img/
├── sql/
│   └── database.sql
└── README.md
```
