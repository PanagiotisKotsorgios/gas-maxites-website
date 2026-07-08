<?php
require_once __DIR__ . '/db.php';

function log_pageview(): void {
    $path = $_SERVER['REQUEST_URI'] ?? '/';
    if (strpos($path, '/admin/') !== false) return;
    $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);
    if (preg_match('~bot|crawler|spider|slurp|facebookexternalhit~i', $ua)) return;

    $ip  = $_SERVER['REMOTE_ADDR'] ?? '';
    $ref = substr($_SERVER['HTTP_REFERER'] ?? '', 0, 500);
    $iph = hash('sha256', $ip . IP_HASH_SALT);
    try {
        db()->prepare('INSERT INTO pageviews (path, referrer, ip_hash, user_agent) VALUES (?,?,?,?)')
            ->execute([substr($path, 0, 255), $ref, $iph, $ua]);
    } catch (Throwable $ex) {
        // never break the site on analytics failure
    }
}
