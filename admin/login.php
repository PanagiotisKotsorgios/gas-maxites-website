<?php
require_once __DIR__ . '/../includes/auth.php';
if (current_user()) { header('Location: ' . SITE_URL . '/admin/index.php'); exit; }
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $u = trim((string)($_POST['username'] ?? ''));
    $p = (string)($_POST['password'] ?? '');
    if (login($u, $p)) { header('Location: ' . SITE_URL . '/admin/index.php'); exit; }
    $error = 'Λάθος όνομα χρήστη ή κωδικός.';
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Σύνδεση — <?= e(SITE_NAME) ?></title>
<link rel="icon" type="image/jpeg" href="<?= SITE_URL ?>/assets/img/logo.jpg">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/admin.css">
</head>
<body class="login-body">
  <form method="post" class="login-card">
    <img src="<?= SITE_URL ?>/assets/img/logo.jpg" alt="" class="login-logo">
    <h1>ΜΑΧΗΤΕΣ · Διαχείριση</h1>
    <p class="muted">Σύνδεση διαχειριστή</p>
    <?php if ($error): ?><p class="alert alert-error"><?= e($error) ?></p><?php endif; ?>
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <label>Όνομα χρήστη<input type="text" name="username" autofocus required></label>
    <label>Κωδικός<input type="password" name="password" required></label>
    <button type="submit" class="btn btn-primary">Σύνδεση</button>
    <p class="muted small"><a href="<?= SITE_URL ?>/"><i class="fa-solid fa-arrow-left"></i> Επιστροφή στον ιστότοπο</a></p>
  </form>
</body>
</html>
