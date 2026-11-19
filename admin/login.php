<?php
require __DIR__ . '/../includes/functions.php';
if (is_admin()) redirect('index.php');
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $ip = client_ip();
    db()->exec("DELETE FROM login_attempts WHERE attempted_at < (NOW() - INTERVAL 1 DAY)");
    $st = db()->prepare('SELECT COUNT(*) FROM login_attempts WHERE ip = ? AND attempted_at > (NOW() - INTERVAL ' . (int)LOGIN_WINDOW . ' MINUTE)');
    $st->execute([$ip]);

    if ((int)$st->fetchColumn() >= MAX_LOGIN_FAILS) {
        $error = 'Too many failed attempts. Try again in ' . LOGIN_WINDOW . ' minutes.';
    } else {
        $st = db()->prepare('SELECT id, password_hash FROM users WHERE username = ?');
        $st->execute([post('username')]);
        $u = $st->fetch();
        // verify against a dummy hash when the user doesn't exist (keeps timing similar)
        $ok = password_verify((string)($_POST['password'] ?? ''), $u['password_hash'] ?? password_hash('dummy', PASSWORD_DEFAULT));
        if ($u && $ok) {
            session_regenerate_id(true);                 // prevents session fixation
            $_SESSION['uid'] = (int)$u['id'];
            db()->prepare('DELETE FROM login_attempts WHERE ip = ?')->execute([$ip]);
            if (password_needs_rehash($u['password_hash'], PASSWORD_DEFAULT)) {
                db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
                    ->execute([password_hash((string)$_POST['password'], PASSWORD_DEFAULT), $u['id']]);
            }
            redirect('index.php');
        }
        db()->prepare('INSERT INTO login_attempts (ip) VALUES (?)')->execute([$ip]);
        $error = 'Invalid username or password.';        // same message for both cases
    }
}
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow"><title>Admin login</title><link rel="stylesheet" href="../css/admin.css"></head>
<body class="login">
<form method="post" class="box" autocomplete="off">
  <h1>Admin login</h1>
  <?php if ($error): ?><div class="flash err"><?= e($error) ?></div><?php endif; ?>
  <?= csrf_field() ?>
  <label>Username<input name="username" required maxlength="50" autofocus></label>
  <label>Password<input name="password" type="password" required></label>
  <button class="btn">Log in</button>
</form>
</body></html>
