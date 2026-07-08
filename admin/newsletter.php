<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();
$page_title = 'Newsletter — Μαχητές Admin';
$active = 'newsletter';
$pdo = db();
$flash = null;

if (($_GET['export'] ?? '') === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="newsletter-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputs($out, "\xEF\xBB\xBF");
    fputcsv($out, ['id','email','ip','confirmed','created_at']);
    foreach ($pdo->query('SELECT id,email,ip,confirmed,created_at FROM newsletter_subscribers ORDER BY id') as $r) fputcsv($out, $r);
    fclose($out); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (($_POST['op'] ?? '') === 'delete') {
        $pdo->prepare('DELETE FROM newsletter_subscribers WHERE id = ?')->execute([(int)$_POST['id']]);
        $flash = 'Ο συνδρομητής διαγράφηκε.';
    }
}

$rows = $pdo->query('SELECT * FROM newsletter_subscribers ORDER BY created_at DESC')->fetchAll();
include __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-wrap">
  <div class="page-head">
    <h1>Newsletter <span class="muted small">(<?= count($rows) ?> συνδρομητές)</span></h1>
    <a class="btn btn-ghost" href="?export=csv"><i class="fa-solid fa-file-csv"></i> Εξαγωγή CSV</a>
  </div>
  <?php if ($flash): ?><p class="alert alert-success"><?= e($flash) ?></p><?php endif; ?>
  <section class="panel">
    <table class="admin-table">
      <thead><tr><th>Email</th><th>IP</th><th>Ημερομηνία</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><a href="mailto:<?= e($r['email']) ?>"><?= e($r['email']) ?></a></td>
            <td class="muted small"><?= e($r['ip'] ?? '') ?></td>
            <td><?= e(date('d/m/Y H:i', strtotime($r['created_at']))) ?></td>
            <td class="row-actions">
              <form method="post" style="display:inline" onsubmit="return confirm('Διαγραφή;')">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="op" value="delete">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button type="submit" class="link-danger">Διαγραφή</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="4" class="muted">Δεν υπάρχουν συνδρομητές ακόμη.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </section>
</div>
<?php include __DIR__ . '/includes/admin_footer.php'; ?>
