<?php
require __DIR__ . '/../includes/functions.php';
$pageTitle = 'Projects';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $id = (int)($_POST['id'] ?? 0);
    try {
        $old = null;
        if ($id) { $st = db()->prepare('SELECT image FROM projects WHERE id = ?'); $st->execute([$id]); $old = $st->fetch(); }

        if (post('do') === 'delete' && $id) {
            delete_upload($old['image'] ?? null);
            db()->prepare('DELETE FROM projects WHERE id = ?')->execute([$id]);
            flash_set('ok', 'Project deleted.');
            redirect('projects.php');
        }
        $title = post('title'); $cat = post('category'); $desc = post('description');
        $tags = mb_substr(post('tags'), 0, 255); $alt = mb_substr(post('image_alt'), 0, 255); $sort = (int)($_POST['sort_order'] ?? 0);
        if ($title === '' || $cat === '' || $desc === '' || mb_strlen($title) > 120 || mb_strlen($cat) > 60) {
            throw new RuntimeException('Title, category and description are required.');
        }
        $img = upload_image($_FILES['image'] ?? []);
        if ($id) {
            if ($img) { delete_upload($old['image'] ?? null); }
            db()->prepare('UPDATE projects SET title=?, category=?, description=?, tags=?, image=?, image_alt=?, sort_order=? WHERE id=?')
                ->execute([$title, $cat, $desc, $tags, $img ?? ($old['image'] ?? ''), $alt, $sort, $id]);
            flash_set('ok', 'Project updated.');
        } else {
            db()->prepare('INSERT INTO projects (title, category, description, tags, image, image_alt, sort_order) VALUES (?,?,?,?,?,?,?)')
                ->execute([$title, $cat, $desc, $tags, $img ?? '', $alt, $sort]);
            flash_set('ok', 'Project added.');
        }
    } catch (RuntimeException $e) { flash_set('err', $e->getMessage()); }
    redirect('projects.php');
}
$edit = null;
if (isset($_GET['edit'])) { $st = db()->prepare('SELECT * FROM projects WHERE id = ?'); $st->execute([(int)$_GET['edit']]); $edit = $st->fetch() ?: null; }
$rows = db()->query('SELECT * FROM projects ORDER BY sort_order, id')->fetchAll();
require __DIR__ . '/_header.php';
?>
<form method="post" enctype="multipart/form-data" class="panel">
  <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
  <label>Title<input name="title" required maxlength="120" value="<?= e($edit['title'] ?? '') ?>"></label>
  <label>Category (becomes a filter button, e.g. Cisco)<input name="category" required maxlength="60" value="<?= e($edit['category'] ?? '') ?>"></label>
  <label>Description<textarea name="description" rows="5" required><?= e($edit['description'] ?? '') ?></textarea></label>
  <label>Tags (comma separated)<input name="tags" maxlength="255" value="<?= e($edit['tags'] ?? '') ?>"></label>
  <label>Image (JPG/PNG/WEBP/GIF, max 2 MB)<input type="file" name="image" accept="image/*"></label>
  <?php if (!empty($edit['image'])): ?><img class="thumb" src="../<?= e($edit['image']) ?>" alt=""><?php endif; ?>
  <label>Image description (alt text)<input name="image_alt" maxlength="255" value="<?= e($edit['image_alt'] ?? '') ?>"></label>
  <label>Order<input name="sort_order" type="number" value="<?= e($edit['sort_order'] ?? 0) ?>"></label>
  <div><button class="btn"><?= $edit ? 'Update' : 'Add' ?> project</button>
  <?php if ($edit): ?><a class="btn ghost" href="projects.php">Cancel</a><?php endif; ?></div>
</form>
<table><tr><th>Title</th><th>Category</th><th>Order</th><th></th></tr>
<?php foreach ($rows as $r): ?>
<tr><td><?= e($r['title']) ?></td><td><?= e($r['category']) ?></td><td><?= (int)$r['sort_order'] ?></td>
<td class="act"><a href="?edit=<?= (int)$r['id'] ?>">Edit</a>
<form method="post" data-confirm="Delete this project?"><?= csrf_field() ?><input type="hidden" name="do" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="link danger">Delete</button></form></td></tr>
<?php endforeach; ?></table>
<?php require __DIR__ . '/_footer.php'; ?>
