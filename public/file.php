<?php
declare(strict_types=1);

session_start();
define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/src/includes/auth.php';

// kalau file sensitif, wajib login
require_admin();

$path = $_GET['path'] ?? '';
$path = str_replace(['..', '\\'], ['', '/'], $path); // anti traversal sederhana

$base = BASE_PATH . '/storage/uploads/';
$full = realpath($base . $path);

if ($full === false || strpos($full, realpath($base)) !== 0 || !is_file($full)) {
  http_response_code(404);
  exit('File not found');
}

$mime = mime_content_type($full) ?: 'application/octet-stream';
header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($full));
readfile($full);
