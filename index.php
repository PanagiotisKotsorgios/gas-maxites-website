<?php
require_once __DIR__ . '/includes/db.php';
$page_title = SITE_NAME . ' — Taekwondo Μεσολόγγι';
$page_desc  = 'Γ.Α.Σ. Μαχητές Μεσολογγίου — Taekwondo με τον δάσκαλο 7ου dan Σωτήρη Λιαμέτη. Δύναμη, Πειθαρχία, Σεβασμός.';
$active = 'home';

$programs = db()->query("SELECT * FROM programs WHERE active = 1 ORDER BY sort_order, id LIMIT 6")->fetchAll();
$featured_athletes = db()->query("SELECT * FROM athletes WHERE active = 1 ORDER BY sort_order, id LIMIT 4")->fetchAll();
$recent_trophies   = db()->query("SELECT * FROM trophies ORDER BY achieved_on DESC, id DESC LIMIT 4")->fetchAll();
$latest_posts      = db()->query("SELECT slug, title, excerpt, cover_image, created_at FROM posts WHERE published = 1 ORDER BY created_at DESC LIMIT 3")->fetchAll();
$gallery_preview   = db()->query("SELECT filename, caption FROM gallery ORDER BY sort_order, id DESC LIMIT 6")->fetchAll();

// Real Google reviews (client-supplied)
$google_reviews = [
    ['name'=>'Panagiotis Kotsorgios','when'=>'πριν από 5 μήνες','stars'=>5,
     'text'=>'Ένας χώρος όπου η σωματική και η πνευματική ανάπτυξη πηγαίνουν χέρι-χέρι. Οι μαθητές μαθαίνουν την πολεμική τέχνη του Tae Kwon Do με σεβασμό, πειθαρχία και υπομονή.'],
    ['name'=>'Kallirroi Kostriva','when'=>'πριν από έναν χρόνο','stars'=>5,
     'text'=>'Είναι το καλύτερο μέρος που κάποιο παιδί μπορεί να μάθει το άθλημα του taekwondo, να εξελιχθεί ώστε να μπορεί να συμμετέχει σε αγώνες είτε να μάθει πολεμική τέχνη.'],
    ['name'=>'Makis Aspiotis','when'=>'πριν από 6 μήνες','stars'=>5,
     'text'=>'Απο τους πιο έμπειρους δασκάλους του χώρου.'],
];
$google_reviews_url = setting('google_reviews_url', 'https://www.google.com/search?q=%CE%9C%CE%91%CE%A7%CE%97%CE%A4%CE%95%CE%A3+%CE%9C%CE%B5%CF%83%CE%BF%CE%BB%CE%BF%CE%B3%CE%B3%CE%AF%CE%BF%CF%85');

// Taekwondo-themed default background images (Unsplash — free & hotlinkable).
// Facebook CDN URLs from the club's page expire, so we ship themed defaults;
// admins can override any of these from the Settings panel.
$def_hero_bg      = 'https://images.unsplash.com/photo-1555597673-b21d5c935865?ixlib=rb-4.0.3&auto=format&fit=crop&w=1800&q=80';
$def_about_bg     = 'https://images.unsplash.com/photo-1555597408-26bc8e548a46?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80';
$def_trophies_bg  = 'https://images.unsplash.com/photo-1552674605-db6ffd4facb5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1800&q=80';
$def_reviews_bg   = 'https://images.unsplash.com/photo-1544717305-2782549b5136?ixlib=rb-4.0.3&auto=format&fit=crop&w=1800&q=80';
$def_cta_bg       = 'https://images.unsplash.com/photo-1518611012118-696072aa579a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1800&q=80';
$def_athlete_ph   = 'https://images.unsplash.com/photo-1594381898411-846e7d193883?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';

include __DIR__ . '/includes/header.php';
?>
<section class="hero hero-image" style="background-image:url('<?= e(setting('hero_bg', $def_hero_bg)) ?>');">
  <div class="hero-overlay"></div>
  <div class="wrap hero-inner">
    <div class="hero-copy">
      <p class="eyebrow eyebrow-light"><i class="fa-solid fa-hand-fist"></i> <?= e(setting('hero_eyebrow', 'Taekwondo · Μεσολόγγι · Από το 1990')) ?></p>
      <h1><?= e(setting('hero_title', 'Δύναμη. Πειθαρχία. Σεβασμός.')) ?></h1>
      <p class="lead lead-light"><?= e(setting('hero_subtitle', 'Ο Γ.Α.Σ. Μαχητές Μεσολογγίου είναι ένας ενεργός σύλλογος Taekwondo με τον δάσκαλο Σωτήρη Λιαμέτη, 7ου dan. Παιδιά, έφηβοι και ενήλικες — όλοι μαθαίνουν την τέχνη με σεβασμό στον εαυτό τους και στους άλλους.')) ?></p>
      <div class="hero-cta">
        <a href="<?= SITE_URL ?>/contact.php" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> <?= e(setting('hero_cta1', 'Έλα για εγγραφή')) ?></a>
        <a href="<?= SITE_URL ?>/schedule.php" class="btn btn-ghost btn-ghost-light"><?= e(setting('hero_cta2', 'Δες το πρόγραμμα')) ?></a>
      </div>
      <div class="hero-rating">
        <div class="stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
        <span><strong><?= e(setting('hero_rating', '4,9/5')) ?></strong> <?= e(setting('hero_rating_sub', 'στο Google · Δες αξιολογήσεις')) ?> <a href="<?= e($google_reviews_url) ?>" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-right"></i></a></span>
      </div>
    </div>
  </div>
  <a href="#features" class="hero-scroll" aria-label="Κύλισε κάτω"><i class="fa-solid fa-chevron-down"></i></a>
</section>

<section class="section features" id="features">
  <div class="wrap">
    <div class="features-grid">
      <?php
      $feats = [
        ['fa-solid fa-hand-fist',       '7ου dan δάσκαλος',   'Ο Σωτήρης Λιαμέτης, με δεκαετίες εμπειρίας σε αγώνες και προπόνηση.'],
        ['fa-solid fa-medal',           'Πανελλήνιοι τίτλοι', 'Οι αθλητές μας συμμετέχουν και διακρίνονται σε Πανελλήνια πρωταθλήματα και Κύπελλα.'],
        ['fa-solid fa-children',        'Όλες οι ηλικίες',    'Τμήματα για παιδιά από 4 ετών, εφήβους, ενήλικες και προχωρημένους αθλητές.'],
        ['fa-solid fa-shield-halved',   'Αυτοάμυνα',          'Πραγματικές δεξιότητες αυτοάμυνας — όχι μόνο άθλημα, αλλά και εργαλείο ζωής.'],
      ];
      foreach ($feats as $i => $f):
        $n = $i + 1;
        $ic = setting("feat{$n}_icon", $f[0]);
        $ti = setting("feat{$n}_title", $f[1]);
        $bo = setting("feat{$n}_body", $f[2]);
      ?>
        <div class="feature">
          <div class="feature-ic"><i class="<?= e($ic) ?>"></i></div>
          <h3><?= e($ti) ?></h3>
          <p><?= e($bo) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section split section-alt">
  <div class="wrap split-inner">
    <div class="split-media" style="background-image:url('<?= e(setting('about_bg', $def_about_bg)) ?>');"></div>
    <div class="split-copy">
      <p class="eyebrow"><i class="fa-solid fa-fire"></i> <?= e(setting('about_eyebrow', 'Ο Σύλλογος')) ?></p>
      <h2><?= e(setting('about_title', 'Μια οικογένεια. Μια πολεμική τέχνη.')) ?></h2>
      <?php echo setting('about_body', '<p>Ο <strong>Γ.Α.Σ. Μαχητές Μεσολογγίου</strong> είναι ένας από τους πιο ενεργούς αθλητικούς συλλόγους Taekwondo της Αιτωλοακαρνανίας. Εδώ, από τα πρώτα βήματα ως και τους αγώνες, μαθαίνουμε ένα άθλημα που χτίζει χαρακτήρα, πειθαρχία και αυτοπεποίθηση.</p><p>Οι μαθητές μας συμμετέχουν ενεργά σε <strong>Πανελλήνια Πρωταθλήματα</strong> και <strong>Κύπελλα</strong> — επιστρέφοντας κάθε φορά με νέες εμπειρίες και νέα τρόπαια.</p>'); ?>
      <div class="split-stats">
        <div><strong><?= e(setting('about_stat1_val', '7ος Dan')) ?></strong><span><?= e(setting('about_stat1_lbl', 'δάσκαλος')) ?></span></div>
        <div><strong><?= e(setting('about_stat2_val', '5.100+')) ?></strong><span><?= e(setting('about_stat2_lbl', 'Φίλοι στα social')) ?></span></div>
        <div><strong><?= e(setting('about_stat3_val', '4,9/5')) ?></strong><span><?= e(setting('about_stat3_lbl', 'Google reviews')) ?></span></div>
      </div>
      <p style="margin-top:1.5rem"><a href="<?= SITE_URL ?>/about.php" class="link-arrow">Μάθε περισσότερα <i class="fa-solid fa-arrow-right"></i></a></p>
    </div>
  </div>
</section>

<?php if ($programs): ?>
<section class="section">
  <div class="wrap">
    <header class="section-head">
      <p class="eyebrow"><i class="fa-solid fa-people-group"></i> <?= e(setting('programs_eyebrow', 'Τμήματα')) ?></p>
      <h2><?= e(setting('programs_title', 'Προπόνηση για κάθε ηλικία')) ?></h2>
      <p class="section-sub"><?= e(setting('programs_sub', 'Τα τμήματά μας είναι οργανωμένα ώστε κάθε αθλητής να προχωράει με τον δικό του ρυθμό.')) ?></p>
    </header>
    <div class="cards-grid">
      <?php foreach ($programs as $p): ?>
        <article class="card program-card">
          <div class="prog-img" style="background-image:url('<?= e(program_image_url($p)) ?>');" aria-hidden="true"></div>
          <h3><?= e($p['name']) ?></h3>
          <?php if ($p['age_range']): ?><p class="prog-age"><i class="fa-solid fa-user-group"></i> <?= e($p['age_range']) ?></p><?php endif; ?>
          <?php if ($p['description']): ?><p><?= e(mb_strimwidth($p['description'], 0, 130, '…', 'UTF-8')) ?></p><?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
    <p class="center-cta"><a href="<?= SITE_URL ?>/schedule.php" class="link-arrow">Δες το πρόγραμμα προπονήσεων <i class="fa-solid fa-arrow-right"></i></a></p>
  </div>
</section>
<?php endif; ?>

<?php if ($featured_athletes): ?>
<section class="section section-alt">
  <div class="wrap">
    <header class="section-head">
      <p class="eyebrow"><i class="fa-solid fa-user-ninja"></i> <?= e(setting('athletes_home_eyebrow', 'Οι αθλητές μας')) ?></p>
      <h2><?= e(setting('athletes_home_title', 'Οι μαχητές μας στο αγωνιστικό ταπί')) ?></h2>
    </header>
    <div class="athletes-grid">
      <?php foreach ($featured_athletes as $a): ?>
        <a class="athlete-card" href="<?= SITE_URL ?>/athlete.php?slug=<?= e($a['slug']) ?>">
          <div class="ath-photo" style="background-image:url('<?= $a['photo'] ? SITE_URL . '/uploads/athletes/' . e($a['photo']) : $def_athlete_ph ?>');"></div>
          <div class="ath-body">
            <?php if ($a['belt']): ?><span class="belt-badge belt-<?= e(strtolower(preg_replace('/\s+/', '-', $a['belt']))) ?>"><?= e($a['belt']) ?></span><?php endif; ?>
            <h3><?= e($a['name']) ?></h3>
            <?php if ($a['weight_category']): ?><p class="muted-sm"><?= e($a['weight_category']) ?></p><?php endif; ?>
            <div class="ath-record">
              <span class="rec-w"><?= (int)$a['wins'] ?>W</span>
              <span class="rec-l"><?= (int)$a['losses'] ?>L</span>
              <span class="rec-d"><?= (int)$a['draws'] ?>D</span>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
    <p class="center-cta"><a href="<?= SITE_URL ?>/athletes.php" class="link-arrow">Δες όλους τους αθλητές <i class="fa-solid fa-arrow-right"></i></a></p>
  </div>
</section>
<?php endif; ?>

<?php if ($recent_trophies): ?>
<section class="section trophies-band" style="background-image:url('<?= e(setting('trophies_home_bg', $def_trophies_bg)) ?>');">
  <div class="reviews-overlay"></div>
  <div class="wrap">
    <header class="section-head section-head-light">
      <p class="eyebrow eyebrow-light"><i class="fa-solid fa-trophy"></i> <?= e(setting('trophies_home_eyebrow', 'Τρόπαια & διακρίσεις')) ?></p>
      <h2><?= e(setting('trophies_home_title', 'Πρόσφατες νίκες')) ?></h2>
    </header>
    <div class="trophies-grid">
      <?php foreach ($recent_trophies as $t): ?>
        <div class="trophy-card">
          <?php if ($t['image']): ?>
            <img src="<?= SITE_URL ?>/uploads/trophies/<?= e($t['image']) ?>" alt="">
          <?php else: ?>
            <div class="trophy-ic"><i class="fa-solid fa-trophy"></i></div>
          <?php endif; ?>
          <h3><?= e($t['title']) ?></h3>
          <?php if ($t['event']): ?><p class="muted-sm"><?= e($t['event']) ?></p><?php endif; ?>
          <?php if ($t['achieved_on']): ?><time><?= e(date('d M Y', strtotime($t['achieved_on']))) ?></time><?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="center-cta">
      <a href="<?= SITE_URL ?>/trophies.php" class="btn btn-primary"><i class="fa-solid fa-medal"></i> <?= e(setting('trophies_home_cta', 'Δες όλα τα τρόπαια')) ?></a>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($gallery_preview): ?>
<section class="section">
  <div class="wrap">
    <header class="section-head">
      <p class="eyebrow"><i class="fa-solid fa-camera"></i> <?= e(setting('gallery_home_eyebrow', 'Στιγμές')) ?></p>
      <h2><?= e(setting('gallery_home_title', 'Ο σύλλογος σε δράση')) ?></h2>
    </header>
    <div class="gallery-preview">
      <?php foreach ($gallery_preview as $g): ?>
        <a href="<?= SITE_URL ?>/gallery.php" class="thumb">
          <img src="<?= SITE_URL ?>/uploads/gallery/<?= e($g['filename']) ?>" alt="<?= e($g['caption'] ?? '') ?>" loading="lazy">
        </a>
      <?php endforeach; ?>
    </div>
    <p class="center-cta"><a href="<?= SITE_URL ?>/gallery.php" class="link-arrow">Δες όλη τη gallery <i class="fa-solid fa-arrow-right"></i></a></p>
  </div>
</section>
<?php endif; ?>

<section class="section reviews-band" style="background-image:url('<?= e(setting('reviews_home_bg', $def_reviews_bg)) ?>');">
  <div class="reviews-overlay"></div>
  <div class="wrap">
    <header class="section-head section-head-light">
      <p class="eyebrow eyebrow-light"><i class="fa-brands fa-google"></i> <?= e(setting('reviews_home_eyebrow', 'Αξιολογήσεις Google')) ?></p>
      <h2><?= e(setting('reviews_home_title', '4,9 / 5 από 15 αξιολογήσεις')) ?></h2>
      <p class="section-sub" style="color:rgba(255,255,255,.8)"><?= e(setting('reviews_home_sub', 'Πραγματικά σχόλια από γονείς και αθλητές μας.')) ?></p>
    </header>
    <div class="quotes-grid">
      <?php foreach ($google_reviews as $r): ?>
        <blockquote class="quote">
          <div class="stars">
            <?php for ($i=0;$i<5;$i++): ?>
              <i class="fa-<?= $i<$r['stars']?'solid':'regular' ?> fa-star"></i>
            <?php endfor; ?>
          </div>
          <p>“<?= e($r['text']) ?>”</p>
          <cite>
            <strong><?= e($r['name']) ?></strong>
            <span><i class="fa-brands fa-google"></i> <?= e($r['when']) ?></span>
          </cite>
        </blockquote>
      <?php endforeach; ?>
    </div>
    <div class="center-cta">
      <a href="<?= e($google_reviews_url) ?>" target="_blank" rel="noopener" class="btn btn-primary">
        <i class="fa-brands fa-google"></i> <?= e(setting('reviews_home_cta', 'Δες όλες τις αξιολογήσεις')) ?>
      </a>
    </div>
  </div>
</section>

<?php if ($latest_posts): ?>
<section class="section">
  <div class="wrap">
    <header class="section-head">
      <p class="eyebrow"><i class="fa-solid fa-newspaper"></i> <?= e(setting('news_home_eyebrow', 'Νέα')) ?></p>
      <h2><?= e(setting('news_home_title', 'Πρόσφατα άρθρα')) ?></h2>
    </header>
    <div class="cards-grid">
      <?php foreach ($latest_posts as $p): ?>
        <a class="card post-card" href="<?= SITE_URL ?>/post.php?slug=<?= e($p['slug']) ?>">
          <?php if ($p['cover_image']): ?>
            <img src="<?= SITE_URL ?>/uploads/posts/<?= e($p['cover_image']) ?>" alt="" loading="lazy">
          <?php else: ?>
            <img src="<?= e($def_hero_bg) ?>" alt="" loading="lazy">
          <?php endif; ?>
          <div class="post-body">
            <time><i class="fa-regular fa-calendar"></i> <?= e(date('d/m/Y', strtotime($p['created_at']))) ?></time>
            <h3><?= e($p['title']) ?></h3>
            <?php if ($p['excerpt']): ?><p><?= e($p['excerpt']) ?></p><?php endif; ?>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="section cta-band" style="background-image:url('<?= e(setting('cta_home_bg', $def_cta_bg)) ?>');">
  <div class="cta-overlay"></div>
  <div class="wrap cta-inner">
    <h2><?= e(setting('cta_home_title', 'Έτοιμος να μπεις στο ταπί;')) ?></h2>
    <p><?= e(setting('cta_home_body', 'Έλα για μια δωρεάν δοκιμαστική προπόνηση. Ο δάσκαλος περιμένει.')) ?></p>
    <div>
      <?php $p = trim(setting('phone2')) ?: trim(setting('phone1')); if ($p): ?>
        <a href="tel:<?= e($p) ?>" class="btn btn-primary"><i class="fa-solid fa-phone"></i> <?= e(setting('cta_home_phone', 'Πάρε τηλέφωνο')) ?></a>
      <?php endif; ?>
      <a href="<?= SITE_URL ?>/contact.php" class="btn btn-ghost btn-ghost-light"><i class="fa-solid fa-envelope"></i> <?= e(setting('cta_home_msg', 'Στείλε μήνυμα')) ?></a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
