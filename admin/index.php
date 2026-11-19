<?php
require __DIR__ . '/../includes/functions.php';
$pageTitle = 'Dashboard';
require __DIR__ . '/_header.php';
$count = fn(string $t) => (int)db()->query("SELECT COUNT(*) FROM $t")->fetchColumn();   // fixed table names only
?>
<div class="stats">
  <div class="stat"><b><?= $count('skills') ?></b>Skills</div>
  <div class="stat"><b><?= $count('projects') ?></b>Projects</div>
  <div class="stat"><b><?= $count('resume_items') ?></b>Resume items</div>
  <div class="stat"><b><?= $unread ?></b>Unread messages</div>
</div>
<p>Use the menu to edit your portfolio. Changes appear on the public site immediately.</p>
<?php require __DIR__ . '/_footer.php'; ?>
