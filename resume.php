<?php
require __DIR__ . '/includes/functions.php';
$current = 'resume'; $title = 'Resume';
$sections = ['Education' => [], 'Experience' => [], 'Training & certifications' => []];
foreach (db()->query('SELECT section, title, description FROM resume_items ORDER BY sort_order, id') as $r) {
    $sections[$r['section']][] = $r;
}
require __DIR__ . '/includes/header.php';
?>
<section class="page-head"><div class="rule"></div><h2>Resume &amp; experience</h2>
<p class="sub">Open each section to see the details.</p></section>
<?php $i = 0; foreach ($sections as $name => $items): $open = ($i++ === 0); ?>
<div class="acc"><button aria-expanded="<?= $open ? 'true' : 'false' ?>"><?= e($name) ?></button>
  <div class="panel"<?= $open ? '' : ' hidden' ?>>
  <?php foreach ($items as $it): ?><h4><?= e($it['title']) ?></h4><p><?= nl2br(e($it['description'])) ?></p><?php endforeach; ?>
  <?php if (!$items): ?><p>Nothing added yet.</p><?php endif; ?>
  </div></div>
<?php endforeach; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
