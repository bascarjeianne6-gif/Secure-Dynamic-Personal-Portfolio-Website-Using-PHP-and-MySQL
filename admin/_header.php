<?php
// Every admin page includes this AFTER functions.php. Blocks guests.
require_admin();
header('Cache-Control: no-store');
$flash = flash_get();
$unread = (int)db()->query('SELECT COUNT(*) FROM messages WHERE is_read = 0')->fetchColumn();
$menu = ['index.php' => 'Dashboard', 'settings.php' => 'Site content', 'skills.php' => 'Skills',
         'projects.php' => 'Projects', 'resume.php' => 'Resume', 'messages.php' => 'Messages' . ($unread ? " ($unread)" : ''),
         'password.php' => 'Password'];
$here = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title><?= e($pageTitle ?? 'Admin') ?> | Admin</title>
<link rel="stylesheet" href="../css/admin.css"></head>
<body>
<aside>
  <h1>Admin</h1>
  <nav><?php foreach ($menu as $f => $l): ?><a href="<?= $f ?>"<?= $f === $here ? ' class="on"' : '' ?>><?= e($l) ?></a><?php endforeach; ?></nav>
  <a href="../index.php" target="_blank" rel="noopener">View site ↗</a>
  <form method="post" action="logout.php"><?= csrf_field() ?><button class="btn ghost">Log out</button></form>
</aside>
<main>
<h2><?= e($pageTitle ?? 'Admin') ?></h2>
<?php if ($flash): ?><div class="flash <?= e($flash[0]) ?>"><?= e($flash[1]) ?></div><?php endif; ?>
