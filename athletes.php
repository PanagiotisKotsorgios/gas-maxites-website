<?php
require_once __DIR__ . '/includes/db.php';
$page_title = 'Οι Αθλητές μας — ' . SITE_NAME;
$active = 'athletes';
$athletes = db()->query("SELECT * FROM athletes WHERE active = 1 ORDER BY sort_order, id")->fetchAll();

$def_bg = 'https://images.unsplash.com/photo-1594381898411-846e7d193883?ixlib=rb-4.0.3&auto=format&fit=crop&w=1800&q=80';
$def_ph = 'https://images.unsplash.com/photo-1594381898411-846e7d193883?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';

include __DIR__ . '/includes/header.php';
?>
<section class="page-hero" style="background-image:url('<?= e(setting('athletes_page_bg', $def_bg)) ?>');">
  <div class="page-hero-overlay"></div>
  <div class="wrap">
    <p class="eyebrow eyebrow-light"><i class="fa-solid fa-user-ninja"></i> <?= e(setting('athletes_page_eyebrow', 'Οι Αθλητές μας')) ?></p>
    <h1><?= e(setting('athletes_page_title', 'Οι Μαχητές')) ?></h1>
    <p class="lead lead-light"><?= e(setting('athletes_page_lead', 'Οι αθλητές που τιμούν τον σύλλογο στο ταπί.')) ?></p>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <?php if ($athletes): ?>
      <div class="athletes-grid athletes-grid-lg">
        <?php foreach ($athletes as $a): ?>
          <a class="athlete-card" href="<?= SITE_URL ?>/athlete.php?slug=<?= e($a['slug']) ?>">
            <div class="ath-photo" style="background-image:url('<?= $a['photo'] ? SITE_URL . '/uploads/athletes/' . e($a['photo']) : $def_ph ?>');"></div>
            <div class="ath-body">
              <?php if ($a['belt']): ?><span class="belt-badge belt-<?= e(strtolower(preg_replace('/\s+/', '-', $a['belt']))) ?>"><?= e($a['belt']) ?></span><?php endif; ?>
              <h3><?= e($a['name']) ?></h3>
              <?php if ($a['weight_category']): ?><p class="muted-sm"><?= e($a['weight_category']) ?></p><?php endif; ?>
              <?php if ($a['age_group']): ?><p class="muted-sm"><i class="fa-solid fa-user-group"></i> <?= e($a['age_group']) ?></p><?php endif; ?>
              <div class="ath-record">
                <span class="rec-w"><?= (int)$a['wins'] ?>W</span>
                <span class="rec-l"><?= (int)$a['losses'] ?>L</span>
                <span class="rec-d"><?= (int)$a['draws'] ?>D</span>
              </div>
              <span class="link-arrow">Προφίλ αθλητή <i class="fa-solid fa-arrow-right"></i></span>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="empty">Οι αθλητές μας θα εμφανιστούν εδώ.</p>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
