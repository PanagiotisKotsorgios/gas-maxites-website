<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();
$page_title = 'Gallery — Μαχητές Admin';
$active = 'gallery';
$pdo = db();
$flash = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $op = $_POST['op'] ?? '';
    try {
        if ($op === 'upload') {
            if (!is_dir(UPLOAD_DIR_GALLERY)) mkdir(UPLOAD_DIR_GALLERY, 0775, true);
            $count = 0;
            $files = $_FILES['images'] ?? null;
            if ($files && is_array($files['name'])) {
                for ($i = 0; $i < count($files['name']); $i++) {
                    if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
                    if ($files['size'][$i] > MAX_UPLOAD_BYTES) continue;
                    $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                    if (!in_array($ext, ['jpg','jpeg','png','webp'], true)) continue;
                    $mime = mime_content_type($files['tmp_name'][$i]);
                    if (!in_array($mime, ['image/jpeg','image/png','image/webp'], true)) continue;
                    $name = 'g-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
                    if (move_uploaded_file($files['tmp_name'][$i], UPLOAD_DIR_GALLERY . '/' . $name)) {
                        $pdo->prepare('INSERT INTO gallery (filename, sort_order) VALUES (?, 0)')->execute([$name]);
                        $count++;
                    }
                }
            }
            $flash = "Ανέβηκαν $count εικόνες.";
        }
        if ($op === 'update_caption') {
            $pdo->prepare('UPDATE gallery SET caption = ? WHERE id = ?')
                ->execute([trim($_POST['caption'] ?? ''), (int)$_POST['id']]);
            $flash = 'Η λεζάντα ενημερώθηκε.';
        }
        if ($op === 'reorder') {
            $ids = $_POST['order'] ?? [];
            $pos = 0;
            foreach ($ids as $id) {
                $pdo->prepare('UPDATE gallery SET sort_order = ? WHERE id = ?')->execute([$pos++, (int)$id]);
            }
            $flash = 'Η σειρά αποθηκεύτηκε.';
        }
        if ($op === 'delete') {
            $id = (int)$_POST['id'];
            $row = $pdo->prepare('SELECT filename FROM gallery WHERE id = ?');
            $row->execute([$id]); $r = $row->fetch();
            if ($r) {
                @unlink(UPLOAD_DIR_GALLERY . '/' . $r['filename']);
                $pdo->prepare('DELETE FROM gallery WHERE id = ?')->execute([$id]);
                $flash = 'Η εικόνα διαγράφηκε.';
            }
        }
    } catch (Throwable $ex) {
        $flash = 'Σφάλμα: ' . $ex->getMessage();
    }
}

$images = $pdo->query('SELECT * FROM gallery ORDER BY sort_order, id DESC')->fetchAll();
include __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-wrap">
  <h1>Gallery</h1>
  <?php if ($flash): ?><p class="alert alert-success"><?= e($flash) ?></p><?php endif; ?>
  <section class="panel">
    <h2>Ανέβασμα εικόνων</h2>
    <form method="post" enctype="multipart/form-data" class="admin-form">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="op" value="upload">
      <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" required>
      <button class="btn btn-primary" type="submit"><i class="fa-solid fa-upload"></i> Ανέβασμα</button>
      <p class="muted small">JPG/PNG/WEBP · έως 5MB η κάθε μία.</p>
    </form>
  </section>

  <section class="panel">
    <h2>Εικόνες</h2>
    <?php if (!$images): ?>
      <p class="muted">Δεν υπάρχουν εικόνες.</p>
    <?php else: ?>
    <form method="post" id="reorder-form">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="op" value="reorder">
      <ul class="gallery-admin" id="gallery-list">
        <?php foreach ($images as $img): ?>
          <li data-id="<?= (int)$img['id'] ?>">
            <input type="hidden" name="order[]" value="<?= (int)$img['id'] ?>">
            <img src="<?= SITE_URL ?>/uploads/gallery/<?= e($img['filename']) ?>" alt="">
            <div class="ga-body">
              <input type="text" placeholder="Λεζάντα (προαιρετικό)" value="<?= e($img['caption'] ?? '') ?>" onchange="saveCaption(<?= (int)$img['id'] ?>, this.value)">
              <div class="ga-actions">
                <button type="button" class="btn-tiny" title="Πάνω" onclick="moveItem(this,-1)"><i class="fa-solid fa-chevron-up"></i></button>
                <button type="button" class="btn-tiny" title="Κάτω" onclick="moveItem(this,1)"><i class="fa-solid fa-chevron-down"></i></button>
                <button type="button" class="btn-tiny link-danger" onclick="deleteImg(<?= (int)$img['id'] ?>)">Διαγραφή</button>
              </div>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
      <button class="btn btn-primary" type="submit"><i class="fa-solid fa-arrows-up-down"></i> Αποθήκευση σειράς</button>
    </form>
    <?php endif; ?>
  </section>
</div>
<script>
const CSRF = <?= json_encode(csrf_token()) ?>;
const SITE = <?= json_encode(SITE_URL) ?>;
function moveItem(btn, dir) {
  const li = btn.closest('li'), list = li.parentNode;
  if (dir < 0 && li.previousElementSibling) list.insertBefore(li, li.previousElementSibling);
  if (dir > 0 && li.nextElementSibling) list.insertBefore(li.nextElementSibling, li);
}
function saveCaption(id, caption) {
  const fd = new FormData();
  fd.append('csrf', CSRF); fd.append('op','update_caption'); fd.append('id', id); fd.append('caption', caption);
  fetch(SITE + '/admin/gallery.php', {method:'POST', body: fd});
}
function deleteImg(id) {
  if (!confirm('Διαγραφή εικόνας;')) return;
  const fd = new FormData();
  fd.append('csrf', CSRF); fd.append('op','delete'); fd.append('id', id);
  fetch(SITE + '/admin/gallery.php', {method:'POST', body: fd}).then(()=>location.reload());
}
</script>
<?php include __DIR__ . '/includes/admin_footer.php'; ?>
