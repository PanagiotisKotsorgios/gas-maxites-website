<?php
require_once __DIR__ . '/includes/db.php';

$slug = (string)($_GET['slug'] ?? '');
$s = db()->prepare("SELECT * FROM athletes WHERE slug = ? AND active = 1");
$s->execute([$slug]);
$a = $s->fetch();

if (!$a) {
    http_response_code(404);
    $page_title = 'Δεν βρέθηκε — ' . SITE_NAME;
    $active = 'athletes';
    include __DIR__ . '/includes/header.php';
    echo '<section class="section"><div class="wrap"><h1>404</h1><p>Ο αθλητής δεν βρέθηκε.</p><p><a href="' . SITE_URL . '/athletes.php"><i class="fa-solid fa-arrow-left"></i> Πίσω στους αθλητές</a></p></div></section>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$page_title = $a['name'] . ' — Αθλητής — ' . SITE_NAME;
$page_desc = mb_substr(strip_tags($a['bio'] ?? '') ?: $a['name'], 0, 160);
$active = 'athlete';

$matches = db()->prepare('SELECT * FROM athlete_matches WHERE athlete_id = ? ORDER BY match_date DESC, id DESC');
$matches->execute([$a['id']]);
$matches = $matches->fetchAll();

$trophies = db()->prepare('SELECT * FROM trophies WHERE athlete_id = ? ORDER BY achieved_on DESC, id DESC');
$trophies->execute([$a['id']]);
$trophies = $trophies->fetchAll();

$total = (int)$a['wins'] + (int)$a['losses'] + (int)$a['draws'];
$win_pct = $total ? round(((int)$a['wins'] / $total) * 100) : 0;

include __DIR__ . '/includes/header.php';
?>
<section class="athlete-hero" style="background-image:url('<?= $a['photo'] ? SITE_URL . '/uploads/athletes/' . e($a['photo']) : 'https://images.unsplash.com/photo-1594381898411-846e7d193883?ixlib=rb-4.0.3&auto=format&fit=crop&w=1800&q=80' ?>');">
  <div class="ath-hero-overlay"></div>
  <div class="wrap ath-hero-inner">
    <p class="eyebrow eyebrow-light"><a href="<?= SITE_URL ?>/athletes.php"><i class="fa-solid fa-arrow-left"></i> Αθλητές</a></p>
    <?php if ($a['belt']): ?><span class="belt-badge belt-<?= e(strtolower(preg_replace('/\s+/', '-', $a['belt']))) ?>"><?= e($a['belt']) ?></span><?php endif; ?>
    <h1><?= e($a['name']) ?></h1>
    <div class="ath-hero-meta">
      <?php if ($a['weight_category']): ?><span><i class="fa-solid fa-weight-hanging"></i> <?= e($a['weight_category']) ?></span><?php endif; ?>
      <?php if ($a['age_group']): ?><span><i class="fa-solid fa-user-group"></i> <?= e($a['age_group']) ?></span><?php endif; ?>
      <?php if ($a['dob']): $age = (int)date('Y') - (int)date('Y', strtotime($a['dob'])); ?>
        <span><i class="fa-solid fa-cake-candles"></i> <?= (int)$age ?> ετών</span>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap athlete-layout">
    <aside class="athlete-side">
      <div class="stat-block">
        <h3>Ρεκόρ</h3>
        <div class="record-big">
          <div><strong class="rec-w-lg"><?= (int)$a['wins'] ?></strong><span>Νίκες</span></div>
          <div><strong class="rec-l-lg"><?= (int)$a['losses'] ?></strong><span>Ήττες</span></div>
          <div><strong class="rec-d-lg"><?= (int)$a['draws'] ?></strong><span>Ισοπαλίες</span></div>
        </div>
        <div class="winpct">
          <div class="winpct-bar"><span style="width:<?= (int)$win_pct ?>%"></span></div>
          <p><strong><?= (int)$win_pct ?>%</strong> ποσοστό νικών <span class="muted-sm">(<?= (int)$total ?> αγώνες)</span></p>
        </div>
      </div>

      <?php if ($trophies): ?>
        <div class="stat-block">
          <h3><i class="fa-solid fa-trophy"></i> Τρόπαια</h3>
          <ul class="ath-trophies">
            <?php foreach ($trophies as $t): ?>
              <li>
                <strong><?= e($t['title']) ?></strong>
                <?php if ($t['event']): ?><span class="muted-sm"><?= e($t['event']) ?></span><?php endif; ?>
                <?php if ($t['achieved_on']): ?><time><?= e(date('d/m/Y', strtotime($t['achieved_on']))) ?></time><?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    </aside>

    <article class="athlete-body">
      <?php if (trim((string)$a['bio']) !== ''): ?>
        <div class="post-body-copy"><?= $a['bio'] /* trusted admin HTML */ ?></div>
      <?php endif; ?>

      <?php if ($matches): ?>
        <h2 class="section-inner-h2"><i class="fa-solid fa-clipboard-list"></i> Αγώνες</h2>
        <div class="matches-table">
          <table>
            <thead>
              <tr><th>Ημερομηνία</th><th>Αντίπαλος</th><th>Διοργάνωση</th><th>Αποτέλεσμα</th><th>Score</th><th>Video</th></tr>
            </thead>
            <tbody>
              <?php foreach ($matches as $m):
                $embed = $m['video_url'] ? youtube_embed($m['video_url']) : null; ?>
                <tr>
                  <td><?= $m['match_date'] ? e(date('d/m/Y', strtotime($m['match_date']))) : '—' ?></td>
                  <td><?= e($m['opponent'] ?: '—') ?></td>
                  <td><?= e($m['event'] ?: '—') ?></td>
                  <td>
                    <?php $res = $m['result']; ?>
                    <span class="result-badge res-<?= e($res) ?>">
                      <?= $res==='win'?'Νίκη':($res==='loss'?'Ήττα':'Ισοπαλία') ?>
                    </span>
                  </td>
                  <td><?= e($m['score'] ?: '—') ?></td>
                  <td>
                    <?php if ($embed): ?>
                      <button class="video-btn" type="button" data-embed="<?= e($embed) ?>">
                        <i class="fa-brands fa-youtube"></i> Δες
                      </button>
                    <?php elseif ($m['video_url']): ?>
                      <a href="<?= e($m['video_url']) ?>" target="_blank" rel="noopener"><i class="fa-solid fa-link"></i> Link</a>
                    <?php else: ?>—<?php endif; ?>
                  </td>
                </tr>
                <?php if ($m['notes']): ?>
                  <tr class="notes-row"><td colspan="6"><em><?= e($m['notes']) ?></em></td></tr>
                <?php endif; ?>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <?php
      $vids = array_filter(array_map(fn($m) => youtube_embed((string)$m['video_url']), $matches));
      if ($vids): ?>
        <h2 class="section-inner-h2"><i class="fa-brands fa-youtube"></i> Βίντεο αγώνων</h2>
        <div class="video-grid">
          <?php foreach (array_slice($vids, 0, 4) as $v): ?>
            <div class="video-wrap">
              <iframe src="<?= e($v) ?>" title="Fight video" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </article>
  </div>
</section>

<div id="videoModal" class="video-modal" hidden>
  <button class="lb-close" aria-label="Κλείσιμο"><i class="fa-solid fa-xmark"></i></button>
  <div class="video-modal-inner"><iframe src="" allow="autoplay; encrypted-media" allowfullscreen></iframe></div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
