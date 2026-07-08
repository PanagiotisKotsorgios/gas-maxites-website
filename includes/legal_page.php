<?php
require_once __DIR__ . '/db.php';
$active = $active ?? '';
$page_desc = $page_desc ?? $legal_title;
include __DIR__ . '/header.php';
?>
<section class="page-hero legal-hero" style="background-image:url('https://images.unsplash.com/photo-1518611012118-696072aa579a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1800&q=80');">
  <div class="page-hero-overlay"></div>
  <div class="wrap">
    <p class="eyebrow eyebrow-light"><i class="fa-solid fa-<?= e($legal_icon ?? 'file-lines') ?>"></i> Νομικά</p>
    <h1><?= e($legal_title) ?></h1>
    <p class="lead lead-light">Τελευταία ενημέρωση: <?= e($legal_updated ?? date('d/m/Y')) ?></p>
  </div>
</section>
<section class="section">
  <div class="wrap legal-wrap">
    <aside class="legal-nav">
      <h4>Νομικά</h4>
      <ul>
        <li><a href="<?= SITE_URL ?>/terms.php" class="<?= ($active==='terms')?'is-active':'' ?>"><i class="fa-solid fa-file-contract"></i> Όροι Χρήσης</a></li>
        <li><a href="<?= SITE_URL ?>/privacy.php" class="<?= ($active==='privacy')?'is-active':'' ?>"><i class="fa-solid fa-user-shield"></i> Πολιτική Απορρήτου</a></li>
        <li><a href="<?= SITE_URL ?>/cookies.php" class="<?= ($active==='cookies')?'is-active':'' ?>"><i class="fa-solid fa-cookie-bite"></i> Πολιτική Cookies</a></li>
      </ul>
    </aside>
    <article class="legal-body">
      <?= $legal_body ?>
    </article>
  </div>
</section>
<?php include __DIR__ . '/footer.php'; ?>
