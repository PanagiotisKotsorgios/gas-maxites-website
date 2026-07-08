<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();
$page_title = 'Μηνύματα — Μαχητές Admin';
$active = 'messages';
$pdo = db();
$flash = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $op = $_POST['op'] ?? '';
    if ($op === 'status') {
        $id = (int)$_POST['id']; $st = $_POST['status'] ?? 'new';
        if (in_array($st, ['new','read','replied','archived'], true)) {
            $pdo->prepare('UPDATE messages SET status = ? WHERE id = ?')->execute([$st, $id]);
            $flash = 'Ενημερώθηκε.';
        }
    }
    if ($op === 'delete') {
        $pdo->prepare('DELETE FROM messages WHERE id = ?')->execute([(int)$_POST['id']]);
        $flash = 'Διαγράφηκε.';
    }
}

$filter = $_GET['filter'] ?? 'all';
$where = ''; $params = [];
if (in_array($filter, ['new','read','replied','archived'], true)) { $where = 'WHERE status = ?'; $params[] = $filter; }

$view_id = (int)($_GET['id'] ?? 0);
$view = null;
if ($view_id) {
    $s = $pdo->prepare("SELECT m.*, p.name AS program_name
                        FROM messages m LEFT JOIN programs p ON p.id = m.program_id
                        WHERE m.id = ?");
    $s->execute([$view_id]); $view = $s->fetch();
    if ($view && $view['status'] === 'new') {
        $pdo->prepare("UPDATE messages SET status = 'read' WHERE id = ?")->execute([$view_id]);
        $view['status'] = 'read';
    }
}

$rows = $pdo->prepare("SELECT id, name, phone, email, status, created_at, body FROM messages $where ORDER BY created_at DESC LIMIT 200");
$rows->execute($params); $rows = $rows->fetchAll();

$counts = $pdo->query("SELECT status, COUNT(*) c FROM messages GROUP BY status")->fetchAll();
$countMap = ['new'=>0,'read'=>0,'replied'=>0,'archived'=>0];
foreach ($counts as $r) $countMap[$r['status']] = (int)$r['c'];

include __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-wrap">
  <h1>Μηνύματα</h1>
  <?php if ($flash): ?><p class="alert alert-success"><?= e($flash) ?></p><?php endif; ?>
  <nav class="pill-nav">
    <a href="?filter=all"      class="<?= $filter==='all'?'is-active':'' ?>">Όλα</a>
    <a href="?filter=new"      class="<?= $filter==='new'?'is-active':'' ?>">Νέα (<?= $countMap['new'] ?>)</a>
    <a href="?filter=read"     class="<?= $filter==='read'?'is-active':'' ?>">Διαβασμένα (<?= $countMap['read'] ?>)</a>
    <a href="?filter=replied"  class="<?= $filter==='replied'?'is-active':'' ?>">Απαντημένα (<?= $countMap['replied'] ?>)</a>
    <a href="?filter=archived" class="<?= $filter==='archived'?'is-active':'' ?>">Αρχείο (<?= $countMap['archived'] ?>)</a>
  </nav>
  <div class="messages-layout">
    <div class="messages-list">
      <?php foreach ($rows as $m): ?>
        <a href="?filter=<?= e($filter) ?>&id=<?= (int)$m['id'] ?>"
           class="msg-item <?= $view && (int)$view['id']===(int)$m['id']?'is-active':'' ?> status-<?= e($m['status']) ?>">
          <div class="msg-top">
            <strong><?= e($m['name']) ?></strong>
            <span class="muted small"><?= e(date('d/m H:i', strtotime($m['created_at']))) ?></span>
          </div>
          <div class="muted small"><?= e($m['phone'] ?: $m['email']) ?></div>
          <p class="msg-snippet"><?= e(mb_strimwidth($m['body'], 0, 90, '…', 'UTF-8')) ?></p>
        </a>
      <?php endforeach; ?>
      <?php if (!$rows): ?><p class="muted p-6">Δεν υπάρχουν μηνύματα.</p><?php endif; ?>
    </div>
    <div class="messages-view">
      <?php if ($view): ?>
        <div class="view-head">
          <h2><?= e($view['name']) ?></h2>
          <span class="pill pill-status-<?= e($view['status']) ?>"><?= e($view['status']) ?></span>
        </div>
        <p class="muted"><?= e(date('l d M Y, H:i', strtotime($view['created_at']))) ?></p>
        <dl class="kv">
          <?php if ($view['phone']): ?><dt>Τηλέφωνο</dt><dd><a href="tel:<?= e($view['phone']) ?>"><?= e($view['phone']) ?></a></dd><?php endif; ?>
          <?php if ($view['email']): ?><dt>Email</dt><dd><a href="mailto:<?= e($view['email']) ?>"><?= e($view['email']) ?></a></dd><?php endif; ?>
          <?php if ($view['program_name']): ?><dt>Τμήμα</dt><dd><?= e($view['program_name']) ?></dd><?php endif; ?>
          <?php if ($view['preferred_datetime']): ?><dt>Προτ. ημ/νία</dt><dd><?= e(date('d/m/Y H:i', strtotime($view['preferred_datetime']))) ?></dd><?php endif; ?>
        </dl>
        <div class="msg-body"><?= nl2br(e($view['body'])) ?></div>
        <form method="post" class="view-actions">
          <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
          <input type="hidden" name="id" value="<?= (int)$view['id'] ?>">
          <select name="status">
            <?php foreach (['new'=>'Νέο','read'=>'Διαβασμένο','replied'=>'Απαντημένο','archived'=>'Αρχείο'] as $k=>$lbl): ?>
              <option value="<?= e($k) ?>" <?= $view['status']===$k?'selected':'' ?>><?= e($lbl) ?></option>
            <?php endforeach; ?>
          </select>
          <button name="op" value="status" class="btn btn-primary">Αποθήκευση</button>
          <button name="op" value="delete" class="btn link-danger" onclick="return confirm('Διαγραφή;')">Διαγραφή</button>
        </form>
      <?php else: ?><p class="muted center">Διάλεξε ένα μήνυμα.</p><?php endif; ?>
    </div>
  </div>
</div>
<?php include __DIR__ . '/includes/admin_footer.php'; ?>
