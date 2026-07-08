<?php
require_once __DIR__ . '/config.php';

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
    return $pdo;
}

function setting(string $key, string $default = ''): string {
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (db()->query('SELECT `key`,`value` FROM settings')->fetchAll() as $r) {
            $cache[$r['key']] = $r['value'];
        }
    }
    // Empty stored value is treated as "not set" — fall back to the code default.
    $val = $cache[$key] ?? '';
    return $val !== '' ? $val : $default;
}

function e(?string $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function slugify(string $text): string {
    if (function_exists('transliterator_transliterate')) {
        $text = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $text);
    } else {
        $text = mb_strtolower($text, 'UTF-8');
    }
    $text = preg_replace('~[^a-z0-9]+~', '-', $text);
    $text = trim($text, '-');
    return $text ?: 'item-' . time();
}

/**
 * Return the image URL for a program row.
 * Uploaded image wins; otherwise pick a themed default based on the name.
 */
function program_image_url(array $p): string {
    if (!empty($p['image'])) return SITE_URL . '/uploads/programs/' . $p['image'];
    $n = mb_strtolower((string)($p['name'] ?? ''), 'UTF-8');
    $base = 'https://images.unsplash.com/';
    $qs   = '?ixlib=rb-4.0.3&auto=format&fit=crop&w=900&q=80';
    if (mb_strpos($n, 'αυτοάμυνα')     !== false) return $base . 'photo-1549060279-7e168fcee0c2' . $qs;
    if (mb_strpos($n, 'αγωνιστικό')    !== false) return $base . 'photo-1555597673-b21d5c935865' . $qs;
    if (mb_strpos($n, 'εφηβ')          !== false) return $base . 'photo-1517438322307-e67111335449' . $qs;
    if (mb_strpos($n, 'ενήλικες')      !== false) return $base . 'photo-1571019614242-c5c5dee9f50b' . $qs;
    if (mb_strpos($n, 'προχωρημένων') !== false) return $base . 'photo-1544717305-2782549b5136' . $qs;
    if (mb_strpos($n, 'αρχαρίων')     !== false) return $base . 'photo-1526401845248-2fb4fbf6a25e' . $qs;
    if (mb_strpos($n, 'παιδικό')       !== false) return $base . 'photo-1544161515-4ab6ce6db874' . $qs;
    return $base . 'photo-1594381898411-846e7d193883' . $qs;
}

/** Turn a YouTube URL into an embed URL (works for youtu.be + watch?v=). */
function youtube_embed(string $url): ?string {
    if (preg_match('~youtu\.be/([A-Za-z0-9_-]{11})~', $url, $m)) return 'https://www.youtube.com/embed/' . $m[1];
    if (preg_match('~[?&]v=([A-Za-z0-9_-]{11})~', $url, $m)) return 'https://www.youtube.com/embed/' . $m[1];
    if (preg_match('~youtube\.com/shorts/([A-Za-z0-9_-]{11})~', $url, $m)) return 'https://www.youtube.com/embed/' . $m[1];
    return null;
}
