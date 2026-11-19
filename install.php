<?php
// ONE-TIME SETUP: creates your admin account. It refuses to run once any user exists.
// >>> DELETE THIS FILE after you have created your admin account. <<<
require __DIR__ . '/includes/functions.php';
$exists = (int)db()->query('SELECT COUNT(*) FROM users')->fetchColumn() > 0;
$msg = ''; $done = false;

if (!$exists && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $u = post('username'); $p = (string)($_POST['password'] ?? '');
    if (!preg_match('/^[A-Za-z0-9_.-]{3,50}$/', $u))  $msg = 'Username: 3–50 letters, numbers, . _ -';
    elseif (strlen($p) < 10)                          $msg = 'Password must be at least 10 characters.';
    else {
        db()->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)')->execute([$u, password_hash($p, PASSWORD_DEFAULT)]);
        $done = true;
    }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex"><title>Install</title><link rel="stylesheet" href="css/admin.css"></head>
<body class="login"><div class="box">
<h1>Portfolio setup</h1>
<?php if ($done): ?>
  <div class="flash ok">Admin account created. Now <b>delete install.php</b>, then <a href="admin/login.php">log in</a>.</div>
<?php elseif ($exists): ?>
  <div class="flash err">Setup is already complete. Delete install.php.</div>
<?php else: ?>
  <?php if ($msg): ?><div class="flash err"><?= e($msg) ?></div><?php endif; ?>
  <form method="post" autocomplete="off"><?= csrf_field() ?>
    <label>Admin username<input name="username" required maxlength="50"></label>
    <label>Admin password (min. 10 chars)<input type="password" name="password" required minlength="10"></label>
    <button class="btn">Create admin</button>
  </form>
<?php endif; ?>
</div></body></html>
