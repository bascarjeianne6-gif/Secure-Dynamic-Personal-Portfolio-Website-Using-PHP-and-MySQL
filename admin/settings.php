<?php
require __DIR__ . '/../includes/functions.php';
$pageTitle = 'Site content';
require_admin();

$fields = [   // key => [label, type]
  'site_name' => ['Full name', 'text'], 'initials' => ['Initials (logo)', 'text'],
  'meta_description' => ['SEO description', 'text'], 'typing_words' => ['Typing words (comma separated)', 'text'],
  'home_intro' => ['Home intro', 'area'], 'about_sub' => ['About – subtitle', 'text'],
  'about_background' => ['About – Background', 'area'], 'about_education' => ['About – Education', 'area'],
  'about_goals' => ['About – Goals (one per line)', 'area'], 'about_interests' => ['About – Interests (one per line)', 'area'],
  'skills_sub' => ['Skills – subtitle', 'text'], 'projects_sub' => ['Projects – subtitle', 'text'],
  'contact_email' => ['Contact email', 'text'], 'contact_location' => ['Location', 'text'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    try {
        if (!filter_var(post('contact_email'), FILTER_VALIDATE_EMAIL)) throw new RuntimeException('Contact email is not valid.');
        $new = upload_image($_FILES['portrait'] ?? []);          // validate upload before saving anything
        foreach ($fields as $k => $_) set_setting($k, mb_substr(post($k), 0, 3000));
        if ($new) { delete_upload(setting('portrait')); set_setting('portrait', $new); }
        flash_set('ok', 'Site content saved.');
    } catch (RuntimeException $e) { flash_set('err', $e->getMessage()); }
    redirect('settings.php');
}
require __DIR__ . '/_header.php';
?>
<form method="post" enctype="multipart/form-data" class="panel">
  <?= csrf_field() ?>
  <?php foreach ($fields as $k => [$label, $type]): ?>
    <label><?= e($label) ?>
    <?php if ($type === 'area'): ?><textarea name="<?= $k ?>" rows="4" maxlength="3000"><?= e(setting($k)) ?></textarea>
    <?php else: ?><input name="<?= $k ?>" value="<?= e(setting($k)) ?>" maxlength="300"><?php endif; ?></label>
  <?php endforeach; ?>
  <label>Portrait photo (JPG/PNG/WEBP, max 2 MB)
    <input type="file" name="portrait" accept="image/jpeg,image/png,image/webp"></label>
  <img src="../<?= e(setting('portrait')) ?>" alt="Current portrait" class="thumb">
  <button class="btn">Save</button>
</form>
<?php require __DIR__ . '/_footer.php'; ?>
