<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/analytics.php';
require_once __DIR__ . '/social.php';
log_pageview();

$page_title  = $page_title  ?? SITE_NAME;
$page_desc   = $page_desc   ?? 'Γυμναστικός Αθλητικός Σύλλογος Μαχητές Μεσολογγίου — Taekwondo · Δύναμη, Πειθαρχία, Σεβασμός.';
$active      = $active      ?? '';
?>
<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($page_title) ?></title>
<meta name="description" content="<?= e($page_desc) ?>">
<meta property="og:title" content="<?= e($page_title) ?>">
<meta property="og:description" content="<?= e($page_desc) ?>">
<meta property="og:type" content="website">
<meta property="og:image" content="<?= SITE_URL ?>/assets/img/logo.jpg">
<meta name="theme-color" content="#0A1224">
<link rel="icon" type="image/jpeg" href="<?= SITE_URL ?>/assets/img/logo.jpg">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="topbar">
  <div class="wrap topbar-inner">
    <div class="topbar-left">
      <span><i class="fa-solid fa-location-dot"></i> <?= e(setting('address', 'Γεωργίου Λιακατά 17, Μεσολόγγι 30200')) ?></span>
      <?php $__tp = trim(setting('topbar_hours')); if ($__tp !== ''): ?>
        <span class="sep">·</span>
        <span><i class="fa-solid fa-clock"></i> <?= e($__tp) ?></span>
      <?php endif; ?>
    </div>
    <div class="topbar-right">
      <?php $p2 = trim(setting('phone2')); if ($p2 !== ''): ?>
        <a href="tel:<?= e($p2) ?>"><i class="fa-solid fa-phone"></i> <?= e($p2) ?></a>
      <?php endif; ?>
      <?php foreach (social_links() as [$label, $url, $icon]): ?>
        <a href="<?= e($url) ?>" target="_blank" rel="noopener" aria-label="<?= e($label) ?>"><i class="<?= e($icon) ?>"></i></a>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<header class="site-header">
  <div class="wrap header-inner">
    <a class="brand" href="<?= SITE_URL ?>/">
      <img src="<?= SITE_URL ?>/assets/img/logo.jpg" alt="<?= e(SITE_NAME) ?>" class="brand-logo">
      <span class="brand-text">
        <strong><?= e(SITE_NAME_SHORT) ?></strong>
        <em>Taekwondo Μεσολόγγι</em>
      </span>
    </a>
    <button class="nav-toggle" aria-label="Μενού" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
    <nav class="site-nav">
      <a href="<?= SITE_URL ?>/"             class="<?= $active==='home'?'is-active':'' ?>">Αρχική</a>
      <div class="has-dropdown">
        <a href="<?= SITE_URL ?>/about.php" class="<?= in_array($active,['about','history','master'])?'is-active':'' ?>">Ο Σύλλογος <i class="fa-solid fa-chevron-down"></i></a>
        <div class="dropdown">
          <a href="<?= SITE_URL ?>/about.php">Ποιοι είμαστε</a>
          <a href="<?= SITE_URL ?>/history.php">Η ιστορία μας</a>
          <a href="<?= SITE_URL ?>/master.php">Ο δάσκαλος</a>
        </div>
      </div>
      <div class="has-dropdown">
        <a href="<?= SITE_URL ?>/athletes.php" class="<?= in_array($active,['athletes','athlete','trophies'])?'is-active':'' ?>">Αθλητές <i class="fa-solid fa-chevron-down"></i></a>
        <div class="dropdown">
          <a href="<?= SITE_URL ?>/athletes.php">Οι αθλητές μας</a>
          <a href="<?= SITE_URL ?>/trophies.php">Τρόπαια &amp; διακρίσεις</a>
        </div>
      </div>
      <a href="<?= SITE_URL ?>/schedule.php" class="<?= $active==='schedule'?'is-active':'' ?>">Πρόγραμμα</a>
      <a href="<?= SITE_URL ?>/gallery.php"  class="<?= $active==='gallery'?'is-active':'' ?>">Gallery</a>
      <a href="<?= SITE_URL ?>/blog.php"     class="<?= $active==='blog'?'is-active':'' ?>">Νέα</a>
      <a href="<?= SITE_URL ?>/contact.php"  class="<?= $active==='contact'?'is-active':'' ?>">Επικοινωνία</a>
      <a class="btn btn-book" href="<?= SITE_URL ?>/contact.php"><i class="fa-solid fa-user-plus"></i> Εγγραφή</a>
    </nav>
  </div>
</header>
<main>
