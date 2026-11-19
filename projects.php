<?php
require __DIR__ . '/includes/functions.php';
$current = 'projects'; $title = 'Projects'; $withModal = true;
$projects = db()->query('SELECT * FROM projects ORDER BY sort_order, id')->fetchAll();
$cats = [];
foreach ($projects as $p) $cats[slugify($p['category'])] = $p['category'];
require __DIR__ . '/includes/header.php';
?>
<section class="page-head"><div class="rule"></div><h2>Projects</h2>
<p class="sub"><?= e(setting('projects_sub')) ?></p></section>
<div class="filters" role="group" aria-label="Filter projects">
  <button class="on" data-filter="all">All</button>
  <?php foreach ($cats as $slug => $label): ?><button data-filter="<?= e($slug) ?>"><?= e($label) ?></button><?php endforeach; ?>
</div>
<div class="grid">
<?php foreach ($projects as $p): ?>
  <article class="card proj" data-cat="<?= e(slugify($p['category'])) ?>">
    <?php if ($p['image'] !== ''): ?><img src="<?= e($p['image']) ?>" alt="<?= e($p['image_alt']) ?>" loading="lazy"><?php endif; ?>
    <h3><?= e($p['title']) ?></h3>
    <p><?= nl2br(e($p['description'])) ?></p>
    <div class="tags"><?php foreach (array_filter(array_map('trim', explode(',', $p['tags']))) as $t): ?><span><?= e($t) ?></span><?php endforeach; ?></div>
  </article>
<?php endforeach; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
