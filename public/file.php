<?php
declare(strict_types=1);

session_start();
define('BASE_PATH', dirname(__DIR__));

$path = $_GET['path'] ?? '';

// Normalisasi + anti traversal
$path = str_replace(["\0", "\\", ".."], ["", "/", ""], $path);
$path = ltrim($path, "/");

// Public allowlist (yang boleh diakses tanpa login)
$isPublic =
    str_starts_with($path, 'sertifikat/') ||
    str_starts_with($path, 'ttd/');

// Selain itu wajib admin
if (!$isPublic) {
    require BASE_PATH . '/src/includes/auth.php';
    require_admin();
}

$baseDir = BASE_PATH . '/storage/uploads/';
$baseReal = realpath($baseDir);
$full = realpath($baseDir . $path);

// Validasi path tetap di dalam uploads + file harus ada
if ($baseReal === false || $full === false || strpos($full, $baseReal) !== 0 || !is_file($full)) {
    http_response_code(404);
    exit('File not found');
}

// Tentukan MIME
$mime = mime_content_type($full) ?: 'application/octet-stream';

// Header aman untuk tampil inline (img/iframe)
header('X-Content-Type-Options: nosniff');
header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($full));
header('Content-Disposition: inline; filename="' . basename($full) . '"');

// (Opsional) cache ringan
header('Cache-Control: private, max-age=3600');

readfile($full);
exit;