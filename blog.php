<?php
require_once __DIR__ . '/includes/db.php';
$page_title = 'Νέα &amp; Άρθρα — ' . SITE_NAME;
$active = 'blog';
$posts = db()->query("SELECT slug, title, excerpt, cover_image, created_at
                      FROM posts WHERE published = 1 ORDER BY created_at DESC")->fetchAll();

$def_bg   = 'https://images.unsplash.com/photo-1518611012118-696072aa579a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1800&q=80';
$def_card = 'https://images.unsplash.com/photo-1555597673-b21d5c935865?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';

include __DIR__ . '/includes/header.php';
?>
<section class="page-hero" style="background-image:url('<?= e(setting('blog_page_bg', $def_bg)) ?>');">
  <div class="page-hero-overlay"></div>
  <div class="wrap">
    <p class="eyebrow eyebrow-light"><i class="fa-solid fa-newspaper"></i> <?= e(setting('blog_page_eyebrow', 'Blog')) ?></p>
    <h1><?= e(setting('blog_page_title', 'Νέα & άρθρα')) ?></h1>
    <p class="lead lead-light"><?= e(setting('blog_page_lead', 'Ενημερώσεις για αγώνες, ανακοινώσεις και ιστορίες από τους Μαχητές.')) ?></p>
  </div>
</section>
<section class="section">
  <div class="wrap">
    <?php if ($posts): ?>
      <div class="cards-grid">
        <?php foreach ($posts as $p): ?>
          <a class="card post-card" href="<?= SITE_URL ?>/post.php?slug=<?= e($p['slug']) ?>">
            <?php if ($p['cover_image']): ?>
              <img src="<?= SITE_URL ?>/uploads/posts/<?= e($p['cover_image']) ?>" alt="" loading="lazy">
            <?php else: ?>
              <img src="<?= e($def_card) ?>" alt="" loading="lazy">
            <?php endif; ?>
            <div class="post-body">
              <time><i class="fa-regular fa-calendar"></i> <?= e(date('d/m/Y', strtotime($p['created_at']))) ?></time>
              <h3><?= e($p['title']) ?></h3>
              <?php if ($p['excerpt']): ?><p><?= e($p['excerpt']) ?></p><?php endif; ?>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="empty">Δεν έχουν δημοσιευτεί άρθρα ακόμη.</p>
    <?php endif; ?>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
