<?php
/**
 * Database & site configuration for Γ.Α.Σ. Μαχητές Μεσολογγίου.
 *
 * Values are read from environment variables (Docker / Coolify friendly);
 * if an env var isn't set, XAMPP-style defaults are used so the project
 * still boots on a plain local install.
 */

function _env(string $key, string $default): string {
    $v = getenv($key);
    return $v !== false ? $v : $default;
}

define('DB_HOST',    _env('DB_HOST',    '127.0.0.1'));
define('DB_NAME',    _env('DB_NAME',    'gas_maxites'));
define('DB_USER',    _env('DB_USER',    'root'));
define('DB_PASS',    _env('DB_PASS',    'root'));
define('DB_CHARSET', 'utf8mb4');

define('SITE_NAME',       _env('SITE_NAME',       'Γ.Α.Σ. Μαχητές Μεσολογγίου'));
define('SITE_NAME_SHORT', _env('SITE_NAME_SHORT', 'ΜΑΧΗΤΕΣ'));
define('SITE_URL',        _env('SITE_URL',        'http://localhost/gas_maxites'));

// Salt for hashing visitor IPs (analytics). Change once for production.
define('IP_HASH_SALT', _env('IP_HASH_SALT', 'change-me-to-a-random-string-for-production'));

define('UPLOAD_DIR_GALLERY',  __DIR__ . '/../uploads/gallery');
define('UPLOAD_DIR_POSTS',    __DIR__ . '/../uploads/posts');
define('UPLOAD_DIR_ATHLETES', __DIR__ . '/../uploads/athletes');
define('UPLOAD_DIR_TROPHIES', __DIR__ . '/../uploads/trophies');
define('UPLOAD_DIR_PROGRAMS', __DIR__ . '/../uploads/programs');
define('MAX_UPLOAD_BYTES', 5 * 1024 * 1024); // 5 MB
