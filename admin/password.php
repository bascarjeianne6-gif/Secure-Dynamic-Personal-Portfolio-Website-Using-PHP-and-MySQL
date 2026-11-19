<?php
require __DIR__ . '/../includes/functions.php';
$pageTitle = 'Change password';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $cur = (string)($_POST['current'] ?? ''); $new = (string)($_POST['new'] ?? ''); $conf = (string)($_POST['confirm'] ?? '');
    $st = db()->prepare('SELECT password_hash FROM users WHERE id = ?'); $st->execute([$_SESSION['uid']]);
    $hash = $st->fetchColumn();
    if (!$hash || !password_verify($cur, $hash))        flash_set('err', 'Current password is incorrect.');
    elseif (strlen($new) < 10)                           flash_set('err', 'New password must be at least 10 characters.');
    elseif (!hash_equals($new, $conf))                   flash_set('err', 'Passwords do not match.');
    else {
        db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([password_hash($new, PASSWORD_DEFAULT), $_SESSION['uid']]);
        session_regenerate_id(true);
        flash_set('ok', 'Password changed.');
    }
    redirect('password.php');
}
require __DIR__ . '/_header.php';
?>
<form method="post" class="panel narrow" autocomplete="off">
  <?= csrf_field() ?>
  <label>Current password<input type="password" name="current" required></label>
  <label>New password (min. 10 characters)<input type="password" name="new" required minlength="10"></label>
  <label>Confirm new password<input type="password" name="confirm" required minlength="10"></label>
  <button class="btn">Change password</button>
</form>
<?php require __DIR__ . '/_footer.php'; ?>
