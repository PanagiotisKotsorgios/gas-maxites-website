<?php
require_once __DIR__ . '/includes/db.php';
$page_title = 'Gallery — ' . SITE_NAME;
$active = 'gallery';
$images = db()->query("SELECT * FROM gallery ORDER BY sort_order, id DESC")->fetchAll();

$def_bg = 'https://images.unsplash.com/photo-1544717305-2782549b5136?ixlib=rb-4.0.3&auto=format&fit=crop&w=1800&q=80';

include __DIR__ . '/includes/header.php';
?>
<section class="page-hero" style="background-image:url('<?= e(setting('gallery_page_bg', $def_bg)) ?>');">
  <div class="page-hero-overlay"></div>
  <div class="wrap">
    <p class="eyebrow eyebrow-light"><i class="fa-solid fa-camera"></i> <?= e(setting('gallery_page_eyebrow', 'Στιγμές')) ?></p>
    <h1><?= e(setting('gallery_page_title', 'Gallery')) ?></h1>
    <p class="lead lead-light"><?= e(setting('gallery_page_lead', 'Ο σύλλογος σε δράση — προπονήσεις, αγώνες, νίκες.')) ?></p>
  </div>
</section>
<section class="section">
  <div class="wrap">
    <?php if ($images): ?>
      <div class="gallery-grid">
        <?php foreach ($images as $img): ?>
          <a class="gallery-item"
             href="<?= SITE_URL ?>/uploads/gallery/<?= e($img['filename']) ?>"
             data-caption="<?= e($img['caption'] ?? '') ?>">
            <img src="<?= SITE_URL ?>/uploads/gallery/<?= e($img['filename']) ?>" alt="<?= e($img['caption'] ?? '') ?>" loading="lazy">
          </a>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="empty">Οι φωτογραφίες θα εμφανιστούν σύντομα.</p>
    <?php endif; ?>
  </div>
</section>
<div id="lightbox" class="lightbox" hidden>
  <button class="lb-close" aria-label="Κλείσιμο"><i class="fa-solid fa-xmark"></i></button>
  <img alt="">
  <p class="lb-caption"></p>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
