<?php
// File pengaturan: membaca data rahasia dari .env lalu menyiapkan koneksi & email.

error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Jakarta');

// baca isi file .env
$file_env = __DIR__ . '/../.env';
if (file_exists($file_env)) {
    $baris = file($file_env, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($baris as $isi) {
        if (substr(trim($isi), 0, 1) == '#') {
            continue;
        }
        list($nama, $nilai) = explode('=', $isi, 2);
        $_ENV[trim($nama)] = trim($nilai);
    }
}

function env($key, $default = '')
{
    return isset($_ENV[$key]) ? $_ENV[$key] : $default;
}

define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));
define('DB_NAME', env('DB_NAME', 'bengkel_queue'));

define('SMTP_HOST', env('SMTP_HOST', 'smtp.gmail.com'));
define('SMTP_PORT', env('SMTP_PORT', 587));
define('SMTP_USER', env('SMTP_USER', ''));
define('SMTP_PASS', env('SMTP_PASS', ''));
define('SMTP_NAMA', env('SMTP_NAMA', 'Bengkel Jaya Motor'));

require_once __DIR__ . '/../vendor/autoload.php';
