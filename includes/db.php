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

/** Turn a YouTube URL into an embed URL (works for youtu.be + watch?v=). */
function youtube_embed(string $url): ?string {
    if (preg_match('~youtu\.be/([A-Za-z0-9_-]{11})~', $url, $m)) return 'https://www.youtube.com/embed/' . $m[1];
    if (preg_match('~[?&]v=([A-Za-z0-9_-]{11})~', $url, $m)) return 'https://www.youtube.com/embed/' . $m[1];
    if (preg_match('~youtube\.com/shorts/([A-Za-z0-9_-]{11})~', $url, $m)) return 'https://www.youtube.com/embed/' . $m[1];
    return null;
}
