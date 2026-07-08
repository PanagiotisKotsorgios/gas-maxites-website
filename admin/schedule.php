<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();
$page_title = 'Πρόγραμμα — Μαχητές Admin';
$active = 'schedule';
$pdo = db();
$flash = null;

$days = [1=>'Δευτέρα',2=>'Τρίτη',3=>'Τετάρτη',4=>'Πέμπτη',5=>'Παρασκευή',6=>'Σάββατο',7=>'Κυριακή'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $op = $_POST['op'] ?? '';
    if ($op === 'save') {
        $id     = (int)($_POST['id'] ?? 0);
        $day    = (int)($_POST['day_of_week'] ?? 1);
        $time   = trim($_POST['time_range'] ?? '');
        $group  = trim($_POST['group_name'] ?? '');
        $age    = trim($_POST['age_range'] ?? '');
        $notes  = trim($_POST['notes'] ?? '');
        $sort   = (int)($_POST['sort_order'] ?? 0);
        $act    = isset($_POST['active']) ? 1 : 0;
        if ($group === '' || $time === '') { $flash = 'Ώρα και όνομα τμήματος υποχρεωτικά.'; }
        else if ($id) {
            $pdo->prepare('UPDATE schedule SET day_of_week=?, time_range=?, group_name=?, age_range=?, notes=?, sort_order=?, active=? WHERE id=?')
                ->execute([$day,$time,$group,$age,$notes,$sort,$act,$id]);
            $flash = 'Ενημερώθηκε.';
        } else {
            $pdo->prepare('INSERT INTO schedule (day_of_week,time_range,group_name,age_range,notes,sort_order,active) VALUES (?,?,?,?,?,?,?)')
                ->execute([$day,$time,$group,$age,$notes,$sort,$act]);
            $flash = 'Προστέθηκε.';
        }
    }
    if ($op === 'delete') {
        $pdo->prepare('DELETE FROM schedule WHERE id = ?')->execute([(int)$_POST['id']]);
        $flash = 'Διαγράφηκε.';
    }
}

$edit_id = (int)($_GET['id'] ?? 0);
$edit = ['id'=>0,'day_of_week'=>1,'time_range'=>'','group_name'=>'','age_range'=>'','notes'=>'','sort_order'=>0,'active'=>1];
if ($edit_id) {
    $s = $pdo->prepare('SELECT * FROM schedule WHERE id = ?');
    $s->execute([$edit_id]);
    $edit = $s->fetch() ?: $edit;
}

$rows = $pdo->query('SELECT * FROM schedule ORDER BY day_of_week, sort_order, id')->fetchAll();
include __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-wrap">
  <h1>Πρόγραμμα προπονήσεων</h1>
  <?php if ($flash): ?><p class="alert alert-success"><?= e($flash) ?></p><?php endif; ?>

  <div class="two-panels">
    <section class="panel">
      <h2><?= $edit['id'] ? 'Επεξεργασία' : 'Νέα προπόνηση' ?></h2>
      <form method="post" class="admin-form">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="op" value="save">
        <input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
        <label>Ημέρα
          <select name="day_of_week">
            <?php foreach ($days as $k=>$lbl): ?>
              <option value="<?= $k ?>" <?= (int)$edit['day_of_week']===$k?'selected':'' ?>><?= e($lbl) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label>Ώρα (π.χ. 17:00-18:30)<input type="text" name="time_range" required value="<?= e($edit['time_range']) ?>"></label>
        <label>Τμήμα<input type="text" name="group_name" required placeholder="Παιδικό αρχαρίων" value="<?= e($edit['group_name']) ?>"></label>
        <label>Ηλικίες<input type="text" name="age_range" placeholder="4-8 ετών" value="<?= e($edit['age_range']) ?>"></label>
        <label>Σημειώσεις<input type="text" name="notes" value="<?= e($edit['notes']) ?>"></label>
        <div class="two-col">
          <label>Σειρά<input type="number" name="sort_order" value="<?= (int)$edit['sort_order'] ?>"></label>
          <label class="inline"><input type="checkbox" name="active" value="1" <?= $edit['active']?'checked':'' ?>> Ενεργό</label>
        </div>
        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> <?= $edit['id']?'Ενημέρωση':'Προσθήκη' ?></button>
        <?php if ($edit['id']): ?><a class="btn btn-ghost" href="<?= SITE_URL ?>/admin/schedule.php">Ακύρωση</a><?php endif; ?>
      </form>
    </section>

    <section class="panel">
      <h2>Εβδομαδιαίο πρόγραμμα</h2>
      <table class="admin-table">
        <thead><tr><th>Ημέρα</th><th>Ώρα</th><th>Τμήμα</th><th>Ηλικίες</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><?= e($days[(int)$r['day_of_week']] ?? '—') ?></td>
              <td class="muted"><?= e($r['time_range']) ?></td>
              <td><?= e($r['group_name']) ?></td>
              <td class="muted"><?= e($r['age_range']) ?></td>
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
          <?php if (!$rows): ?><tr><td colspan="5" class="muted">Δεν υπάρχει πρόγραμμα ακόμη.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </section>
  </div>
</div>
<?php include __DIR__ . '/includes/admin_footer.php'; ?>
