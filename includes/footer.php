<?php
$__newsletter_ok = null; $__newsletter_err = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['op'] ?? '') === 'newsletter') {
    $em = trim($_POST['newsletter_email'] ?? '');
    if (!filter_var($em, FILTER_VALIDATE_EMAIL)) {
        $__newsletter_err = 'Παρακαλώ δώσε έγκυρο email.';
    } else {
        try {
            db()->prepare('INSERT INTO newsletter_subscribers (email, ip) VALUES (?, ?)
                           ON DUPLICATE KEY UPDATE created_at = created_at')
               ->execute([mb_substr($em, 0, 191), $_SERVER['REMOTE_ADDR'] ?? null]);
            $__newsletter_ok = 'Ευχαριστούμε! Είσαι στη λίστα μας.';
        } catch (Throwable $ex) {
            $__newsletter_err = 'Κάτι πήγε στραβά, δοκίμασε ξανά.';
        }
    }
}
?>
</main>

<section class="cookie-banner" id="cookie-banner" role="dialog" aria-labelledby="cookie-title" aria-describedby="cookie-desc" hidden>
  <div class="cookie-inner">
    <div class="cookie-icon" aria-hidden="true">
      <i class="fa-solid fa-shield-halved"></i>
    </div>
    <div class="cookie-body">
      <h4 id="cookie-title" class="cookie-title">Σεβόμαστε την ιδιωτικότητά σου</h4>
      <p id="cookie-desc" class="cookie-desc">
        Χρησιμοποιούμε απαραίτητα cookies για τη λειτουργία του site και προαιρετικά cookies στατιστικών για να βελτιώσουμε την εμπειρία σου. Μπορείς να τα αποδεχτείς όλα ή να δεχτείς μόνο τα απαραίτητα. Περισσότερα στην <a href="<?= SITE_URL ?>/cookies.php">πολιτική cookies</a>.
      </p>
    </div>
    <div class="cookie-actions">
      <button type="button" class="btn btn-ghost btn-small" onclick="rejectCookies()">
        <i class="fa-solid fa-xmark"></i> Μόνο απαραίτητα
      </button>
      <button type="button" class="btn btn-primary btn-small" onclick="acceptCookies()">
        <i class="fa-solid fa-check"></i> Αποδοχή όλων
      </button>
    </div>
  </div>
</section>

<footer class="site-footer">
  <div class="footer-newsletter">
    <div class="wrap nl-inner">
      <div class="nl-copy">
        <p class="eyebrow"><i class="fa-solid fa-envelopes-bulk"></i> Newsletter</p>
        <h3>Μείνε ενημερωμένος για αγώνες &amp; προπονήσεις</h3>
        <p>Νέα του συλλόγου, νίκες και ανακοινώσεις.</p>
      </div>
      <form class="nl-form" method="post">
        <input type="hidden" name="op" value="newsletter">
        <div class="nl-field">
          <i class="fa-solid fa-envelope"></i>
          <input type="email" name="newsletter_email" placeholder="Το email σου" required>
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Εγγραφή</button>
        </div>
        <?php if ($__newsletter_ok): ?>
          <p class="nl-msg nl-ok"><i class="fa-solid fa-circle-check"></i> <?= e($__newsletter_ok) ?></p>
        <?php elseif ($__newsletter_err): ?>
          <p class="nl-msg nl-err"><i class="fa-solid fa-triangle-exclamation"></i> <?= e($__newsletter_err) ?></p>
        <?php endif; ?>
      </form>
    </div>
  </div>

  <div class="wrap footer-grid">
    <div class="footer-col">
      <a class="footer-brand" href="<?= SITE_URL ?>/">
        <img src="<?= SITE_URL ?>/assets/img/logo.jpg" alt="<?= e(SITE_NAME) ?>">
      </a>
      <p class="footer-tag"><?= e(setting('footer_tagline', 'Γυμναστικός Αθλητικός Σύλλογος Μαχητές Μεσολογγίου. Δύναμη, Πειθαρχία, Σεβασμός.')) ?></p>
      <div class="footer-socials">
        <?php foreach (social_links() as [$label, $url, $icon]): ?>
          <a href="<?= e($url) ?>" target="_blank" rel="noopener" aria-label="<?= e($label) ?>"><i class="<?= e($icon) ?>"></i></a>
        <?php endforeach; ?>
        <?php $em = trim(setting('email')); if ($em !== ''): ?>
          <a href="mailto:<?= e($em) ?>" aria-label="Email"><i class="fa-solid fa-envelope"></i></a>
        <?php endif; ?>
      </div>
    </div>

    <div class="footer-col">
      <h4>Ο Σύλλογος</h4>
      <ul class="footer-links">
        <li><a href="<?= SITE_URL ?>/about.php"><i class="fa-solid fa-chevron-right"></i> Ποιοι είμαστε</a></li>
        <li><a href="<?= SITE_URL ?>/history.php"><i class="fa-solid fa-chevron-right"></i> Ιστορία</a></li>
        <li><a href="<?= SITE_URL ?>/master.php"><i class="fa-solid fa-chevron-right"></i> Ο δάσκαλος</a></li>
        <li><a href="<?= SITE_URL ?>/schedule.php"><i class="fa-solid fa-chevron-right"></i> Πρόγραμμα προπονήσεων</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h4>Αθλητές</h4>
      <ul class="footer-links">
        <li><a href="<?= SITE_URL ?>/athletes.php"><i class="fa-solid fa-chevron-right"></i> Οι αθλητές μας</a></li>
        <li><a href="<?= SITE_URL ?>/trophies.php"><i class="fa-solid fa-chevron-right"></i> Τρόπαια</a></li>
        <li><a href="<?= SITE_URL ?>/gallery.php"><i class="fa-solid fa-chevron-right"></i> Gallery</a></li>
        <li><a href="<?= SITE_URL ?>/blog.php"><i class="fa-solid fa-chevron-right"></i> Νέα</a></li>
      </ul>
    </div>

    <div class="footer-col">
      <h4>Επικοινωνία</h4>
      <ul class="footer-contact">
        <?php $addr = trim(setting('address')); if ($addr !== ''): ?>
          <li><i class="fa-solid fa-location-dot"></i> <?= e($addr) ?></li>
        <?php endif; ?>
        <?php foreach (contact_links() as [$lbl, $url, $icon, $display]): ?>
          <li><i class="<?= e($icon) ?>"></i> <a href="<?= e($url) ?>"<?= (strpos($url,'http')===0)?' target="_blank" rel="noopener"':'' ?>><?= e($display) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

  <div class="footer-legal">
    <div class="wrap legal-inner">
      <p>© <?= date('Y') ?> <?= e(SITE_NAME) ?>. Όλα τα δικαιώματα κατοχυρωμένα.</p>
      <ul class="legal-links">
        <li><a href="<?= SITE_URL ?>/terms.php">Όροι Χρήσης</a></li>
        <li><a href="<?= SITE_URL ?>/privacy.php">Πολιτική Απορρήτου</a></li>
        <li><a href="<?= SITE_URL ?>/cookies.php">Cookies</a></li>
        <li><a class="admin-link" href="<?= SITE_URL ?>/admin/login.php"><i class="fa-solid fa-lock"></i> Διαχείριση</a></li>
      </ul>
    </div>
  </div>
</footer>

<button class="scroll-top" id="scrollTop" aria-label="Πάνω" hidden><i class="fa-solid fa-arrow-up"></i></button>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
