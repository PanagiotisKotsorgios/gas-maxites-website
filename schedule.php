<?php
require_once __DIR__ . '/includes/db.php';
$page_title = 'Πρόγραμμα Προπονήσεων — ' . SITE_NAME;
$active = 'schedule';

$days_labels = [1=>'Δευτέρα',2=>'Τρίτη',3=>'Τετάρτη',4=>'Πέμπτη',5=>'Παρασκευή',6=>'Σάββατο',7=>'Κυριακή'];
$days_short  = [1=>'Δευ',2=>'Τρί',3=>'Τετ',4=>'Πέμ',5=>'Παρ',6=>'Σάβ',7=>'Κυρ'];
$rows = db()->query("SELECT * FROM schedule WHERE active = 1 ORDER BY day_of_week, sort_order, id")->fetchAll();

// Build a matrix keyed by [time_range][day_of_week] = row.
// Time slots are ordered by start-time extracted from the range (e.g. "17:00-18:00").
$slots = [];              // time_range => start-time (for sorting)
$matrix = [];             // time_range => [day => [$row, ...]]
foreach ($rows as $r) {
    $tr = trim($r['time_range']);
    if ($tr === '') continue;
    if (!isset($slots[$tr])) {
        $start = '99:99';
        if (preg_match('~(\d{1,2})[:.](\d{2})~', $tr, $m)) {
            $start = str_pad($m[1], 2, '0', STR_PAD_LEFT) . ':' . $m[2];
        }
        $slots[$tr] = $start;
    }
    $matrix[$tr][(int)$r['day_of_week']][] = $r;
}
uasort($slots, fn($a,$b) => strcmp($a,$b));
$slot_keys = array_keys($slots);
$today_dow = (int)date('N'); // 1=Mon..7=Sun

$programs = db()->query("SELECT * FROM programs WHERE active = 1 ORDER BY sort_order, id")->fetchAll();

$def_bg = 'https://images.unsplash.com/photo-1552674605-db6ffd4facb5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1800&q=80';

include __DIR__ . '/includes/header.php';
?>
<section class="page-hero" style="background-image:url('<?= e(setting('schedule_page_bg', $def_bg)) ?>');">
  <div class="page-hero-overlay"></div>
  <div class="wrap">
    <p class="eyebrow eyebrow-light"><i class="fa-solid fa-calendar-days"></i> <?= e(setting('schedule_page_eyebrow', 'Πρόγραμμα')) ?></p>
    <h1><?= e(setting('schedule_page_title', 'Προπονήσεις & τμήματα')) ?></h1>
    <p class="lead lead-light"><?= e(setting('schedule_page_lead', 'Το εβδομαδιαίο πρόγραμμά μας και τα τμήματα ανά ηλικία και επίπεδο.')) ?></p>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <header class="section-head">
      <p class="eyebrow"><i class="fa-solid fa-clock"></i> <?= e(setting('schedule_week_eyebrow', 'Εβδομαδιαίο πρόγραμμα')) ?></p>
      <h2><?= e(setting('schedule_week_title', 'Ημέρες & ώρες προπονήσεων')) ?></h2>
      <?php $tip = trim(setting('schedule_week_note', 'Η στήλη της σημερινής ημέρας είναι επισημασμένη. Σύρετε πλάγια σε κινητό.')); if ($tip !== ''): ?>
        <p class="section-sub"><?= e($tip) ?></p>
      <?php endif; ?>
    </header>

    <?php if ($rows && $slot_keys): ?>
      <div class="sched-table-wrap">
        <table class="sched-table" aria-label="Εβδομαδιαίο πρόγραμμα">
          <thead>
            <tr>
              <th class="sched-th-time" scope="col"><i class="fa-solid fa-clock"></i> Ώρα</th>
              <?php foreach ($days_labels as $d => $lbl): ?>
                <th scope="col" class="<?= $d===$today_dow?'sched-today':'' ?>">
                  <span class="sched-day-full"><?= e($lbl) ?></span>
                  <span class="sched-day-short"><?= e($days_short[$d]) ?></span>
                </th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($slot_keys as $tr): ?>
              <tr>
                <th class="sched-time" scope="row"><?= e($tr) ?></th>
                <?php foreach ($days_labels as $d => $_lbl):
                  $cell = $matrix[$tr][$d] ?? [];
                ?>
                  <td class="<?= $d===$today_dow?'sched-today':'' ?> <?= empty($cell)?'is-empty':'' ?>">
                    <?php if (empty($cell)): ?>
                      <span class="sched-dash" aria-hidden="true">—</span>
                    <?php else: foreach ($cell as $s): ?>
                      <div class="sched-cell">
                        <strong class="sched-group"><?= e($s['group_name']) ?></strong>
                        <?php if ($s['age_range']): ?><span class="sched-age"><?= e($s['age_range']) ?></span><?php endif; ?>
                        <?php if (!empty($s['notes'])): ?><span class="sched-note"><i class="fa-solid fa-circle-info"></i> <?= e($s['notes']) ?></span><?php endif; ?>
                      </div>
                    <?php endforeach; endif; ?>
                  </td>
                <?php endforeach; ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="sched-legend">
        <span class="sched-legend-dot" aria-hidden="true"></span>
        <span>Σήμερα: <strong><?= e($days_labels[$today_dow]) ?></strong></span>
        <span class="sep">·</span>
        <span>Για εγγραφή: <a href="tel:<?= e(setting('phone2', '6937125755')) ?>"><i class="fa-solid fa-phone"></i> <?= e(setting('phone2', '6937125755')) ?></a></span>
      </div>
    <?php else: ?>
      <p class="empty">Το πρόγραμμα θα ανακοινωθεί σύντομα. Για πληροφορίες: <?= e(setting('phone2', '6937125755')) ?>.</p>
    <?php endif; ?>
  </div>
</section>

<?php if ($programs): ?>
<section class="section section-alt">
  <div class="wrap">
    <header class="section-head">
      <p class="eyebrow"><i class="fa-solid fa-people-group"></i> <?= e(setting('schedule_prog_eyebrow', 'Τμήματα')) ?></p>
      <h2><?= e(setting('schedule_prog_title', 'Επιλέξτε την ομάδα που ταιριάζει')) ?></h2>
    </header>
    <div class="programs-list">
      <?php foreach ($programs as $p): ?>
        <article class="program-row">
          <div class="prog-row-img" style="background-image:url('<?= e(program_image_url($p)) ?>');"></div>
          <div>
            <h3><?= e($p['name']) ?></h3>
            <?php if ($p['age_range']): ?><p class="prog-age"><i class="fa-solid fa-user-group"></i> <?= e($p['age_range']) ?></p><?php endif; ?>
            <?php if ($p['description']): ?><p><?= nl2br(e($p['description'])) ?></p><?php endif; ?>
          </div>
          <div class="program-meta">
            <a class="btn btn-small" href="<?= SITE_URL ?>/contact.php?program=<?= (int)$p['id'] ?>">Εγγραφή</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
