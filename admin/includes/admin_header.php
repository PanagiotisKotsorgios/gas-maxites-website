<?php
require_once __DIR__ . '/../../includes/auth.php';
require_admin();
$page_title = $page_title ?? 'Διαχείριση — ' . SITE_NAME;
$active = $active ?? '';
?>
<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($page_title) ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
<link rel="icon" type="image/jpeg" href="<?= SITE_URL ?>/assets/img/logo.jpg">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/admin.css">
</head>
<body>
<header class="admin-top">
  <div class="admin-brand">
    <a href="<?= SITE_URL ?>/admin/index.php">
      <img src="<?= SITE_URL ?>/assets/img/logo.jpg" alt="ΜΑΧΗΤΕΣ" class="admin-logo">
      <span>Admin</span>
    </a>
  </div>
  <nav class="admin-nav">
    <a href="<?= SITE_URL ?>/admin/index.php"      class="<?= $active==='dash'?'is-active':'' ?>"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
    <a href="<?= SITE_URL ?>/admin/posts.php"      class="<?= $active==='posts'?'is-active':'' ?>"><i class="fa-solid fa-newspaper"></i> Άρθρα</a>
    <a href="<?= SITE_URL ?>/admin/athletes.php"   class="<?= $active==='athletes'?'is-active':'' ?>"><i class="fa-solid fa-user-ninja"></i> Αθλητές</a>
    <a href="<?= SITE_URL ?>/admin/trophies.php"   class="<?= $active==='trophies'?'is-active':'' ?>"><i class="fa-solid fa-trophy"></i> Τρόπαια</a>
    <a href="<?= SITE_URL ?>/admin/schedule.php"   class="<?= $active==='schedule'?'is-active':'' ?>"><i class="fa-solid fa-calendar-days"></i> Πρόγραμμα</a>
    <a href="<?= SITE_URL ?>/admin/programs.php"   class="<?= $active==='programs'?'is-active':'' ?>"><i class="fa-solid fa-people-group"></i> Τμήματα</a>
    <a href="<?= SITE_URL ?>/admin/gallery.php"    class="<?= $active==='gallery'?'is-active':'' ?>"><i class="fa-solid fa-images"></i> Gallery</a>
    <a href="<?= SITE_URL ?>/admin/messages.php"   class="<?= $active==='messages'?'is-active':'' ?>"><i class="fa-solid fa-envelope"></i> Μηνύματα</a>
    <a href="<?= SITE_URL ?>/admin/newsletter.php" class="<?= $active==='newsletter'?'is-active':'' ?>"><i class="fa-solid fa-paper-plane"></i> Newsletter</a>
    <a href="<?= SITE_URL ?>/admin/settings.php"   class="<?= $active==='settings'?'is-active':'' ?>"><i class="fa-solid fa-gear"></i> Ρυθμίσεις</a>
  </nav>
  <div class="admin-user">
    <a href="<?= SITE_URL ?>/" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> Ιστότοπος</a>
    <a href="<?= SITE_URL ?>/admin/logout.php" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Αποσύνδεση</a>
  </div>
</header>
<main class="admin-main">
