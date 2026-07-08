<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();
$page_title = 'Αθλητές — Μαχητές Admin';
$active = 'athletes';
$pdo = db();
$action = $_GET['action'] ?? 'list';
$flash = null;

function handle_upload_athlete_photo(): ?string {
    if (empty($_FILES['photo']['name'])) return null;
    if ($_FILES['photo']['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Σφάλμα ανεβάσματος.');
    if ($_FILES['photo']['size'] > MAX_UPLOAD_BYTES) throw new RuntimeException('Το αρχείο ξεπερνά τα 5MB.');
    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','webp'], true)) throw new RuntimeException('Επιτρέπονται μόνο JPG/PNG/WEBP.');
    $mime = mime_content_type($_FILES['photo']['tmp_name']);
    if (!in_array($mime, ['image/jpeg','image/png','image/webp'], true)) throw new RuntimeException('Μη έγκυρη εικόνα.');
    if (!is_dir(UPLOAD_DIR_ATHLETES)) mkdir(UPLOAD_DIR_ATHLETES, 0775, true);
    $name = 'ath-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
    move_uploaded_file($_FILES['photo']['tmp_name'], UPLOAD_DIR_ATHLETES . '/' . $name);
    return $name;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $op = $_POST['op'] ?? '';
    try {
        if ($op === 'save_athlete') {
            $id       = (int)($_POST['id'] ?? 0);
            $name     = trim($_POST['name'] ?? '');
            $slug     = trim($_POST['slug'] ?? '') ?: slugify($name);
            $weight   = trim($_POST['weight_category'] ?? '');
            $belt     = trim($_POST['belt'] ?? '');
            $age_grp  = trim($_POST['age_group'] ?? '');
            $dob      = trim($_POST['dob'] ?? '') ?: null;
            $bio      = (string)($_POST['bio'] ?? '');
            $wins     = (int)($_POST['wins'] ?? 0);
            $losses   = (int)($_POST['losses'] ?? 0);
            $draws    = (int)($_POST['draws'] ?? 0);
            $sort     = (int)($_POST['sort_order'] ?? 0);
            $active_i = isset($_POST['active']) ? 1 : 0;
            if ($name === '') throw new RuntimeException('Το όνομα είναι υποχρεωτικό.');
            $photo = handle_upload_athlete_photo();

            if ($id) {
                if ($photo) {
                    $pdo->prepare('UPDATE athletes SET slug=?, name=?, weight_category=?, belt=?, age_group=?, dob=?, photo=?, bio=?, wins=?, losses=?, draws=?, sort_order=?, active=? WHERE id=?')
                        ->execute([$slug,$name,$weight,$belt,$age_grp,$dob,$photo,$bio,$wins,$losses,$draws,$sort,$active_i,$id]);
                } else {
                    $pdo->prepare('UPDATE athletes SET slug=?, name=?, weight_category=?, belt=?, age_group=?, dob=?, bio=?, wins=?, losses=?, draws=?, sort_order=?, active=? WHERE id=?')
                        ->execute([$slug,$name,$weight,$belt,$age_grp,$dob,$bio,$wins,$losses,$draws,$sort,$active_i,$id]);
                }
                $flash = 'Ο αθλητής ενημερώθηκε.';
                header('Location: ' . SITE_URL . '/admin/athletes.php?action=edit&id=' . $id . '&msg=' . urlencode($flash));
                exit;
            } else {
                $pdo->prepare('INSERT INTO athletes (slug,name,weight_category,belt,age_group,dob,photo,bio,wins,losses,draws,sort_order,active) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)')
                    ->execute([$slug,$name,$weight,$belt,$age_grp,$dob,$photo,$bio,$wins,$losses,$draws,$sort,$active_i]);
                $newId = (int)$pdo->lastInsertId();
                header('Location: ' . SITE_URL . '/admin/athletes.php?action=edit&id=' . $newId . '&msg=' . urlencode('Ο αθλητής προστέθηκε.'));
                exit;
            }
        }
        if ($op === 'delete_athlete') {
            $id = (int)($_POST['id'] ?? 0);
            $pdo->prepare('DELETE FROM athletes WHERE id = ?')->execute([$id]);
            header('Location: ' . SITE_URL . '/admin/athletes.php?msg=' . urlencode('Ο αθλητής διαγράφηκε.'));
            exit;
        }
        if ($op === 'save_match') {
            $aid   = (int)($_POST['athlete_id'] ?? 0);
            $mid   = (int)($_POST['match_id'] ?? 0);
            $date  = trim($_POST['match_date'] ?? '') ?: null;
            $opp   = trim($_POST['opponent'] ?? '');
            $ev    = trim($_POST['event'] ?? '');
            $res   = $_POST['result'] ?? 'win';
            $score = trim($_POST['score'] ?? '');
            $vid   = trim($_POST['video_url'] ?? '');
            $notes = trim($_POST['notes'] ?? '');
            if (!in_array($res, ['win','loss','draw'], true)) $res = 'win';
            if ($mid) {
                $pdo->prepare('UPDATE athlete_matches SET match_date=?, opponent=?, event=?, result=?, score=?, video_url=?, notes=? WHERE id=? AND athlete_id=?')
                    ->execute([$date,$opp,$ev,$res,$score,$vid,$notes,$mid,$aid]);
            } else {
                $pdo->prepare('INSERT INTO athlete_matches (athlete_id,match_date,opponent,event,result,score,video_url,notes) VALUES (?,?,?,?,?,?,?,?)')
                    ->execute([$aid,$date,$opp,$ev,$res,$score,$vid,$notes]);
            }
            header('Location: ' . SITE_URL . '/admin/athletes.php?action=edit&id=' . $aid . '&msg=' . urlencode('Ο αγώνας αποθηκεύτηκε.') . '#matches');
            exit;
        }
        if ($op === 'delete_match') {
            $mid = (int)($_POST['match_id'] ?? 0);
            $aid = (int)($_POST['athlete_id'] ?? 0);
            $pdo->prepare('DELETE FROM athlete_matches WHERE id = ? AND athlete_id = ?')->execute([$mid, $aid]);
            header('Location: ' . SITE_URL . '/admin/athletes.php?action=edit&id=' . $aid . '&msg=' . urlencode('Ο αγώνας διαγράφηκε.') . '#matches');
            exit;
        }
    } catch (Throwable $ex) {
        $flash = 'Σφάλμα: ' . $ex->getMessage();
    }
}
$flash = $flash ?? ($_GET['msg'] ?? null);

if ($action === 'edit' || $action === 'new') {
    $ath = ['id'=>0,'slug'=>'','name'=>'','weight_category'=>'','belt'=>'','age_group'=>'','dob'=>'','photo'=>null,'bio'=>'','wins'=>0,'losses'=>0,'draws'=>0,'sort_order'=>0,'active'=>1];
    $matches = [];
    if ($action === 'edit') {
        $s = $pdo->prepare('SELECT * FROM athletes WHERE id = ?');
        $s->execute([(int)($_GET['id'] ?? 0)]);
        $ath = $s->fetch() ?: $ath;
        if ($ath['id']) {
            $ms = $pdo->prepare('SELECT * FROM athlete_matches WHERE athlete_id = ? ORDER BY match_date DESC, id DESC');
            $ms->execute([$ath['id']]);
            $matches = $ms->fetchAll();
        }
    }
    include __DIR__ . '/includes/admin_header.php';
    ?>
    <div class="admin-wrap">
      <p><a href="<?= SITE_URL ?>/admin/athletes.php"><i class="fa-solid fa-arrow-left"></i> Αθλητές</a></p>
      <h1><?= $ath['id'] ? 'Επεξεργασία αθλητή' : 'Νέος αθλητής' ?></h1>
      <?php if ($flash): ?><p class="alert alert-success"><?= e($flash) ?></p><?php endif; ?>

      <div class="two-panels">
        <section class="panel">
          <h2>Στοιχεία</h2>
          <form method="post" enctype="multipart/form-data" class="admin-form">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="op" value="save_athlete">
            <input type="hidden" name="id" value="<?= (int)$ath['id'] ?>">
            <label>Όνομα<input type="text" name="name" required value="<?= e($ath['name']) ?>"></label>
            <label>Slug (URL) <small>Αφήστε κενό για αυτόματο.</small>
              <input type="text" name="slug" value="<?= e($ath['slug']) ?>">
            </label>
            <div class="two-col">
              <label>Ζώνη
                <input type="text" name="belt" placeholder="Μαύρη 1ου Dan" value="<?= e($ath['belt']) ?>">
              </label>
              <label>Κατηγορία βάρους
                <input type="text" name="weight_category" placeholder="-68kg" value="<?= e($ath['weight_category']) ?>">
              </label>
            </div>
            <div class="two-col">
              <label>Ηλικιακή κατηγορία
                <input type="text" name="age_group" placeholder="Νέοι Άνδρες / Παιδιά" value="<?= e($ath['age_group']) ?>">
              </label>
              <label>Ημ/νία γέννησης
                <input type="date" name="dob" value="<?= e($ath['dob']) ?>">
              </label>
            </div>
            <div class="two-col two-col-3">
              <label>Νίκες<input type="number" name="wins" min="0" value="<?= (int)$ath['wins'] ?>"></label>
              <label>Ήττες<input type="number" name="losses" min="0" value="<?= (int)$ath['losses'] ?>"></label>
              <label>Ισοπαλίες<input type="number" name="draws" min="0" value="<?= (int)$ath['draws'] ?>"></label>
            </div>
            <label>Φωτογραφία (JPG/PNG/WEBP, ≤5MB)
              <input type="file" name="photo" accept="image/jpeg,image/png,image/webp">
            </label>
            <?php if ($ath['photo']): ?>
              <p><img src="<?= SITE_URL ?>/uploads/athletes/<?= e($ath['photo']) ?>" style="max-width:180px;border-radius:6px"></p>
            <?php endif; ?>
            <div class="editor-field">
              <label for="editor-toolbar">Βιογραφικό</label>
              <small class="muted">Χρησιμοποίησε τη γραμμή εργαλείων.</small>
              <div id="editor-toolbar">
                <span class="ql-formats">
                  <select class="ql-header"><option value="2">Επικεφαλίδα</option><option value="3">Υπο-επικεφαλίδα</option><option selected>Κείμενο</option></select>
                </span>
                <span class="ql-formats"><button class="ql-bold"></button><button class="ql-italic"></button><button class="ql-underline"></button></span>
                <span class="ql-formats"><button class="ql-list" value="ordered"></button><button class="ql-list" value="bullet"></button><button class="ql-blockquote"></button></span>
                <span class="ql-formats"><button class="ql-link"></button></span>
                <span class="ql-formats"><button class="ql-clean"></button></span>
              </div>
              <div id="editor"><?= $ath['bio'] ?></div>
              <textarea name="bio" id="editor-input" hidden><?= e($ath['bio']) ?></textarea>
            </div>
            <div class="two-col">
              <label>Σειρά<input type="number" name="sort_order" value="<?= (int)$ath['sort_order'] ?>"></label>
              <label class="inline"><input type="checkbox" name="active" value="1" <?= $ath['active']?'checked':'' ?>> Ενεργός</label>
            </div>
            <button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Αποθήκευση</button>
            <?php if ($ath['id']): ?>
              <a class="btn btn-ghost" href="<?= SITE_URL ?>/athlete.php?slug=<?= e($ath['slug']) ?>" target="_blank">Δες προφίλ</a>
            <?php endif; ?>
          </form>
        </section>

        <?php if ($ath['id']): ?>
        <section class="panel" id="matches">
          <h2>Αγώνες (<?= count($matches) ?>)</h2>
          <form method="post" class="admin-form match-form">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="op" value="save_match">
            <input type="hidden" name="athlete_id" value="<?= (int)$ath['id'] ?>">
            <input type="hidden" name="match_id" value="0">
            <div class="two-col">
              <label>Ημερομηνία<input type="date" name="match_date"></label>
              <label>Αντίπαλος<input type="text" name="opponent"></label>
            </div>
            <label>Διοργάνωση<input type="text" name="event" placeholder="Πανελλήνιο Κύπελλο 2025"></label>
            <div class="two-col">
              <label>Αποτέλεσμα
                <select name="result">
                  <option value="win">Νίκη</option>
                  <option value="loss">Ήττα</option>
                  <option value="draw">Ισοπαλία</option>
                </select>
              </label>
              <label>Score<input type="text" name="score" placeholder="12-8"></label>
            </div>
            <label>YouTube URL (προαιρετικό)<input type="url" name="video_url" placeholder="https://youtu.be/..."></label>
            <label>Σημειώσεις<input type="text" name="notes"></label>
            <button class="btn btn-primary" type="submit"><i class="fa-solid fa-plus"></i> Προσθήκη αγώνα</button>
          </form>

          <table class="admin-table" style="margin-top:1rem">
            <thead><tr><th>Ημ/νία</th><th>Αντίπαλος</th><th>Διοργάνωση</th><th>Αποτ.</th><th>Score</th><th></th></tr></thead>
            <tbody>
              <?php foreach ($matches as $m): ?>
                <tr>
                  <td><?= $m['match_date']?e(date('d/m/y', strtotime($m['match_date']))):'—' ?></td>
                  <td><?= e($m['opponent']) ?></td>
                  <td><?= e($m['event']) ?></td>
                  <td><span class="pill pill-status-<?= e($m['result']) ?>"><?= e($m['result']==='win'?'Νίκη':($m['result']==='loss'?'Ήττα':'Ισοπ.')) ?></span></td>
                  <td><?= e($m['score']) ?></td>
                  <td class="row-actions">
                    <?php if ($m['video_url']): ?><a href="<?= e($m['video_url']) ?>" target="_blank"><i class="fa-brands fa-youtube"></i></a><?php endif; ?>
                    <form method="post" style="display:inline" onsubmit="return confirm('Διαγραφή αγώνα;')">
                      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                      <input type="hidden" name="op" value="delete_match">
                      <input type="hidden" name="match_id" value="<?= (int)$m['id'] ?>">
                      <input type="hidden" name="athlete_id" value="<?= (int)$ath['id'] ?>">
                      <button type="submit" class="link-danger">Διαγραφή</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (!$matches): ?><tr><td colspan="6" class="muted">Δεν υπάρχουν αγώνες ακόμη.</td></tr><?php endif; ?>
            </tbody>
          </table>
        </section>
        <?php endif; ?>
      </div>
    </div>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
    (function() {
      const quill = new Quill('#editor', { modules:{toolbar:'#editor-toolbar'}, placeholder:'Βιογραφικό αθλητή…', theme:'snow' });
      const hidden = document.getElementById('editor-input');
      hidden.value = quill.root.innerHTML;
      quill.on('text-change', () => { hidden.value = quill.getText().trim() === '' ? '' : quill.root.innerHTML; });
      const mainForm = document.querySelector('form.admin-form');
      if (mainForm) mainForm.addEventListener('submit', () => { hidden.value = quill.getText().trim() === '' ? '' : quill.root.innerHTML; });
    })();
    </script>
    <?php
    include __DIR__ . '/includes/admin_footer.php';
    exit;
}

$athletes = $pdo->query('SELECT id, slug, name, belt, weight_category, wins, losses, draws, active FROM athletes ORDER BY sort_order, id')->fetchAll();
include __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-wrap">
  <div class="page-head">
    <h1>Αθλητές</h1>
    <a href="<?= SITE_URL ?>/admin/athletes.php?action=new" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Νέος αθλητής</a>
  </div>
  <?php if ($flash): ?><p class="alert alert-success"><?= e($flash) ?></p><?php endif; ?>
  <table class="admin-table">
    <thead><tr><th>Όνομα</th><th>Ζώνη</th><th>Κατηγορία</th><th>Ρεκόρ</th><th>Ενεργός</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($athletes as $a): ?>
        <tr>
          <td><?= e($a['name']) ?></td>
          <td class="muted"><?= e($a['belt']) ?></td>
          <td class="muted"><?= e($a['weight_category']) ?></td>
          <td><?= (int)$a['wins'] ?>W / <?= (int)$a['losses'] ?>L / <?= (int)$a['draws'] ?>D</td>
          <td><?= $a['active'] ? '<i class="fa-solid fa-check text-ok"></i>' : '<span class="muted">—</span>' ?></td>
          <td class="row-actions">
            <a href="<?= SITE_URL ?>/athlete.php?slug=<?= e($a['slug']) ?>" target="_blank">Δες</a>
            <a href="<?= SITE_URL ?>/admin/athletes.php?action=edit&id=<?= (int)$a['id'] ?>">Επεξεργασία</a>
            <form method="post" style="display:inline" onsubmit="return confirm('Διαγραφή αθλητή; (θα διαγραφούν και οι αγώνες)')">
              <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="op" value="delete_athlete">
              <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
              <button type="submit" class="link-danger">Διαγραφή</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$athletes): ?><tr><td colspan="6" class="muted">Δεν υπάρχουν αθλητές ακόμη.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/includes/admin_footer.php'; ?>
