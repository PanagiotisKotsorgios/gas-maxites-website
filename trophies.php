<?php
require_once __DIR__ . '/includes/db.php';
$page_title = 'Τρόπαια &amp; Διακρίσεις — ' . SITE_NAME;
$active = 'trophies';
$trophies = db()->query("SELECT t.*, a.name AS athlete_name, a.slug AS athlete_slug
                         FROM trophies t LEFT JOIN athletes a ON a.id = t.athlete_id
                         ORDER BY t.achieved_on DESC, t.sort_order, t.id DESC")->fetchAll();

// Group by year
$grouped = [];
foreach ($trophies as $t) {
    $yr = $t['achieved_on'] ? date('Y', strtotime($t['achieved_on'])) : 'Χωρίς ημερομηνία';
    $grouped[$yr][] = $t;
}

$def_bg = 'https://images.unsplash.com/photo-1552674605-db6ffd4facb5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1800&q=80';

include __DIR__ . '/includes/header.php';
?>
<section class="page-hero" style="background-image:url('<?= e(setting('trophies_page_bg', $def_bg)) ?>');">
  <div class="page-hero-overlay"></div>
  <div class="wrap">
    <p class="eyebrow eyebrow-light"><i class="fa-solid fa-trophy"></i> <?= e(setting('trophies_page_eyebrow', 'Τρόπαια')) ?></p>
    <h1><?= e(setting('trophies_page_title', 'Τρόπαια & Διακρίσεις')) ?></h1>
    <p class="lead lead-light"><?= e(setting('trophies_page_lead', 'Οι νίκες μας σε πανελλήνια πρωταθλήματα και κύπελλα.')) ?></p>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <?php if ($grouped): ?>
      <?php foreach ($grouped as $year => $rows): ?>
        <section class="trophy-year">
          <h2 class="year-head"><i class="fa-solid fa-calendar"></i> <?= e((string)$year) ?></h2>
          <div class="trophies-grid trophies-grid-lg">
            <?php foreach ($rows as $t): ?>
              <div class="trophy-card">
                <?php if ($t['image']): ?>
                  <img src="<?= SITE_URL ?>/uploads/trophies/<?= e($t['image']) ?>" alt="">
                <?php else: ?>
                  <div class="trophy-ic"><i class="fa-solid fa-trophy"></i></div>
                <?php endif; ?>
                <h3><?= e($t['title']) ?></h3>
                <?php if ($t['event']): ?><p class="muted-sm"><?= e($t['event']) ?></p><?php endif; ?>
                <?php if ($t['description']): ?><p class="trophy-desc"><?= e($t['description']) ?></p><?php endif; ?>
                <div class="trophy-meta">
                  <?php if ($t['achieved_on']): ?><time><?= e(date('d M Y', strtotime($t['achieved_on']))) ?></time><?php endif; ?>
                  <?php if ($t['athlete_slug']): ?>
                    <a class="link-arrow" href="<?= SITE_URL ?>/athlete.php?slug=<?= e($t['athlete_slug']) ?>"><?= e($t['athlete_name']) ?> <i class="fa-solid fa-arrow-right"></i></a>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="empty">Τα τρόπαια του συλλόγου θα εμφανιστούν εδώ.</p>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
