<?php
require __DIR__ . '/../includes/functions.php';
$pageTitle = 'Resume';
require_admin();
$sections = ['Education', 'Experience', 'Training & certifications'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $id = (int)($_POST['id'] ?? 0);
    if (post('do') === 'delete' && $id) {
        db()->prepare('DELETE FROM resume_items WHERE id = ?')->execute([$id]);
        flash_set('ok', 'Item deleted.');
    } else {
        $sec = post('section'); $t = post('title'); $d = post('description'); $sort = (int)($_POST['sort_order'] ?? 0);
        if (!in_array($sec, $sections, true) || $t === '' || mb_strlen($t) > 150 || $d === '') {
            flash_set('err', 'Choose a section and fill in the title and details.');
        } elseif ($id) {
            db()->prepare('UPDATE resume_items SET section=?, title=?, description=?, sort_order=? WHERE id=?')->execute([$sec, $t, $d, $sort, $id]);
            flash_set('ok', 'Item updated.');
        } else {
            db()->prepare('INSERT INTO resume_items (section, title, description, sort_order) VALUES (?,?,?,?)')->execute([$sec, $t, $d, $sort]);
            flash_set('ok', 'Item added.');
        }
    }
    redirect('resume.php');
}
$edit = null;
if (isset($_GET['edit'])) { $st = db()->prepare('SELECT * FROM resume_items WHERE id = ?'); $st->execute([(int)$_GET['edit']]); $edit = $st->fetch() ?: null; }
$rows = db()->query('SELECT * FROM resume_items ORDER BY section, sort_order, id')->fetchAll();
require __DIR__ . '/_header.php';
?>
<form method="post" class="panel">
  <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
  <label>Section<select name="section"><?php foreach ($sections as $s): ?><option<?= ($edit['section'] ?? '') === $s ? ' selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?></select></label>
  <label>Title<input name="title" required maxlength="150" value="<?= e($edit['title'] ?? '') ?>"></label>
  <label>Details<textarea name="description" rows="4" required><?= e($edit['description'] ?? '') ?></textarea></label>
  <label>Order<input name="sort_order" type="number" value="<?= e($edit['sort_order'] ?? 0) ?>"></label>
  <div><button class="btn"><?= $edit ? 'Update' : 'Add' ?> item</button>
  <?php if ($edit): ?><a class="btn ghost" href="resume.php">Cancel</a><?php endif; ?></div>
</form>
<table><tr><th>Section</th><th>Title</th><th>Order</th><th></th></tr>
<?php foreach ($rows as $r): ?>
<tr><td><?= e($r['section']) ?></td><td><?= e($r['title']) ?></td><td><?= (int)$r['sort_order'] ?></td>
<td class="act"><a href="?edit=<?= (int)$r['id'] ?>">Edit</a>
<form method="post" data-confirm="Delete this item?"><?= csrf_field() ?><input type="hidden" name="do" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="link danger">Delete</button></form></td></tr>
<?php endforeach; ?></table>
<?php require __DIR__ . '/_footer.php'; ?>
