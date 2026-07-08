<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();
$page_title = 'Τρόπαια — Μαχητές Admin';
$active = 'trophies';
$pdo = db();
$flash = null;

function handle_upload_trophy(): ?string {
    if (empty($_FILES['image']['name'])) return null;
    if ($_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Σφάλμα ανεβάσματος.');
    if ($_FILES['image']['size'] > MAX_UPLOAD_BYTES) throw new RuntimeException('Το αρχείο ξεπερνά τα 5MB.');
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','webp'], true)) throw new RuntimeException('Επιτρέπονται μόνο JPG/PNG/WEBP.');
    $mime = mime_content_type($_FILES['image']['tmp_name']);
    if (!in_array($mime, ['image/jpeg','image/png','image/webp'], true)) throw new RuntimeException('Μη έγκυρη εικόνα.');
    if (!is_dir(UPLOAD_DIR_TROPHIES)) mkdir(UPLOAD_DIR_TROPHIES, 0775, true);
    $name = 'trophy-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
    move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_DIR_TROPHIES . '/' . $name);
    return $name;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $op = $_POST['op'] ?? '';
    try {
        if ($op === 'save') {
            $id     = (int)($_POST['id'] ?? 0);
            $title  = trim($_POST['title'] ?? '');
            $event  = trim($_POST['event'] ?? '');
            $date   = trim($_POST['achieved_on'] ?? '') ?: null;
            $desc   = trim($_POST['description'] ?? '');
            $ath    = (int)($_POST['athlete_id'] ?? 0) ?: null;
            $cat    = trim($_POST['category'] ?? '');
            $sort   = (int)($_POST['sort_order'] ?? 0);
            if ($title === '') throw new RuntimeException('Ο τίτλος είναι υποχρεωτικός.');
            $img = handle_upload_trophy();
            if ($id) {
                if ($img) {
                    $pdo->prepare('UPDATE trophies SET title=?, event=?, achieved_on=?, description=?, image=?, athlete_id=?, category=?, sort_order=? WHERE id=?')
                        ->execute([$title,$event,$date,$desc,$img,$ath,$cat,$sort,$id]);
                } else {
                    $pdo->prepare('UPDATE trophies SET title=?, event=?, achieved_on=?, description=?, athlete_id=?, category=?, sort_order=? WHERE id=?')
                        ->execute([$title,$event,$date,$desc,$ath,$cat,$sort,$id]);
                }
                $flash = 'Το τρόπαιο ενημερώθηκε.';
            } else {
                $pdo->prepare('INSERT INTO trophies (title,event,achieved_on,description,image,athlete_id,category,sort_order) VALUES (?,?,?,?,?,?,?,?)')
                    ->execute([$title,$event,$date,$desc,$img,$ath,$cat,$sort]);
                $flash = 'Το τρόπαιο προστέθηκε.';
            }
        }
        if ($op === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            $pdo->prepare('DELETE FROM trophies WHERE id = ?')->execute([$id]);
            $flash = 'Το τρόπαιο διαγράφηκε.';
        }
    } catch (Throwable $ex) {
        $flash = 'Σφάλμα: ' . $ex->getMessage();
    }
}

$edit_id = (int)($_GET['id'] ?? 0);
$edit = ['id'=>0,'title'=>'','event'=>'','achieved_on'=>'','description'=>'','image'=>null,'athlete_id'=>0,'category'=>'','sort_order'=>0];
if ($edit_id) {
    $s = $pdo->prepare('SELECT * FROM trophies WHERE id = ?');
    $s->execute([$edit_id]);
    $edit = $s->fetch() ?: $edit;
}

$trophies = $pdo->query('SELECT t.*, a.name AS athlete_name FROM trophies t LEFT JOIN athletes a ON a.id = t.athlete_id ORDER BY t.achieved_on DESC, t.id DESC')->fetchAll();
$athletes = $pdo->query('SELECT id, name FROM athletes ORDER BY sort_order, name')->fetchAll();

include __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-wrap">
  <h1>Τρόπαια &amp; διακρίσεις</h1>
  <?php if ($flash): ?><p class="alert alert-success"><?= e($flash) ?></p><?php endif; ?>

  <div class="two-panels">
    <section class="panel">
      <h2><?= $edit['id'] ? 'Επεξεργασία' : 'Νέο τρόπαιο' ?></h2>
      <form method="post" enctype="multipart/form-data" class="admin-form">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="op" value="save">
        <input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
        <label>Τίτλος<input type="text" name="title" required value="<?= e($edit['title']) ?>"></label>
        <label>Διοργάνωση<input type="text" name="event" value="<?= e($edit['event']) ?>"></label>
        <div class="two-col">
          <label>Ημ/νία<input type="date" name="achieved_on" value="<?= e($edit['achieved_on']) ?>"></label>
          <label>Κατηγορία
            <select name="category">
              <option value="">—</option>
              <option value="Ομαδικό" <?= $edit['category']==='Ομαδικό'?'selected':'' ?>>Ομαδικό</option>
              <option value="Ατομικό" <?= $edit['category']==='Ατομικό'?'selected':'' ?>>Ατομικό</option>
              <option value="Ειδικό βραβείο" <?= $edit['category']==='Ειδικό βραβείο'?'selected':'' ?>>Ειδικό βραβείο</option>
            </select>
          </label>
        </div>
        <label>Αθλητής (προαιρετικά)
          <select name="athlete_id">
            <option value="0">— Ομαδικό / χωρίς συγκεκριμένο αθλητή —</option>
            <?php foreach ($athletes as $a): ?>
              <option value="<?= (int)$a['id'] ?>" <?= (int)$edit['athlete_id']===(int)$a['id']?'selected':'' ?>><?= e($a['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>Περιγραφή<textarea name="description" rows="3"><?= e($edit['description']) ?></textarea></label>
        <label>Εικόνα (JPG/PNG/WEBP, ≤5MB)
          <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
        </label>
        <?php if ($edit['image']): ?>
          <p><img src="<?= SITE_URL ?>/uploads/trophies/<?= e($edit['image']) ?>" style="max-width:180px;border-radius:6px"></p>
        <?php endif; ?>
        <label>Σειρά<input type="number" name="sort_order" value="<?= (int)$edit['sort_order'] ?>"></label>
        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> <?= $edit['id']?'Ενημέρωση':'Προσθήκη' ?></button>
        <?php if ($edit['id']): ?><a class="btn btn-ghost" href="<?= SITE_URL ?>/admin/trophies.php">Ακύρωση</a><?php endif; ?>
      </form>
    </section>

    <section class="panel">
      <h2>Όλα τα τρόπαια (<?= count($trophies) ?>)</h2>
      <table class="admin-table">
        <thead><tr><th>Τίτλος</th><th>Διοργάνωση</th><th>Ημ/νία</th><th>Αθλητής</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($trophies as $t): ?>
            <tr>
              <td><?= e($t['title']) ?></td>
              <td class="muted"><?= e($t['event']) ?></td>
              <td><?= $t['achieved_on']?e(date('d/m/Y', strtotime($t['achieved_on']))):'—' ?></td>
              <td class="muted"><?= e($t['athlete_name'] ?? '—') ?></td>
              <td class="row-actions">
                <a href="?id=<?= (int)$t['id'] ?>">Επεξεργασία</a>
                <form method="post" style="display:inline" onsubmit="return confirm('Διαγραφή;')">
                  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="op" value="delete">
                  <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                  <button type="submit" class="link-danger">Διαγραφή</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$trophies): ?><tr><td colspan="5" class="muted">Δεν υπάρχουν τρόπαια.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </section>
  </div>
</div>
<?php include __DIR__ . '/includes/admin_footer.php'; ?>
