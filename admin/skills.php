<?php
require __DIR__ . '/../includes/functions.php';
$pageTitle = 'Skills';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $id = (int)($_POST['id'] ?? 0);
    if (post('do') === 'delete' && $id) {
        db()->prepare('DELETE FROM skills WHERE id = ?')->execute([$id]);
        flash_set('ok', 'Skill deleted.');
    } else {
        $name = post('name'); $level = filter_var($_POST['level'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 100]]);
        $sort = (int)($_POST['sort_order'] ?? 0);
        if ($name === '' || mb_strlen($name) > 100 || $level === false) {
            flash_set('err', 'Name is required and level must be 0–100.');
        } elseif ($id) {
            db()->prepare('UPDATE skills SET name=?, level=?, sort_order=? WHERE id=?')->execute([$name, $level, $sort, $id]);
            flash_set('ok', 'Skill updated.');
        } else {
            db()->prepare('INSERT INTO skills (name, level, sort_order) VALUES (?,?,?)')->execute([$name, $level, $sort]);
            flash_set('ok', 'Skill added.');
        }
    }
    redirect('skills.php');
}
$edit = null;
if (isset($_GET['edit'])) { $st = db()->prepare('SELECT * FROM skills WHERE id = ?'); $st->execute([(int)$_GET['edit']]); $edit = $st->fetch() ?: null; }
$rows = db()->query('SELECT * FROM skills ORDER BY sort_order, id')->fetchAll();
require __DIR__ . '/_header.php';
?>
<form method="post" class="panel inline">
  <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
  <label>Skill<input name="name" required maxlength="100" value="<?= e($edit['name'] ?? '') ?>"></label>
  <label>Level (0–100)<input name="level" type="number" min="0" max="100" required value="<?= e($edit['level'] ?? 50) ?>"></label>
  <label>Order<input name="sort_order" type="number" value="<?= e($edit['sort_order'] ?? 0) ?>"></label>
  <button class="btn"><?= $edit ? 'Update' : 'Add' ?> skill</button>
  <?php if ($edit): ?><a class="btn ghost" href="skills.php">Cancel</a><?php endif; ?>
</form>
<table><tr><th>Skill</th><th>Level</th><th>Order</th><th></th></tr>
<?php foreach ($rows as $r): ?>
<tr><td><?= e($r['name']) ?></td><td><?= (int)$r['level'] ?>%</td><td><?= (int)$r['sort_order'] ?></td>
<td class="act"><a href="?edit=<?= (int)$r['id'] ?>">Edit</a>
<form method="post" data-confirm="Delete this skill?"><?= csrf_field() ?><input type="hidden" name="do" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="link danger">Delete</button></form></td></tr>
<?php endforeach; ?></table>
<?php require __DIR__ . '/_footer.php'; ?>
