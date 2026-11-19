<?php
require __DIR__ . '/includes/functions.php';
$current = 'contact'; $title = 'Contact';
$extraScripts = ['js/contact.js'];
$old = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];
$err = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    foreach ($old as $k => $_) $old[$k] = post($k);

    if (post('website') !== '') redirect('contact.php');          // honeypot: bots fill this in

    $len = fn(string $s) => mb_strlen($s, 'UTF-8');
    if ($len($old['name']) < 2 || $len($old['name']) > 100)       $err['name'] = 'Please enter your name (2–100 characters).';
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL) || $len($old['email']) > 150) $err['email'] = 'Please enter a valid email address.';
    if ($len($old['subject']) < 3 || $len($old['subject']) > 150) $err['subject'] = 'Please enter a subject (3–150 characters).';
    if ($len($old['message']) < 10 || $len($old['message']) > 2000) $err['message'] = 'Message must be 10–2000 characters.';

    if (!$err) {
        $st = db()->prepare('SELECT COUNT(*) FROM messages WHERE ip = ? AND created_at > (NOW() - INTERVAL 1 HOUR)');
        $st->execute([client_ip()]);
        if ((int)$st->fetchColumn() >= MAX_MSG_PER_HR) {
            $err['message'] = 'Too many messages from your connection. Please try again later.';
        }
    }
    if (!$err) {
        db()->prepare('INSERT INTO messages (name, email, subject, message, ip) VALUES (?,?,?,?,?)')
            ->execute([$old['name'], $old['email'], $old['subject'], $old['message'], client_ip()]);
        flash_set('ok', 'Thank you! Your message has been sent. I’ll reply within two days.');
        redirect('contact.php');
    }
}
$flash = flash_get();
require __DIR__ . '/includes/header.php';
?>
<section class="page-head"><div class="rule"></div><h2>Let’s work together</h2>
<p class="sub">Send a message and I’ll reply within two days.</p></section>
<div class="two">
  <div class="card"><h3>Find me online</h3>
    <ul class="links">
      <li>Email: <a href="mailto:<?= e(setting('contact_email')) ?>"><?= e(setting('contact_email')) ?></a></li>
    </ul><p>Location: <?= e(setting('contact_location')) ?></p></div>
  <form id="contactForm" class="card" method="post" action="contact.php" novalidate>
    <?= csrf_field() ?>
    <div style="position:absolute;left:-9999px" aria-hidden="true"><label>Leave empty<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
    <?php foreach (['name' => 'Name', 'email' => 'Email', 'subject' => 'Subject'] as $f => $label): ?>
      <label for="<?= $f ?>"><?= $label ?></label>
      <input id="<?= $f ?>" name="<?= $f ?>" type="<?= $f === 'email' ? 'email' : 'text' ?>" maxlength="150" value="<?= e($old[$f]) ?>" required>
      <div class="err" id="err-<?= $f ?>" aria-live="polite"><?= e($err[$f] ?? '') ?></div>
    <?php endforeach; ?>
    <label for="message">Message</label>
    <textarea id="message" name="message" rows="5" maxlength="2000" required><?= e($old['message']) ?></textarea>
    <div class="err" id="err-message" aria-live="polite"><?= e($err['message'] ?? '') ?></div>
    <button class="btn" type="submit" style="margin-top:14px">send message</button>
    <p id="ok" role="status"><?= $flash ? e($flash[1]) : '' ?></p>
  </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
