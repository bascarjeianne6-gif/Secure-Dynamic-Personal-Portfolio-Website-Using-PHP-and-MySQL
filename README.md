# Secure Dynamic Personal Portfolio – PHP + MySQL

Your static HTML portfolio converted into a database-driven site with a secure admin panel.
Requires **PHP 8.1+** and **MySQL/MariaDB** (XAMPP, Laragon or WAMP all work).

## 1. Put your existing assets in place
This package contains only the new code. Copy **your own files** into it:

    css/style.css      <- your existing stylesheet (unchanged)
    js/main.js         <- your existing script (unchanged)
    images/*.png/jpg   <- Cisco.png, animation.png, seminar.png, bascar.jpg ...

Final structure:

    portfolio/
    ├── index.php about.php skills.php projects.php resume.php contact.php
    ├── install.php            (run once, then DELETE)
    ├── database.sql
    ├── config/   config.php db.php
    ├── includes/ functions.php header.php footer.php
    ├── admin/    login, index, settings, skills, projects, resume, messages, password, logout
    ├── css/      style.css (yours) + admin.css
    ├── js/       main.js (yours) + contact.js + admin.js
    └── images/   your images + uploads/

## 2. Run it in VS Code + XAMPP
1. Copy the `portfolio` folder to `C:\xampp\htdocs\portfolio` and open it in VS Code.
2. Start **Apache** and **MySQL** in the XAMPP control panel.
3. Open http://localhost/phpmyadmin → **Import** → choose `database.sql` → Go.
4. Check `config/config.php` (default XAMPP: user `root`, empty password).
5. Visit http://localhost/portfolio/install.php, create your admin username + password.
6. **Delete `install.php`.**
7. Log in at http://localhost/portfolio/admin/login.php and edit your content.

Public site: http://localhost/portfolio/

## Security features
| Threat | Protection |
|---|---|
| SQL injection | PDO prepared statements, emulation off |
| XSS | Every output escaped with `e()`; strict Content-Security-Policy (no inline scripts) |
| CSRF | Per-session token on every POST form, checked with `hash_equals` |
| Password theft | `password_hash` (bcrypt), rehash on login, min. 10 chars |
| Brute force | Max 5 failed logins / 15 min / IP |
| Session hijack/fixation | HttpOnly + SameSite cookie, `session_regenerate_id`, 30 min idle timeout, POST-only logout |
| Malicious uploads | Size limit, real MIME check + `getimagesize`, random filenames, PHP execution disabled in `images/uploads` |
| Contact spam | Honeypot field, server-side validation, 5 msgs / hour / IP |
| Info leaks | Errors logged not shown, config/includes blocked, directory listing off, `noindex` admin |
| Clickjacking etc. | `X-Frame-Options`, `nosniff`, `Referrer-Policy`, `Permissions-Policy` |

## Before going live
- Set `APP_DEBUG` to `false` (already default) and use a **non-root MySQL user with a strong password**.
- Use HTTPS, then uncomment the redirect/HSTS lines in `.htaccess`.
- Make sure `install.php` is deleted.
- Replace the placeholder text (e.g. "Your High School") in Admin → Resume.

## What changed from your static files
- Content now comes from MySQL; each `.html` page became a `.php` page with identical markup/classes, so your CSS and JS keep working.
- Fixed typos: leftover "John Moore" (title, alt text, mailto), broken HTML in resume.html, "Explor".
- Project filter buttons are generated from the project categories.
- The contact form saves to the `messages` table (view in Admin → Messages). `js/contact.js` stops `main.js` from swallowing the submit.
