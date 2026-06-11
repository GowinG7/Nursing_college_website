# BPKMCH Admin Panel — v2 (with Gallery & Image Uploads)

## What's new in v2
- **Gallery** module — upload, list, delete images with title + caption.
- **Faculty** — add a profile photo for each member.
- **Notices** — attach an image to a notice.
- Auto file cleanup when records are deleted.

## Install / Upgrade
1. Copy the `admin/` folder into your project root (overwrite the previous one).
2. Run `sql/admin.sql` in phpMyAdmin (safe to re-run; uses `IF NOT EXISTS`).
3. Create an `uploads/` folder in the project root and make it writable:
   ```
   mkdir uploads
   chmod -R 775 uploads
   ```
   Subfolders (`uploads/faculty`, `uploads/notices`, `uploads/gallery`) are created automatically on first upload.

## Image Rules
- Allowed: jpg, jpeg, png, gif, webp
- Max size: 5 MB
- Stored as `uploads/{type}/{unique-name}.{ext}` and referenced from the DB.

## Show images on the public site
Use the path stored in the DB (relative to project root):
```php
<img src="<?= htmlspecialchars($row['image']) ?>" alt="">
```
For faculty/notices, the column is `image`. For gallery, it's also `image`.

## Default Login
- URL: `admin/login.php`
- Username: `admin`
- Password: `admin123`  ← **change immediately**
