<?php
require_once __DIR__ . '/includes/db.php';
$page_title = 'Η Ιστορία μας — ' . SITE_NAME;
$active = 'history';

$def_bg = 'https://images.unsplash.com/photo-1544717305-2782549b5136?ixlib=rb-4.0.3&auto=format&fit=crop&w=1800&q=80';

$timeline = [
    ['1990s',   'Οι πρώτες προπονήσεις',            'Ο Σωτήρης Λιαμέτης, ήδη κάτοχος υψηλών Dan, ξεκινά να διδάσκει Taekwondo στο Μεσολόγγι — φέρνοντας για πρώτη φορά συστηματική εκπαίδευση του αθλήματος στην περιοχή.'],
    ['2000s',   'Πανελλήνιες συμμετοχές',           'Οι αθλητές του συλλόγου αρχίζουν να συμμετέχουν σε Πανελλήνια Πρωταθλήματα και Κύπελλα, κάνοντας γνωστό το Μεσολόγγι στον χώρο του Ελληνικού Taekwondo.'],
    ['2010s',   'Ο σύλλογος μεγαλώνει',             'Νέοι αθλητές, τμήματα για κάθε ηλικία, και σταθερές διακρίσεις στα εθνικά πρωταθλήματα. Ο σύλλογος γίνεται σημείο αναφοράς για την πόλη.'],
    ['2024',    'Μαθήματα αυτοάμυνας στο ΚΑΠΗ',     'Ο δάσκαλος Σωτήρης Λιαμέτης προσφέρει μαθήματα αυτοάμυνας στο ΚΑΠΗ Μεσολογγίου, δίνοντας πίσω στην κοινωνία που στηρίζει τον σύλλογο.'],
    ['Σήμερα',  'Δύναμη, Πειθαρχία, Σεβασμός',      'Ο σύλλογος συνεχίζει να προπονεί την επόμενη γενιά αθλητών με την ίδια φιλοσοφία: το Taekwondo δεν είναι μόνο άθλημα — είναι τρόπος ζωής.'],
];
include __DIR__ . '/includes/header.php';
?>
<section class="page-hero" style="background-image:url('<?= e(setting('history_page_bg', $def_bg)) ?>');">
  <div class="page-hero-overlay"></div>
  <div class="wrap">
    <p class="eyebrow eyebrow-light"><i class="fa-solid fa-clock-rotate-left"></i> <?= e(setting('history_page_eyebrow', 'Ιστορία')) ?></p>
    <h1><?= e(setting('history_page_title', 'Η ιστορία των Μαχητών')) ?></h1>
    <p class="lead lead-light"><?= e(setting('history_page_lead', 'Δεκαετίες προπόνησης, εμπειρίας και νικών στο Μεσολόγγι.')) ?></p>
  </div>
</section>

<section class="section">
  <div class="wrap article-wrap">
    <?php echo setting('history_body', '
      <p>Ο <strong>Γ.Α.Σ. Μαχητές Μεσολογγίου</strong> γεννήθηκε από την αγάπη για την πολεμική τέχνη του Taekwondo και την πεποίθηση ότι το άθλημα μπορεί να αλλάξει ζωές — ιδιαίτερα των νέων ανθρώπων της Αιτωλοακαρνανίας.</p>
    '); ?>

    <div class="timeline">
      <?php foreach ($timeline as $i => $t):
        $n = $i + 1;
        $yr = setting("hist_t{$n}_year",  $t[0]);
        $ti = setting("hist_t{$n}_title", $t[1]);
        $bo = setting("hist_t{$n}_body",  $t[2]);
      ?>
        <div class="tl-item">
          <div class="tl-year"><?= e($yr) ?></div>
          <div class="tl-body">
            <h3><?= e($ti) ?></h3>
            <p><?= e($bo) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
