<?php
require_once __DIR__ . '/includes/db.php';

$page_title = 'Επικοινωνία — ' . SITE_NAME;
$active = 'contact';
$sent = false;
$errors = [];
$form = [
    'name' => '', 'phone' => '', 'email' => '',
    'program_id' => (int)($_GET['program'] ?? 0) ?: '',
    'preferred_datetime' => '', 'body' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['op'] ?? '') !== 'newsletter') {
    foreach ($form as $k => $_) {
        $form[$k] = trim((string)($_POST[$k] ?? ''));
    }
    if (!empty($_POST['website'])) {
        $sent = true;
    } else {
        if ($form['name'] === '') $errors[] = 'Παρακαλώ συμπλήρωσε το όνομά σου.';
        if ($form['body'] === '') $errors[] = 'Παρακαλώ γράψε το μήνυμά σου.';
        if ($form['phone'] === '' && $form['email'] === '') $errors[] = 'Άφησέ μας τηλέφωνο ή email για επικοινωνία.';
        if ($form['email'] !== '' && !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Το email δεν φαίνεται σωστό.';

        if (!$errors) {
            $dt = null;
            if ($form['preferred_datetime'] !== '') {
                $ts = strtotime($form['preferred_datetime']);
                if ($ts) $dt = date('Y-m-d H:i:s', $ts);
            }
            $pid = $form['program_id'] !== '' ? (int)$form['program_id'] : null;
            db()->prepare('INSERT INTO messages
                 (name, phone, email, program_id, preferred_datetime, body, status, ip, user_agent)
                 VALUES (?,?,?,?,?,?,\'new\',?,?)')
               ->execute([
                    mb_substr($form['name'], 0, 191),
                    mb_substr($form['phone'], 0, 64),
                    mb_substr($form['email'], 0, 191),
                    $pid, $dt, $form['body'],
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
               ]);
            $sent = true;
            $form = ['name'=>'','phone'=>'','email'=>'','program_id'=>'','preferred_datetime'=>'','body'=>''];
        }
    }
}

$def_bg = 'https://images.unsplash.com/photo-1518611012118-696072aa579a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1800&q=80';
$programs = db()->query("SELECT id, name FROM programs WHERE active = 1 ORDER BY sort_order, id")->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<section class="page-hero" style="background-image:url('<?= e(setting('contact_page_bg', $def_bg)) ?>');">
  <div class="page-hero-overlay"></div>
  <div class="wrap">
    <p class="eyebrow eyebrow-light"><i class="fa-solid fa-envelope-open-text"></i> <?= e(setting('contact_page_eyebrow', 'Επικοινωνία')) ?></p>
    <h1><?= e(setting('contact_page_title', 'Έλα στους Μαχητές')) ?></h1>
    <p class="lead lead-light"><?= e(setting('contact_page_lead', 'Στείλε μας μήνυμα ή τηλεφώνησε — κλείνουμε δοκιμαστική προπόνηση.')) ?></p>
  </div>
</section>

<section class="section">
  <div class="wrap contact-grid">
    <div class="contact-form-wrap">
      <?php if ($sent): ?>
        <div class="alert alert-success">
          <h2>Ευχαριστούμε!</h2>
          <p>Το μήνυμά σου παρελήφθη. Θα επικοινωνήσουμε μαζί σου το συντομότερο.</p>
        </div>
      <?php else: ?>
        <?php if ($errors): ?>
          <div class="alert alert-error"><ul><?php foreach ($errors as $er) echo '<li>' . e($er) . '</li>'; ?></ul></div>
        <?php endif; ?>
        <form method="post" class="contact-form" novalidate>
          <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;" aria-hidden="true">
          <label>Όνομα *
            <input type="text" name="name" required value="<?= e($form['name']) ?>">
          </label>
          <div class="two-col">
            <label>Τηλέφωνο<input type="tel" name="phone" value="<?= e($form['phone']) ?>"></label>
            <label>Email<input type="email" name="email" value="<?= e($form['email']) ?>"></label>
          </div>
          <div class="two-col">
            <label>Τμήμα ενδιαφέροντος
              <select name="program_id">
                <option value="">— Επιλογή —</option>
                <?php foreach ($programs as $p): ?>
                  <option value="<?= (int)$p['id'] ?>" <?= (string)$form['program_id']===(string)$p['id']?'selected':'' ?>>
                    <?= e($p['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </label>
            <label>Προτιμώμενη ημ/νία &amp; ώρα<input type="datetime-local" name="preferred_datetime" value="<?= e($form['preferred_datetime']) ?>"></label>
          </div>
          <label>Μήνυμα *
            <textarea name="body" rows="5" required><?= e($form['body']) ?></textarea>
          </label>
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Αποστολή</button>
        </form>
      <?php endif; ?>
    </div>

    <aside class="contact-info">
      <h3><i class="fa-solid fa-address-card"></i> Στοιχεία</h3>
      <ul class="contact-list">
        <?php $addr = trim(setting('address')); if ($addr !== ''): ?>
          <li>
            <span class="ci-ic"><i class="fa-solid fa-location-dot"></i></span>
            <div><strong>Διεύθυνση</strong><br><?= e($addr) ?></div>
          </li>
        <?php endif; ?>
        <?php foreach (contact_links() as [$lbl, $url, $icon, $display]): ?>
          <li>
            <span class="ci-ic"><i class="<?= e($icon) ?>"></i></span>
            <div><strong><?= e($lbl) ?></strong><br><a href="<?= e($url) ?>"<?= (strpos($url,'http')===0)?' target="_blank" rel="noopener"':'' ?>><?= e($display) ?></a></div>
          </li>
        <?php endforeach; ?>
      </ul>
      <div class="map">
        <iframe
          src="https://www.google.com/maps?q=<?= urlencode(setting('address', 'Γεωργίου Λιακατά 17, Μεσολόγγι')) ?>&output=embed"
          loading="lazy" referrerpolicy="no-referrer-when-downgrade"
          style="border:0;width:100%;height:280px" allowfullscreen></iframe>
      </div>
    </aside>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
