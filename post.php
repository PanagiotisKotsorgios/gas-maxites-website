<?php
require_once __DIR__ . '/includes/db.php';

$slug = (string)($_GET['slug'] ?? '');
$s = db()->prepare("SELECT * FROM posts WHERE slug = ? AND published = 1");
$s->execute([$slug]);
$post = $s->fetch();

if (!$post) {
    http_response_code(404);
    $page_title = 'Δεν βρέθηκε — ' . SITE_NAME;
    $active = 'blog';
    include __DIR__ . '/includes/header.php';
    echo '<section class="section"><div class="wrap"><h1>404</h1><p>Το άρθρο δεν βρέθηκε.</p><p><a href="' . SITE_URL . '/blog.php"><i class="fa-solid fa-arrow-left"></i> Πίσω στα άρθρα</a></p></div></section>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$page_title = $post['title'] . ' — ' . SITE_NAME;
$page_desc  = $post['excerpt'] ?: $page_title;
$active = 'blog';
include __DIR__ . '/includes/header.php';
?>
<article class="post">
  <?php if ($post['cover_image']): ?>
    <div class="post-cover">
      <img src="<?= SITE_URL ?>/uploads/posts/<?= e($post['cover_image']) ?>" alt="">
    </div>
  <?php endif; ?>
  <div class="wrap post-wrap">
    <p class="eyebrow"><i class="fa-solid fa-newspaper"></i> Άρθρο</p>
    <h1><?= e($post['title']) ?></h1>
    <p class="post-date"><i class="fa-regular fa-calendar"></i> <?= e(date('d M Y', strtotime($post['created_at']))) ?></p>
    <div class="post-body-copy">
      <?= $post['body'] /* trusted HTML from admin rich editor */ ?>
    </div>
    <p class="post-back"><a href="<?= SITE_URL ?>/blog.php"><i class="fa-solid fa-arrow-left"></i> Όλα τα άρθρα</a></p>
  </div>
</article>
<?php include __DIR__ . '/includes/footer.php'; ?>
