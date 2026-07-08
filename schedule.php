<?php
require_once __DIR__ . '/includes/db.php';
$page_title = 'Πρόγραμμα Προπονήσεων — ' . SITE_NAME;
$active = 'schedule';

$days_labels = [1=>'Δευτέρα',2=>'Τρίτη',3=>'Τετάρτη',4=>'Πέμπτη',5=>'Παρασκευή',6=>'Σάββατο',7=>'Κυριακή'];
$rows = db()->query("SELECT * FROM schedule WHERE active = 1 ORDER BY day_of_week, sort_order, id")->fetchAll();
$by_day = [];
foreach ($rows as $r) $by_day[(int)$r['day_of_week']][] = $r;

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
    </header>
    <?php if ($rows): ?>
      <div class="schedule-grid">
        <?php foreach ($days_labels as $d => $lbl): ?>
          <div class="sch-day <?= empty($by_day[$d])?'is-empty':'' ?>">
            <h3><?= e($lbl) ?></h3>
            <?php if (empty($by_day[$d])): ?>
              <p class="muted-sm">—</p>
            <?php else: ?>
              <ul>
                <?php foreach ($by_day[$d] as $s): ?>
                  <li>
                    <span class="sch-time"><?= e($s['time_range']) ?></span>
                    <span class="sch-group"><?= e($s['group_name']) ?></span>
                    <?php if ($s['age_range']): ?><span class="sch-age"><?= e($s['age_range']) ?></span><?php endif; ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
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
          <?php if (!empty($p['image'])): ?>
            <div class="prog-row-img" style="background-image:url('<?= SITE_URL ?>/uploads/programs/<?= e($p['image']) ?>');"></div>
          <?php elseif (!empty($p['icon'])): ?>
            <div class="prog-row-ic"><i class="<?= e($p['icon']) ?>"></i></div>
          <?php endif; ?>
          <div>
            <h3><?= e($p['name']) ?></h3>
            <?php if ($p['age_range']): ?><p class="prog-age"><i class="fa-solid fa-user-group"></i> <?= e($p['age_range']) ?></p><?php endif; ?>
            <?php if ($p['description']): ?><p><?= nl2br(e($p['description'])) ?></p><?php endif; ?>
          </div>
          <div class="program-meta">
            <?php if ($p['monthly_fee'] !== null): ?>
              <span class="fee"><?= number_format((float)$p['monthly_fee'], 0) ?>€<small>/μήνα</small></span>
            <?php endif; ?>
            <a class="btn btn-small" href="<?= SITE_URL ?>/contact.php?program=<?= (int)$p['id'] ?>">Εγγραφή</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
