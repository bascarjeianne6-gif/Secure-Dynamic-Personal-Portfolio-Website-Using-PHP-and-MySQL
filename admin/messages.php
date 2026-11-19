<?php
require __DIR__ . '/../includes/functions.php';
$pageTitle = 'Messages';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $id = (int)($_POST['id'] ?? 0);
    if (post('do') === 'delete') db()->prepare('DELETE FROM messages WHERE id = ?')->execute([$id]);
    if (post('do') === 'toggle') db()->prepare('UPDATE messages SET is_read = 1 - is_read WHERE id = ?')->execute([$id]);
    redirect('messages.php');
}
$rows = db()->query('SELECT * FROM messages ORDER BY created_at DESC, id DESC LIMIT 200')->fetchAll();
require __DIR__ . '/_header.php';
?>
<?php if (!$rows): ?><p>No messages yet.</p><?php endif; ?>
<?php foreach ($rows as $m): ?>
<article class="panel msg<?= $m['is_read'] ? '' : ' new' ?>">
  <header><b><?= e($m['subject']) ?></b><small><?= e($m['created_at']) ?></small></header>
  <p class="meta">From <?= e($m['name']) ?> &lt;<a href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a>&gt;</p>
  <p><?= nl2br(e($m['message'])) ?></p>
  <div class="act">
    <form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$m['id'] ?>"><input type="hidden" name="do" value="toggle"><button class="link"><?= $m['is_read'] ? 'Mark unread' : 'Mark read' ?></button></form>
    <form method="post" data-confirm="Delete this message?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$m['id'] ?>"><input type="hidden" name="do" value="delete"><button class="link danger">Delete</button></form>
  </div>
</article>
<?php endforeach; ?>
<?php require __DIR__ . '/_footer.php'; ?>
