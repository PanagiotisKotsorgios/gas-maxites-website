<?php
require_once __DIR__ . '/includes/db.php';
$page_title = 'Ο Σύλλογος — ' . SITE_NAME;
$active = 'about';

$def_bg = 'https://images.unsplash.com/photo-1555597673-b21d5c935865?ixlib=rb-4.0.3&auto=format&fit=crop&w=1800&q=80';

$cards = [
    ['fa-solid fa-clock-rotate-left', 'Η ιστορία μας', 'Πώς ξεκίνησε ο σύλλογος και τι έχουμε πετύχει.', SITE_URL . '/history.php', 'Διάβασε'],
    ['fa-solid fa-user-tie',          'Ο δάσκαλος',    'Σωτήρης Λιαμέτης, 7ου Dan, ETU TKD WTF.',       SITE_URL . '/master.php',  'Γνώρισε τον'],
    ['fa-solid fa-calendar-days',     'Πρόγραμμα',     'Ημέρες και ώρες προπονήσεων ανά τμήμα.',        SITE_URL . '/schedule.php','Δες το'],
];
include __DIR__ . '/includes/header.php';
?>
<section class="page-hero" style="background-image:url('<?= e(setting('about_page_bg', $def_bg)) ?>');">
  <div class="page-hero-overlay"></div>
  <div class="wrap">
    <p class="eyebrow eyebrow-light"><i class="fa-solid fa-fire"></i> <?= e(setting('about_page_eyebrow', 'Ο Σύλλογος')) ?></p>
    <h1><?= e(setting('about_page_title', 'Ποιοι είμαστε')) ?></h1>
    <p class="lead lead-light"><?= e(setting('about_page_lead', 'Ο Γ.Α.Σ. Μαχητές Μεσολογγίου — δύναμη, πειθαρχία, σεβασμός.')) ?></p>
  </div>
</section>

<section class="section">
  <div class="wrap article-wrap">
    <?php echo setting('about_body', '
      <p>Ο <strong>Γυμναστικός Αθλητικός Σύλλογος «Μαχητές Μεσολογγίου»</strong> είναι ένας ενεργός τοπικός σύλλογος με έδρα την οδό Γεωργίου Λιακατά 17, στο Μεσολόγγι. Δραστηριοποιείται στο άθλημα του <em>Taekwondo</em> και εκπαιδεύει αθλητές όλων των ηλικιών, από τα πρώτα βήματα ως και το αγωνιστικό επίπεδο.</p>

      <p>Επικεφαλής και προπονητής της ομάδας είναι ο <strong>Σωτήρης Λιαμέτης</strong>, δάσκαλος <strong>7ου Dan</strong>, με χρόνια εμπειρίας και πολλές διακρίσεις. Οι αθλητές του συλλόγου συμμετέχουν ενεργά σε <strong>Πανελλήνια Πρωταθλήματα</strong> και <strong>Κύπελλα</strong>, επιστρέφοντας συχνά με μετάλλια και τίτλους.</p>

      <h2>Οι αξίες μας</h2>
      <p>Ό,τι διδάσκουμε στο ταπί, το εφαρμόζουμε και στη ζωή:</p>
      <ul>
        <li><strong>Δύναμη</strong> — φυσική, ψυχική, χαρακτήρα.</li>
        <li><strong>Πειθαρχία</strong> — στην προπόνηση, στη σχέση με τους άλλους, στον εαυτό μας.</li>
        <li><strong>Σεβασμός</strong> — για τον προπονητή, τους συναθλητές, τους αντιπάλους.</li>
      </ul>

      <h2>Σε ποιους απευθυνόμαστε</h2>
      <p>Στον σύλλογο έχουν θέση όλες οι ηλικίες: από παιδιά 4-5 ετών ως εφήβους, ενήλικες και αθλητές αγωνιστικού επιπέδου. Οι προπονήσεις προσαρμόζονται στις ανάγκες κάθε τμήματος.</p>
    '); ?>
  </div>
</section>

<section class="section section-alt">
  <div class="wrap section-head">
    <p class="eyebrow"><i class="fa-solid fa-arrow-right-long"></i> <?= e(setting('about_cards_eyebrow', 'Περισσότερα')) ?></p>
    <h2><?= e(setting('about_cards_title', 'Γνώρισε τον σύλλογο')) ?></h2>
  </div>
  <div class="wrap cards-grid" style="max-width:1080px">
    <?php foreach ($cards as $i => $c):
      $n = $i + 1;
      $ic = setting("about_c{$n}_icon", $c[0]);
      $ti = setting("about_c{$n}_title", $c[1]);
      $bo = setting("about_c{$n}_body", $c[2]);
    ?>
      <a class="card link-card" href="<?= e($c[3]) ?>">
        <div class="svc-ic"><i class="<?= e($ic) ?>"></i></div>
        <h3><?= e($ti) ?></h3>
        <p><?= e($bo) ?></p>
        <span class="link-arrow"><?= e($c[4]) ?> <i class="fa-solid fa-arrow-right"></i></span>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
