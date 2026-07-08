<?php
// One-shot installer for ΓΑΣ Μαχητές Μεσολογγίου. Delete after installing.
require_once __DIR__ . '/includes/db.php';

$already = file_exists(__DIR__ . '/.installed');
$done = false;
$errors = [];
$log = [];

/**
 * Idempotently add columns / tweak schema for older installs.
 */
function _apply_migrations(PDO $pdo, array &$log): void {
    // programs.image and programs.icon may be missing on older installs
    $cols = $pdo->query("SHOW COLUMNS FROM programs")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('image', $cols, true)) {
        $pdo->exec("ALTER TABLE programs ADD COLUMN image VARCHAR(255) DEFAULT NULL AFTER monthly_fee");
        $log[] = 'programs.image προστέθηκε.';
    }
    if (!in_array('icon', $cols, true)) {
        $pdo->exec("ALTER TABLE programs ADD COLUMN icon VARCHAR(64) DEFAULT NULL AFTER image");
        $log[] = 'programs.icon προστέθηκε.';
    }
}

if ($already && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['op'] ?? '') === 'migrate') {
    try {
        $pdo = db();
        $schema = file_get_contents(__DIR__ . '/sql/schema.sql');
        foreach (array_filter(array_map('trim', explode(';', $schema))) as $stmt) $pdo->exec($stmt);
        _apply_migrations($pdo, $log);
        @mkdir(__DIR__ . '/uploads/gallery', 0775, true);
        @mkdir(__DIR__ . '/uploads/posts', 0775, true);
        @mkdir(__DIR__ . '/uploads/athletes', 0775, true);
        @mkdir(__DIR__ . '/uploads/trophies', 0775, true);
        @mkdir(__DIR__ . '/uploads/programs', 0775, true);
        $log[] = 'Το σχήμα ενημερώθηκε.';
        $done = true;
    } catch (Throwable $ex) {
        $errors[] = 'Σφάλμα: ' . $ex->getMessage();
    }
}

if (!$already && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? 'admin');
    $password = (string)($_POST['password'] ?? '');
    if (mb_strlen($password) < 8) $errors[] = 'Ο κωδικός πρέπει να έχει τουλάχιστον 8 χαρακτήρες.';
    if ($username === '') $errors[] = 'Δώσε όνομα χρήστη.';

    if (!$errors) {
        try {
            $pdo = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            $log[] = 'Βάση: ' . DB_NAME;

            $pdo = db();
            $schema = file_get_contents(__DIR__ . '/sql/schema.sql');
            foreach (array_filter(array_map('trim', explode(';', $schema))) as $stmt) $pdo->exec($stmt);
            $log[] = 'Πίνακες δημιουργήθηκαν.';
            _apply_migrations($pdo, $log);

            $exists = $pdo->prepare('SELECT id FROM users WHERE username = ?');
            $exists->execute([$username]);
            if ($exists->fetch()) $log[] = 'Ο χρήστης "' . $username . '" υπάρχει.';
            else {
                $pdo->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)')
                    ->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
                $log[] = 'Διαχειριστής "' . $username . '" δημιουργήθηκε.';
            }

            // Defaults
            $defaults = [
                'hero_title'     => 'Δύναμη. Πειθαρχία. Σεβασμός.',
                'hero_subtitle'  => 'Ο Γ.Α.Σ. Μαχητές Μεσολογγίου είναι ένας ενεργός σύλλογος Taekwondo με τον δάσκαλο Σωτήρη Λιαμέτη, 7ου dan.',
                'about_title'    => 'Μια οικογένεια. Μια πολεμική τέχνη.',
                'about_body'     => '<p>Ο <strong>Γ.Α.Σ. Μαχητές Μεσολογγίου</strong> είναι ένας από τους πιο ενεργούς αθλητικούς συλλόγους Taekwondo της Αιτωλοακαρνανίας. Εδώ, από τα πρώτα βήματα ως και τους αγώνες, μαθαίνουμε ένα άθλημα που χτίζει χαρακτήρα.</p><p>Οι μαθητές μας συμμετέχουν σε <strong>Πανελλήνια Πρωταθλήματα</strong> και <strong>Κύπελλα</strong> — επιστρέφοντας με νέα εμπειρίες και τρόπαια.</p>',
                'master_name'    => 'Σωτήρης Λιαμέτης',
                'master_title'   => 'Δάσκαλος 7ου Dan · ETU TKD WTF',
                'master_bio'     => '',
                'master_photo'   => '',
                'history_body'   => '<p>Ο <strong>Γ.Α.Σ. Μαχητές Μεσολογγίου</strong> γεννήθηκε από την αγάπη για την πολεμική τέχνη του Taekwondo — μιας τέχνης που μπορεί να αλλάξει ζωές.</p>',
                'address'        => 'Γεωργίου Λιακατά 17, Μεσολόγγι 30200',
                'phone1'         => '2631055890',
                'phone2'         => '6937125755',
                'email'          => '',
                'whatsapp_number'=> '306937125755',
                'viber_number'   => '306937125755',
                'facebook_url'   => 'https://facebook.com/liametisswtirismaxites1',
                'instagram_url'  => 'https://instagram.com/liametisswtirismaxites1',
                'tiktok_url'     => '',
                'youtube_url'    => '',
                'twitter_url'    => '',
                'google_reviews_url' => 'https://www.google.com/search?q=%CE%9C%CE%91%CE%A7%CE%97%CE%A4%CE%95%CE%A3+%CE%9C%CE%B5%CF%83%CE%BF%CE%BB%CE%BF%CE%B3%CE%B3%CE%AF%CE%BF%CF%85',
                'google_maps_url'    => 'https://www.google.com/maps?q=' . rawurlencode('Γεωργίου Λιακατά 17, Μεσολόγγι'),
                'footer_tagline' => 'Γυμναστικός Αθλητικός Σύλλογος Μαχητές Μεσολογγίου. Δύναμη, Πειθαρχία, Σεβασμός.',
            ];
            $up = $pdo->prepare('INSERT IGNORE INTO settings (`key`,`value`) VALUES (?,?)');
            foreach ($defaults as $k => $v) $up->execute([$k, $v]);
            $log[] = 'Ρυθμίσεις.';

            // Seed programs
            if ((int)$pdo->query('SELECT COUNT(*) FROM programs')->fetchColumn() === 0) {
                $ins = $pdo->prepare('INSERT INTO programs (name,description,age_range,monthly_fee,icon,sort_order,active) VALUES (?,?,?,?,?,?,1)');
                $seed = [
                    ['Παιδικό αρχαρίων', 'Οι πρώτες τεχνικές, μέσα από παιχνίδι και πειθαρχία.', '4-8 ετών', 35, 'fa-solid fa-child-reaching', 10],
                    ['Παιδικό προχωρημένων', 'Συστηματική προπόνηση, poomsae και πρώτοι αγώνες.', '8-12 ετών', 40, 'fa-solid fa-hand-fist', 20],
                    ['Εφηβικό', 'Δύναμη, τεχνική και προετοιμασία για αγωνιστική δράση.', '12-16 ετών', 45, 'fa-solid fa-bolt', 30],
                    ['Ενήλικες', 'Για αρχάριους και έμπειρους. Ευεξία, αυτοάμυνα, φόρμα.', '16+ ετών', 50, 'fa-solid fa-user-ninja', 40],
                    ['Αγωνιστικό τμήμα', 'Εξειδικευμένη προετοιμασία για Πανελλήνια πρωταθλήματα.', 'Επιλεγμένοι αθλητές', 60, 'fa-solid fa-trophy', 50],
                    ['Αυτοάμυνα', 'Πραγματικές τεχνικές αυτοάμυνας για την καθημερινότητα.', 'Ενήλικες', 45, 'fa-solid fa-shield-halved', 60],
                ];
                foreach ($seed as $r) $ins->execute($r);
                $log[] = 'Προεπιλεγμένα τμήματα.';
            }

            // Seed schedule
            if ((int)$pdo->query('SELECT COUNT(*) FROM schedule')->fetchColumn() === 0) {
                $ins = $pdo->prepare('INSERT INTO schedule (day_of_week,time_range,group_name,age_range,sort_order,active) VALUES (?,?,?,?,?,1)');
                $seed = [
                    [1,'17:00-18:00','Παιδικό αρχαρίων','4-8',10],
                    [1,'18:00-19:00','Παιδικό προχωρημένων','8-12',20],
                    [1,'19:00-20:30','Εφηβικό / Ενήλικες','12+',30],
                    [3,'17:00-18:00','Παιδικό αρχαρίων','4-8',10],
                    [3,'18:00-19:00','Παιδικό προχωρημένων','8-12',20],
                    [3,'19:00-20:30','Αγωνιστικό','επιλεγμένοι',30],
                    [5,'17:00-18:00','Παιδικό αρχαρίων','4-8',10],
                    [5,'18:00-19:00','Παιδικό προχωρημένων','8-12',20],
                    [5,'19:00-20:30','Εφηβικό / Ενήλικες','12+',30],
                    [6,'10:00-11:30','Οικογενειακό','όλες τις ηλικίες',10],
                ];
                foreach ($seed as $r) $ins->execute($r);
                $log[] = 'Προεπιλεγμένο πρόγραμμα.';
            }

            @mkdir(__DIR__ . '/uploads/gallery',  0775, true);
            @mkdir(__DIR__ . '/uploads/posts',    0775, true);
            @mkdir(__DIR__ . '/uploads/athletes', 0775, true);
            @mkdir(__DIR__ . '/uploads/trophies', 0775, true);
            @mkdir(__DIR__ . '/uploads/programs', 0775, true);
            $log[] = 'Φάκελοι uploads έτοιμοι.';

            file_put_contents(__DIR__ . '/.installed', date('c'));
            $done = true;
        } catch (Throwable $ex) {
            $errors[] = 'Σφάλμα: ' . $ex->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<title>Εγκατάσταση — ΓΑΣ Μαχητές</title>
<link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="login-body">
  <div class="login-card" style="width:min(560px,94vw); text-align:left">
    <h1 style="text-align:center">Εγκατάσταση ΓΑΣ Μαχητές</h1>

    <?php if ($done): ?>
      <p class="alert alert-success">Ολοκληρώθηκε.</p>
      <?php if ($log): ?><ul class="muted small"><?php foreach ($log as $l) echo '<li>' . e($l) . '</li>'; ?></ul><?php endif; ?>
      <p><strong>Για ασφάλεια, διέγραψε τώρα το <code>install.php</code>.</strong></p>
      <p>
        <a class="btn btn-primary" href="<?= SITE_URL ?>/admin/login.php">Είσοδος</a>
        <a class="btn btn-ghost" href="<?= SITE_URL ?>/">Δες τον ιστότοπο</a>
      </p>
    <?php elseif ($already): ?>
      <p class="alert">Το site είναι ήδη εγκατεστημένο. Μπορείς να ενημερώσεις το σχήμα για τυχόν νέους πίνακες.</p>
      <?php foreach ($errors as $er): ?><p class="alert alert-error"><?= e($er) ?></p><?php endforeach; ?>
      <form method="post">
        <input type="hidden" name="op" value="migrate">
        <button class="btn btn-primary" type="submit">Ενημέρωση σχήματος</button>
      </form>
    <?php else: ?>
      <p class="muted">Δημιούργησε τη βάση, τους πίνακες και τον πρώτο διαχειριστή.</p>
      <?php foreach ($errors as $er): ?><p class="alert alert-error"><?= e($er) ?></p><?php endforeach; ?>
      <form method="post">
        <label>Όνομα χρήστη διαχειριστή<input type="text" name="username" value="admin" required></label>
        <label>Κωδικός (≥8 χαρακτήρες)<input type="password" name="password" required minlength="8"></label>
        <button class="btn btn-primary" type="submit" style="width:100%">Ξεκίνα εγκατάσταση</button>
      </form>
      <p class="muted small">Έλεγξε πρώτα τα στοιχεία βάσης στο <code>includes/config.php</code>.</p>
    <?php endif; ?>
  </div>
</body>
</html>
