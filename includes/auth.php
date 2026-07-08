<?php
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) session_start();

function current_user(): ?array {
    if (empty($_SESSION['uid'])) return null;
    $s = db()->prepare('SELECT id, username FROM users WHERE id = ?');
    $s->execute([$_SESSION['uid']]);
    return $s->fetch() ?: null;
}
function require_admin(): void {
    if (!current_user()) {
        header('Location: ' . SITE_URL . '/admin/login.php');
        exit;
    }
}
function login(string $username, string $password): bool {
    $s = db()->prepare('SELECT id, password_hash FROM users WHERE username = ?');
    $s->execute([$username]);
    $row = $s->fetch();
    if (!$row || !password_verify($password, $row['password_hash'])) return false;
    session_regenerate_id(true);
    $_SESSION['uid'] = $row['id'];
    return true;
}
function logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_check(): void {
    $t = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $t)) {
        http_response_code(400);
        exit('Invalid CSRF token.');
    }
}
