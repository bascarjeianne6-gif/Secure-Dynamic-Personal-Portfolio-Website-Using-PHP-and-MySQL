<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

/* ---------- Bootstrap: errors, headers, session ---------- */
set_exception_handler(function (Throwable $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo APP_DEBUG ? '<pre>' . htmlspecialchars((string)$e) . '</pre>'
                   : '<h1>Something went wrong.</h1><p>Please try again later.</p>';
    exit;
});

function security_headers(): void
{
    header_remove('X-Powered-By');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; "
         . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; "
         . "font-src https://fonts.gstatic.com; script-src 'self'; "
         . "form-action 'self'; frame-ancestors 'none'; base-uri 'self'");
}

function secure_session_start(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) return;
    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name('PORTFOLIOSESS');
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => $https,
                               'httponly' => true, 'samesite' => 'Lax']);
    session_start();
    if (isset($_SESSION['last']) && time() - $_SESSION['last'] > SESSION_IDLE) {
        session_unset(); session_destroy(); session_start();
    }
    $_SESSION['last'] = time();
}

security_headers();
secure_session_start();

/* ---------- Output / input helpers ---------- */
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

function post(string $k): string { return trim((string)($_POST[$k] ?? '')); }

function lines(string $text): array
{
    return array_values(array_filter(array_map('trim', preg_split('/\R/', $text))));
}

function slugify(string $s): string
{
    $s = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $s), '-'));
    return $s !== '' ? $s : 'other';
}

function redirect(string $url): never { header('Location: ' . $url); exit; }

function client_ip(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
}

/* ---------- Site settings (key/value table) ---------- */
function setting(string $key, string $default = ''): string
{
    static $all = null;
    if ($all === null) {
        $all = [];
        foreach (db()->query('SELECT skey, svalue FROM settings') as $r) $all[$r['skey']] = $r['svalue'];
    }
    return $all[$key] ?? $default;
}

function set_setting(string $key, string $value): void
{
    $st = db()->prepare('INSERT INTO settings (skey, svalue) VALUES (?, ?)
                         ON DUPLICATE KEY UPDATE svalue = VALUES(svalue)');
    $st->execute([$key, $value]);
}

/* ---------- CSRF ---------- */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">'; }
function csrf_verify(): void
{
    $sent = (string)($_POST['csrf'] ?? '');
    if (!hash_equals($_SESSION['csrf'] ?? '', $sent)) {
        http_response_code(419);
        exit('Invalid or expired form. Go back, refresh the page and try again.');
    }
}

/* ---------- Flash messages ---------- */
function flash_set(string $type, string $msg): void { $_SESSION['flash'] = [$type, $msg]; }
function flash_get(): ?array { $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f; }

/* ---------- Admin auth ---------- */
function is_admin(): bool { return !empty($_SESSION['uid']); }
function require_admin(): void { if (!is_admin()) redirect('login.php'); }

/* ---------- Safe image upload ---------- */
function upload_image(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if ($file['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Upload failed.');
    if ($file['size'] > MAX_UPLOAD) throw new RuntimeException('Image must be 2 MB or smaller.');
    if (!is_uploaded_file($file['tmp_name'])) throw new RuntimeException('Invalid upload.');

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($allowed[$mime]) || @getimagesize($file['tmp_name']) === false) {
        throw new RuntimeException('Only JPG, PNG, WEBP or GIF images are allowed.');
    }
    $name = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];   // never trust the original name
    $dir  = __DIR__ . '/../images/uploads/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    if (!move_uploaded_file($file['tmp_name'], $dir . $name)) throw new RuntimeException('Could not save image.');
    return 'images/uploads/' . $name;
}

function delete_upload(?string $path): void
{
    if ($path && preg_match('#^images/uploads/[a-f0-9]{32}\.(jpg|png|webp|gif)$#', $path)) {
        @unlink(__DIR__ . '/../' . $path);
    }
}
