<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();
$page_title = 'Dashboard — Μαχητές Admin';
$active = 'dash';
$pdo = db();

$stats = [
    'today'  => (int)$pdo->query("SELECT COUNT(*) FROM pageviews WHERE created_at >= CURDATE()")->fetchColumn(),
    'week'   => (int)$pdo->query("SELECT COUNT(*) FROM pageviews WHERE created_at >= (NOW() - INTERVAL 7 DAY)")->fetchColumn(),
    'month'  => (int)$pdo->query("SELECT COUNT(*) FROM pageviews WHERE created_at >= (NOW() - INTERVAL 30 DAY)")->fetchColumn(),
    'unique_week' => (int)$pdo->query("SELECT COUNT(DISTINCT ip_hash) FROM pageviews WHERE created_at >= (NOW() - INTERVAL 7 DAY)")->fetchColumn(),
    'msgs_new'    => (int)$pdo->query("SELECT COUNT(*) FROM messages WHERE status = 'new'")->fetchColumn(),
    'posts_total' => (int)$pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn(),
    'athletes'    => (int)$pdo->query("SELECT COUNT(*) FROM athletes")->fetchColumn(),
    'trophies'    => (int)$pdo->query("SELECT COUNT(*) FROM trophies")->fetchColumn(),
    'newsletter'  => (int)$pdo->query("SELECT COUNT(*) FROM newsletter_subscribers")->fetchColumn(),
];

$top_paths = $pdo->query(
    "SELECT path, COUNT(*) c FROM pageviews
     WHERE created_at >= (NOW() - INTERVAL 30 DAY)
     GROUP BY path ORDER BY c DESC LIMIT 10"
)->fetchAll();
$top_ref = $pdo->query(
    "SELECT referrer, COUNT(*) c FROM pageviews
     WHERE created_at >= (NOW() - INTERVAL 30 DAY) AND referrer <> ''
     GROUP BY referrer ORDER BY c DESC LIMIT 10"
)->fetchAll();

$daily = $pdo->query(
    "SELECT DATE(created_at) d, COUNT(*) c
     FROM pageviews WHERE created_at >= (CURDATE() - INTERVAL 13 DAY)
     GROUP BY d ORDER BY d"
)->fetchAll();
$daily_map = [];
foreach ($daily as $r) $daily_map[$r['d']] = (int)$r['c'];
$series = [];
for ($i = 13; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $series[$d] = $daily_map[$d] ?? 0;
}
$max = max($series) ?: 1;

include __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-wrap">
  <h1>Dashboard</h1>
  <div class="stat-grid">
    <div class="stat"><span class="stat-num"><?= $stats['today'] ?></span><span>Επισκέψεις σήμερα</span></div>
    <div class="stat"><span class="stat-num"><?= $stats['week'] ?></span><span>Τελευταίες 7 μέρες</span></div>
    <div class="stat"><span class="stat-num"><?= $stats['month'] ?></span><span>Τελευταίες 30 μέρες</span></div>
    <div class="stat"><span class="stat-num"><?= $stats['unique_week'] ?></span><span>Μοναδικοί (7d)</span></div>
    <div class="stat stat-alert"><span class="stat-num"><?= $stats['msgs_new'] ?></span><span>Νέα μηνύματα</span></div>
    <div class="stat"><span class="stat-num"><?= $stats['posts_total'] ?></span><span>Άρθρα</span></div>
    <div class="stat"><span class="stat-num"><?= $stats['athletes'] ?></span><span>Αθλητές</span></div>
    <div class="stat"><span class="stat-num"><?= $stats['trophies'] ?></span><span>Τρόπαια</span></div>
    <div class="stat"><span class="stat-num"><?= $stats['newsletter'] ?></span><span>Newsletter</span></div>
  </div>

  <section class="panel">
    <h2>Επισκέψεις — τελευταίες 14 μέρες</h2>
    <div class="sparkline">
      <?php foreach ($series as $d => $c): ?>
        <div class="bar" title="<?= e($d) ?>: <?= (int)$c ?>">
          <span style="height: <?= (int)round(($c / $max) * 100) ?>%"></span>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="sparkline-labels">
      <span><?= e(array_key_first($series)) ?></span>
      <span><?= e(array_key_last($series)) ?></span>
    </div>
  </section>

  <div class="two-panels">
    <section class="panel">
      <h2>Δημοφιλείς σελίδες (30d)</h2>
      <table class="admin-table">
        <thead><tr><th>Path</th><th>Επισκέψεις</th></tr></thead>
        <tbody>
          <?php foreach ($top_paths as $r): ?>
            <tr><td><?= e($r['path']) ?></td><td><?= (int)$r['c'] ?></td></tr>
          <?php endforeach; ?>
          <?php if (!$top_paths): ?><tr><td colspan="2" class="muted">Χωρίς δεδομένα.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </section>
    <section class="panel">
      <h2>Πηγές επισκεψιμότητας (30d)</h2>
      <table class="admin-table">
        <thead><tr><th>Referrer</th><th>Επισκέψεις</th></tr></thead>
        <tbody>
          <?php foreach ($top_ref as $r): ?>
            <tr><td><?= e(mb_strimwidth($r['referrer'], 0, 60, '…', 'UTF-8')) ?></td><td><?= (int)$r['c'] ?></td></tr>
          <?php endforeach; ?>
          <?php if (!$top_ref): ?><tr><td colspan="2" class="muted">Χωρίς δεδομένα.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </section>
  </div>
</div>
<?php include __DIR__ . '/includes/admin_footer.php'; ?>
