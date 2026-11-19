<?php
require __DIR__ . '/includes/functions.php';
$current = 'home'; $title = 'Home';
require __DIR__ . '/includes/header.php';
$words = e(setting('typing_words', 'Student'));
$first = e(explode(',', setting('typing_words', 'Student'))[0]);
?>
<section class="hero">
  <div>
    <div class="rule"></div>
    <h1>I’m <?= e(setting('site_name')) ?>,<br>a <em><span id="typing" data-words="<?= $words ?>"><?= $first ?></span></em><span class="cursor"></span></h1>
    <p class="lead"><?= nl2br(e(setting('home_intro'))) ?></p>
    <div class="actions">
      <a class="btn" href="contact.php">contact me</a>
      <a class="btn alt" href="projects.php">open project</a>
    </div>
  </div>
  <div class="photo"><div class="ring"><img src="<?= e(setting('portrait', 'images/bascar.jpg')) ?>" alt="Portrait of <?= e(setting('site_name')) ?>"></div></div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
