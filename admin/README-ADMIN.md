# BPKMCH Admin Dashboard

## Installation

1. Copy the `admin/` folder into your existing `bpkmch-nursing/` project root (alongside `index.php`).
2. Copy `sql/admin.sql` into your `sql/` folder.
3. Import the admin SQL into MySQL (via phpMyAdmin or CLI):
   ```
   mysql -u root bpkmch_nursing < sql/admin.sql
   ```
4. Open: `http://localhost/bpkmch-nursing/admin/login.php`

## Default Credentials
- **Username:** `admin`
- **Password:** `admin123`

> Change the password immediately after first login by re-hashing in MySQL:
> ```sql
> UPDATE admins SET password = '<new bcrypt hash>' WHERE username='admin';
> ```
> Generate a hash with: `php -r "echo password_hash('yourpass', PASSWORD_DEFAULT);"`

## Features
- **Dashboard** with live stats (programs, faculty, notices, messages)
- **Programs CRUD** (add / edit / delete)
- **Faculty CRUD**
- **Notices CRUD**
- **Contact messages** inbox with reply + delete
- **AJAX delete** (no page reload) via jQuery
- **Secure**: prepared statements, session auth, password hashing (bcrypt)
- **Responsive** green-themed admin UI matching the main site

## File Structure
```
admin/
├── assets/
│   ├── admin.css
│   └── admin.js
├── ajax/
│   └── delete.php
├── includes/
│   ├── auth.php
│   ├── header.php
│   └── footer.php
├── login.php
├── logout.php
├── dashboard.php
├── programs.php
├── faculty.php
├── notices.php
└── messages.php
sql/
└── admin.sql
```
