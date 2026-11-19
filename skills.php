<?php
require __DIR__ . '/includes/functions.php';
$current = 'skills'; $title = 'Skills';
$skills = db()->query('SELECT name, level FROM skills ORDER BY sort_order, id')->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<section class="page-head"><div class="rule"></div><h2>Skills</h2>
<p class="sub"><?= e(setting('skills_sub')) ?></p></section>
<div class="grid">
<?php foreach ($skills as $s): $lvl = max(0, min(100, (int)$s['level'])); ?>
  <div class="card skill"><div class="row"><span><?= e($s['name']) ?></span><span><?= $lvl ?>%</span></div>
    <div class="bar" role="progressbar" aria-valuenow="<?= $lvl ?>" aria-valuemin="0" aria-valuemax="100" aria-label="<?= e($s['name']) ?>"><span data-level="<?= $lvl ?>"></span></div></div>
<?php endforeach; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
