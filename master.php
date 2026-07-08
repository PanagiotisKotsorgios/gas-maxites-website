<?php
require_once __DIR__ . '/includes/db.php';
$page_title = 'Ο Δάσκαλος — ' . setting('master_name', 'Σωτήρης Λιαμέτης') . ' — ' . SITE_NAME;
$active = 'master';

$def_bg = 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1800&q=80';
$def_ph = 'https://images.unsplash.com/photo-1594381898411-846e7d193883?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';

$facts = [
    ['fa-solid fa-medal',  '7ου Dan',              'μαύρη ζώνη Tae Kwon Do'],
    ['fa-solid fa-globe',  'ETU · WTF',            'Ευρωπαϊκή & Παγκόσμια Ομοσπονδία'],
    ['fa-solid fa-users',  'Δεκαετίες',            'στην προπόνηση αθλητών'],
    ['fa-solid fa-trophy', 'Πολλές διακρίσεις',    'μαθητές του σε πανελλήνιους αγώνες'],
];
include __DIR__ . '/includes/header.php';
?>
<section class="page-hero" style="background-image:url('<?= e(setting('master_page_bg', $def_bg)) ?>');">
  <div class="page-hero-overlay"></div>
  <div class="wrap">
    <p class="eyebrow eyebrow-light"><i class="fa-solid fa-user-tie"></i> <?= e(setting('master_page_eyebrow', 'Ο Δάσκαλος')) ?></p>
    <h1><?= e(setting('master_name', 'Σωτήρης Λιαμέτης')) ?></h1>
    <p class="lead lead-light"><?= e(setting('master_title', 'Δάσκαλος 7ου Dan · ETU TKD WTF · Ιδρυτής & προπονητής των Μαχητών')) ?></p>
  </div>
</section>

<section class="section">
  <div class="wrap master-grid">
    <aside class="master-side">
      <?php $mp = setting('master_photo'); $fallback = setting('master_photo_fallback', $def_ph); ?>
      <div class="master-photo" style="background-image:url('<?= $mp ? SITE_URL . '/uploads/athletes/' . e($mp) : e($fallback) ?>');"></div>
      <ul class="fact-list">
        <?php foreach ($facts as $i => $f):
          $n = $i + 1;
          $ic = setting("mf{$n}_icon",  $f[0]);
          $ti = setting("mf{$n}_title", $f[1]);
          $bo = setting("mf{$n}_body",  $f[2]);
        ?>
          <li><i class="<?= e($ic) ?>"></i> <div><strong><?= e($ti) ?></strong><span><?= e($bo) ?></span></div></li>
        <?php endforeach; ?>
      </ul>
    </aside>
    <article class="master-body">
      <?php echo setting('master_bio', '
        <p>Ο <strong>Σωτήρης Λιαμέτης</strong> είναι ένας από τους πιο έμπειρους δασκάλους Taekwondo της Ελλάδας. Με έδρα το Μεσολόγγι, έχει αφιερώσει τη ζωή του στη μετάδοση των αρχών και της τεχνικής του Taekwondo σε γενιές αθλητών.</p>

        <h2>Φιλοσοφία</h2>
        <p>Η προσέγγιση του Σωτήρη Λιαμέτη είναι ολιστική: το Taekwondo δεν είναι απλώς ένα σύνολο τεχνικών, αλλά τρόπος να χτίσει κανείς <strong>χαρακτήρα</strong>. Στις προπονήσεις του δίνεται έμφαση στην <em>πειθαρχία</em>, τον <em>σεβασμό</em> και τη <em>συνέπεια</em> — αξίες που ο κάθε αθλητής παίρνει και εκτός ταπί.</p>

        <h2>Κοινωνική δράση</h2>
        <p>Πέρα από τον σύλλογο, ο δάσκαλος έχει προσφέρει μαθήματα αυτοάμυνας στο <strong>ΚΑΠΗ Μεσολογγίου</strong> και σε άλλες κοινωνικές δράσεις της Ιεράς Πόλης, θέλοντας να επιστρέψει στην κοινωνία που στηρίζει τον σύλλογο.</p>

        <blockquote>«Το Taekwondo δεν σε κάνει σκληρό — σε κάνει υπεύθυνο.»</blockquote>

        <h2>Αναγνώριση</h2>
        <p>Οι μαθητές του σύλλογου εκπροσωπούν σταθερά το Μεσολόγγι σε αγώνες πανελλήνιας εμβέλειας, ενώ ο σύλλογος έχει βραβευτεί ως <strong>«Χρυσή Εταιρεία»</strong> για τη συνεισφορά του στο άθλημα και την κοινωνία.</p>
      '); ?>

      <div class="cta-inline">
        <a href="<?= SITE_URL ?>/contact.php" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> <?= e(setting('master_cta1', 'Κλείσε δοκιμαστική')) ?></a>
        <a href="<?= SITE_URL ?>/athletes.php" class="btn btn-ghost"><?= e(setting('master_cta2', 'Δες τους αθλητές του')) ?></a>
      </div>
    </article>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
