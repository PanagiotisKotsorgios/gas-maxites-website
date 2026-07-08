<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();
$page_title = 'Τμήματα — Μαχητές Admin';
$active = 'programs';
$pdo = db();
$flash = null;

if (!defined('UPLOAD_DIR_PROGRAMS')) {
    define('UPLOAD_DIR_PROGRAMS', __DIR__ . '/../uploads/programs');
}

function handle_upload_program_image(): ?string {
    if (empty($_FILES['image']['name'])) return null;
    if ($_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Σφάλμα ανεβάσματος.');
    if ($_FILES['image']['size'] > MAX_UPLOAD_BYTES) throw new RuntimeException('Το αρχείο ξεπερνά τα 5MB.');
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','webp'], true)) throw new RuntimeException('Επιτρέπονται μόνο JPG/PNG/WEBP.');
    $mime = mime_content_type($_FILES['image']['tmp_name']);
    if (!in_array($mime, ['image/jpeg','image/png','image/webp'], true)) throw new RuntimeException('Μη έγκυρη εικόνα.');
    if (!is_dir(UPLOAD_DIR_PROGRAMS)) mkdir(UPLOAD_DIR_PROGRAMS, 0775, true);
    $name = 'prog-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
    move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_DIR_PROGRAMS . '/' . $name);
    return $name;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $op = $_POST['op'] ?? '';
    try {
        if ($op === 'save') {
            $id     = (int)($_POST['id'] ?? 0);
            $name   = trim($_POST['name'] ?? '');
            $desc   = trim($_POST['description'] ?? '');
            $age    = trim($_POST['age_range'] ?? '');
            $icon   = trim($_POST['icon'] ?? '');
            $fee    = $_POST['monthly_fee'] !== '' ? (float)$_POST['monthly_fee'] : null;
            $sort   = (int)($_POST['sort_order'] ?? 0);
            $act    = isset($_POST['active']) ? 1 : 0;
            $img    = handle_upload_program_image();
            if ($name === '') throw new RuntimeException('Το όνομα είναι υποχρεωτικό.');
            if ($id) {
                if ($img) {
                    $pdo->prepare('UPDATE programs SET name=?, description=?, age_range=?, monthly_fee=?, icon=?, image=?, sort_order=?, active=? WHERE id=?')
                        ->execute([$name,$desc,$age,$fee,$icon,$img,$sort,$act,$id]);
                } else {
                    $pdo->prepare('UPDATE programs SET name=?, description=?, age_range=?, monthly_fee=?, icon=?, sort_order=?, active=? WHERE id=?')
                        ->execute([$name,$desc,$age,$fee,$icon,$sort,$act,$id]);
                }
                $flash = 'Ενημερώθηκε.';
            } else {
                $pdo->prepare('INSERT INTO programs (name,description,age_range,monthly_fee,icon,image,sort_order,active) VALUES (?,?,?,?,?,?,?,?)')
                    ->execute([$name,$desc,$age,$fee,$icon,$img,$sort,$act]);
                $flash = 'Προστέθηκε.';
            }
        }
        if ($op === 'clear_image') {
            $pdo->prepare('UPDATE programs SET image=NULL WHERE id=?')->execute([(int)$_POST['id']]);
            $flash = 'Η εικόνα αφαιρέθηκε.';
        }
        if ($op === 'delete') {
            $pdo->prepare('DELETE FROM programs WHERE id = ?')->execute([(int)$_POST['id']]);
            $flash = 'Διαγράφηκε.';
        }
    } catch (Throwable $ex) {
        $flash = 'Σφάλμα: ' . $ex->getMessage();
    }
}

$edit_id = (int)($_GET['id'] ?? 0);
$edit = ['id'=>0,'name'=>'','description'=>'','age_range'=>'','monthly_fee'=>'','image'=>null,'icon'=>'','sort_order'=>0,'active'=>1];
if ($edit_id) {
    $s = $pdo->prepare('SELECT * FROM programs WHERE id = ?');
    $s->execute([$edit_id]);
    $edit = $s->fetch() ?: $edit;
}
$rows = $pdo->query('SELECT * FROM programs ORDER BY sort_order, id')->fetchAll();
include __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-wrap">
  <h1>Τμήματα</h1>
  <p class="admin-hint">
    <i class="fa-solid fa-circle-info"></i>
    Εδώ διαχειρίζεσαι το περιεχόμενο κάθε <strong>κάρτας τμήματος</strong> (όνομα, ηλικίες, περιγραφή, εικόνα/εικονίδιο, συνδρομή).
    Για τους <strong>τίτλους της ενότητας</strong> (eyebrow, τίτλος, υπότιτλος) στην αρχική σελίδα &rarr;
    <a href="<?= SITE_URL ?>/admin/settings.php#programs_h"><i class="fa-solid fa-gear"></i> Ρυθμίσεις &raquo; Αρχική — Τμήματα (headings)</a>.
  </p>
  <?php if ($flash): ?><p class="alert alert-success"><?= e($flash) ?></p><?php endif; ?>
  <div class="two-panels">
    <section class="panel">
      <h2><?= $edit['id']?'Επεξεργασία':'Νέο τμήμα' ?></h2>
      <form method="post" enctype="multipart/form-data" class="admin-form">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="op" value="save">
        <input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
        <label>Όνομα<input type="text" name="name" required value="<?= e($edit['name']) ?>"></label>
        <label>Περιγραφή<textarea name="description" rows="3"><?= e($edit['description']) ?></textarea></label>
        <div class="two-col">
          <label>Ηλικίες<input type="text" name="age_range" placeholder="4-8 ετών" value="<?= e($edit['age_range']) ?>"></label>
          <label>Μηνιαία συνδρομή (€)<input type="number" step="0.01" name="monthly_fee" value="<?= e((string)$edit['monthly_fee']) ?>"></label>
        </div>
        <label>Εικονίδιο (FontAwesome class — π.χ. <code>fa-solid fa-hand-fist</code>) <small class="muted">Χρησιμοποιείται όταν δεν έχει εικόνα.</small>
          <input type="text" name="icon" placeholder="fa-solid fa-hand-fist" value="<?= e((string)($edit['icon'] ?? '')) ?>">
        </label>
        <label>Εικόνα τμήματος (JPG/PNG/WEBP, ≤5MB) <small class="muted">Αν οριστεί, εμφανίζεται αντί για εικονίδιο.</small>
          <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
        </label>
        <?php if (!empty($edit['image'])): ?>
          <div style="display:flex;gap:1rem;align-items:center">
            <img src="<?= SITE_URL ?>/uploads/programs/<?= e($edit['image']) ?>" alt="" style="max-width:140px;border-radius:6px">
            <form method="post" style="margin:0" onsubmit="return confirm('Αφαίρεση εικόνας;')">
              <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="op" value="clear_image">
              <input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
              <button type="submit" class="link-danger">Αφαίρεση εικόνας</button>
            </form>
          </div>
        <?php endif; ?>
        <div class="two-col">
          <label>Σειρά<input type="number" name="sort_order" value="<?= (int)$edit['sort_order'] ?>"></label>
          <label class="inline"><input type="checkbox" name="active" value="1" <?= $edit['active']?'checked':'' ?>> Ενεργό</label>
        </div>
        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> <?= $edit['id']?'Ενημέρωση':'Προσθήκη' ?></button>
        <?php if ($edit['id']): ?><a class="btn btn-ghost" href="<?= SITE_URL ?>/admin/programs.php">Ακύρωση</a><?php endif; ?>
      </form>
    </section>
    <section class="panel">
      <h2>Όλα τα τμήματα</h2>
      <table class="admin-table">
        <thead><tr><th></th><th>Όνομα</th><th>Ηλικίες</th><th>€/μήνα</th><th>Ενεργό</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td>
                <?php if (!empty($r['image'])): ?>
                  <img src="<?= SITE_URL ?>/uploads/programs/<?= e($r['image']) ?>" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:6px">
                <?php elseif (!empty($r['icon'])): ?>
                  <i class="<?= e($r['icon']) ?>"></i>
                <?php else: ?>
                  <span class="muted">—</span>
                <?php endif; ?>
              </td>
              <td><?= e($r['name']) ?></td>
              <td class="muted"><?= e($r['age_range']) ?></td>
              <td><?= $r['monthly_fee']!==null ? number_format((float)$r['monthly_fee'],0) : '—' ?></td>
              <td><?= $r['active'] ? '<i class="fa-solid fa-check text-ok"></i>' : '<span class="muted">—</span>' ?></td>
              <td class="row-actions">
                <a href="?id=<?= (int)$r['id'] ?>">Επεξεργασία</a>
                <form method="post" style="display:inline" onsubmit="return confirm('Διαγραφή;')">
                  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="op" value="delete">
                  <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                  <button type="submit" class="link-danger">Διαγραφή</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$rows): ?><tr><td colspan="6" class="muted">Δεν υπάρχουν τμήματα.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </section>
  </div>
</div>
<?php include __DIR__ . '/includes/admin_footer.php'; ?>
