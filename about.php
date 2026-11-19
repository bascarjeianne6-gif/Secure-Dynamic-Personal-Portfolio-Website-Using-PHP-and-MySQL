<?php
require __DIR__ . '/includes/functions.php';
$current = 'about'; $title = 'About';
require __DIR__ . '/includes/header.php';
?>
<section class="page-head"><div class="rule"></div><h2>About me</h2>
<p class="sub"><?= e(setting('about_sub')) ?></p></section>
<div class="grid">
  <article class="card"><h3>Background</h3><p><?= nl2br(e(setting('about_background'))) ?></p></article>
  <article class="card"><h3>Education</h3><p><?= nl2br(e(setting('about_education'))) ?></p></article>
  <article class="card"><h3>Goals</h3><ul><?php foreach (lines(setting('about_goals')) as $l): ?><li><?= e($l) ?></li><?php endforeach; ?></ul></article>
  <article class="card"><h3>Interests</h3><ul><?php foreach (lines(setting('about_interests')) as $l): ?><li><?= e($l) ?></li><?php endforeach; ?></ul></article>
</div>
<p style="margin-top:32px"><a class="btn" href="resume.php">view my resume</a></p>
<?php require __DIR__ . '/includes/footer.php'; ?>
