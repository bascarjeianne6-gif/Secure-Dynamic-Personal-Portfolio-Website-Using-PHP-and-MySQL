<?php
// Expects: $current (nav key), $title (page title)
$nav = ['home' => ['index.php', 'home'], 'about' => ['about.php', 'about'], 'skills' => ['skills.php', 'skills'],
        'projects' => ['projects.php', 'projects'], 'resume' => ['resume.php', 'resume'], 'contact' => ['contact.php', 'contact']];
$siteName = setting('site_name', 'Portfolio');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> | <?= e($siteName) ?></title>
<meta name="description" content="<?= e(setting('meta_description')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="shell">
<header class="top">
  <a class="logo" href="index.php"><i><?= e(setting('initials', 'JB')) ?></i><?= e($siteName) ?></a>
  <nav id="mainNav" aria-label="Main"><ul>
  <?php foreach ($nav as $key => [$href, $label]): ?>
    <li><a href="<?= $href ?>"<?= $key === $current ? ' aria-current="page"' : '' ?>><?= $label ?></a></li>
  <?php endforeach; ?>
  </ul></nav>
  <div class="tools">
    <button id="themeBtn" aria-label="Toggle dark mode">☾</button>
    <a class="btn sm hide-m" href="contact.php">contact</a>
    <button id="menuBtn" aria-label="Open menu" aria-expanded="false">☰</button>
  </div>
</header>
<main>
