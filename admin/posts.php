<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();
$page_title = 'Άρθρα — Μαχητές Admin';
$active = 'posts';
$pdo = db();
$action = $_GET['action'] ?? 'list';
$flash = null;

function handle_upload_cover(): ?string {
    if (empty($_FILES['cover_image']['name'])) return null;
    if ($_FILES['cover_image']['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($_FILES['cover_image']['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Σφάλμα ανεβάσματος.');
    if ($_FILES['cover_image']['size'] > MAX_UPLOAD_BYTES) throw new RuntimeException('Το αρχείο ξεπερνά τα 5MB.');
    $ext = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','webp'], true)) throw new RuntimeException('Επιτρέπονται μόνο JPG/PNG/WEBP.');
    $mime = mime_content_type($_FILES['cover_image']['tmp_name']);
    if (!in_array($mime, ['image/jpeg','image/png','image/webp'], true)) throw new RuntimeException('Μη έγκυρη εικόνα.');
    if (!is_dir(UPLOAD_DIR_POSTS)) mkdir(UPLOAD_DIR_POSTS, 0775, true);
    $name = 'post-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
    move_uploaded_file($_FILES['cover_image']['tmp_name'], UPLOAD_DIR_POSTS . '/' . $name);
    return $name;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $op = $_POST['op'] ?? '';
    try {
        if ($op === 'create' || $op === 'update') {
            $id      = (int)($_POST['id'] ?? 0);
            $title   = trim($_POST['title'] ?? '');
            $slug    = trim($_POST['slug'] ?? '') ?: slugify($title);
            $excerpt = trim($_POST['excerpt'] ?? '');
            $body    = (string)($_POST['body'] ?? '');
            $pub     = isset($_POST['published']) ? 1 : 0;
            if ($title === '' || $body === '') throw new RuntimeException('Τίτλος και κείμενο είναι υποχρεωτικά.');
            $cover = handle_upload_cover();
            if ($op === 'create') {
                $pdo->prepare('INSERT INTO posts (slug,title,excerpt,body,cover_image,published) VALUES (?,?,?,?,?,?)')
                    ->execute([$slug, $title, $excerpt, $body, $cover, $pub]);
                $flash = 'Το άρθρο δημιουργήθηκε.';
            } else {
                if ($cover) {
                    $pdo->prepare('UPDATE posts SET slug=?, title=?, excerpt=?, body=?, cover_image=?, published=? WHERE id=?')
                        ->execute([$slug, $title, $excerpt, $body, $cover, $pub, $id]);
                } else {
                    $pdo->prepare('UPDATE posts SET slug=?, title=?, excerpt=?, body=?, published=? WHERE id=?')
                        ->execute([$slug, $title, $excerpt, $body, $pub, $id]);
                }
                $flash = 'Το άρθρο ενημερώθηκε.';
            }
            header('Location: ' . SITE_URL . '/admin/posts.php?msg=' . urlencode($flash));
            exit;
        }
        if ($op === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            $pdo->prepare('DELETE FROM posts WHERE id = ?')->execute([$id]);
            header('Location: ' . SITE_URL . '/admin/posts.php?msg=' . urlencode('Το άρθρο διαγράφηκε.'));
            exit;
        }
    } catch (Throwable $ex) {
        $flash = 'Σφάλμα: ' . $ex->getMessage();
    }
}
$flash = $flash ?? ($_GET['msg'] ?? null);

if ($action === 'edit' || $action === 'new') {
    $post = ['id'=>0,'slug'=>'','title'=>'','excerpt'=>'','body'=>'','cover_image'=>null,'published'=>0];
    if ($action === 'edit') {
        $s = $pdo->prepare('SELECT * FROM posts WHERE id = ?');
        $s->execute([(int)($_GET['id'] ?? 0)]);
        $post = $s->fetch() ?: $post;
    }
    include __DIR__ . '/includes/admin_header.php';
    ?>
    <div class="admin-wrap">
      <p><a href="<?= SITE_URL ?>/admin/posts.php"><i class="fa-solid fa-arrow-left"></i> Άρθρα</a></p>
      <h1><?= $post['id'] ? 'Επεξεργασία άρθρου' : 'Νέο άρθρο' ?></h1>
      <?php if ($flash): ?><p class="alert"><?= e($flash) ?></p><?php endif; ?>
      <form method="post" enctype="multipart/form-data" class="admin-form">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="op" value="<?= $post['id']?'update':'create' ?>">
        <input type="hidden" name="id" value="<?= (int)$post['id'] ?>">
        <label>Τίτλος<input type="text" name="title" required value="<?= e($post['title']) ?>"></label>
        <label>Slug (URL) <small>Αφήστε κενό για αυτόματο.</small>
          <input type="text" name="slug" value="<?= e($post['slug']) ?>">
        </label>
        <label>Περίληψη<textarea name="excerpt" rows="2"><?= e($post['excerpt']) ?></textarea></label>
        <div class="editor-field">
          <label for="editor-toolbar">Κείμενο</label>
          <small class="muted">Χρησιμοποίησε τη γραμμή εργαλείων για μορφοποίηση.</small>
          <div id="editor-toolbar">
            <span class="ql-formats">
              <select class="ql-header">
                <option value="2">Επικεφαλίδα</option>
                <option value="3">Υπο-επικεφαλίδα</option>
                <option selected>Κείμενο</option>
              </select>
            </span>
            <span class="ql-formats">
              <button class="ql-bold"></button><button class="ql-italic"></button>
              <button class="ql-underline"></button><button class="ql-strike"></button>
            </span>
            <span class="ql-formats">
              <button class="ql-list" value="ordered"></button>
              <button class="ql-list" value="bullet"></button>
              <button class="ql-blockquote"></button>
            </span>
            <span class="ql-formats">
              <button class="ql-link"></button><button class="ql-image"></button>
            </span>
            <span class="ql-formats">
              <select class="ql-align">
                <option selected></option><option value="center"></option>
                <option value="right"></option><option value="justify"></option>
              </select>
            </span>
            <span class="ql-formats"><button class="ql-clean"></button></span>
          </div>
          <div id="editor"><?= $post['body'] ?></div>
          <textarea name="body" id="editor-input" hidden required><?= e($post['body']) ?></textarea>
        </div>
        <label>Εικόνα εξωφύλλου (JPG/PNG/WEBP, ≤5MB)
          <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp">
        </label>
        <?php if ($post['cover_image']): ?>
          <p><img src="<?= SITE_URL ?>/uploads/posts/<?= e($post['cover_image']) ?>" style="max-width:220px;border-radius:6px"></p>
        <?php endif; ?>
        <label class="inline">
          <input type="checkbox" name="published" value="1" <?= $post['published']?'checked':'' ?>> Δημοσιευμένο
        </label>
        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Αποθήκευση</button>
      </form>
    </div>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
    (function() {
      const quill = new Quill('#editor', { modules:{toolbar:'#editor-toolbar'}, placeholder:'Γράψε εδώ…', theme:'snow' });
      const hidden = document.getElementById('editor-input');
      hidden.value = quill.root.innerHTML;
      quill.on('text-change', () => {
        hidden.value = quill.getText().trim() === '' ? '' : quill.root.innerHTML;
      });
      document.querySelector('form.admin-form').addEventListener('submit', () => {
        hidden.value = quill.getText().trim() === '' ? '' : quill.root.innerHTML;
      });
    })();
    </script>
    <?php
    include __DIR__ . '/includes/admin_footer.php';
    exit;
}

$posts = $pdo->query('SELECT id, slug, title, published, created_at FROM posts ORDER BY created_at DESC')->fetchAll();
include __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-wrap">
  <div class="page-head">
    <h1>Άρθρα</h1>
    <a href="<?= SITE_URL ?>/admin/posts.php?action=new" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Νέο άρθρο</a>
  </div>
  <?php if ($flash): ?><p class="alert alert-success"><?= e($flash) ?></p><?php endif; ?>
  <table class="admin-table">
    <thead><tr><th>Τίτλος</th><th>Slug</th><th>Κατάσταση</th><th>Ημερομηνία</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($posts as $p): ?>
        <tr>
          <td><?= e($p['title']) ?></td>
          <td class="muted"><?= e($p['slug']) ?></td>
          <td><?= $p['published'] ? '<span class="pill pill-ok">Δημοσ.</span>' : '<span class="pill">Πρόχειρο</span>' ?></td>
          <td><?= e(date('d/m/Y', strtotime($p['created_at']))) ?></td>
          <td class="row-actions">
            <a href="<?= SITE_URL ?>/post.php?slug=<?= e($p['slug']) ?>" target="_blank">Δες</a>
            <a href="<?= SITE_URL ?>/admin/posts.php?action=edit&id=<?= (int)$p['id'] ?>">Επεξεργασία</a>
            <form method="post" style="display:inline" onsubmit="return confirm('Διαγραφή άρθρου;')">
              <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="op" value="delete">
              <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
              <button type="submit" class="link-danger">Διαγραφή</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$posts): ?><tr><td colspan="5" class="muted">Δεν υπάρχουν άρθρα.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/includes/admin_footer.php'; ?>
