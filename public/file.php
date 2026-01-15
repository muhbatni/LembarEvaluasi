<?php
declare(strict_types=1);

session_start();
define('BASE_PATH', dirname(__DIR__));

$path = $_GET['path'] ?? '';

// Normalisasi + anti traversal
$path = str_replace(["\0", "\\", ".."], ["", "/", ""], $path);
$path = ltrim($path, "/");

// Public allowlist
$isPublic =
    str_starts_with($path, 'sertifikat/') ||
    str_starts_with($path, 'ttd/');

if (!$isPublic) {
    require BASE_PATH . '/src/includes/auth.php';
    require_admin();
}

$baseDir  = BASE_PATH . '/storage/uploads/';
$baseReal = realpath($baseDir);
$full     = realpath($baseDir . $path);

if ($baseReal === false || $full === false || strpos($full, $baseReal) !== 0 || !is_file($full)) {
    http_response_code(404);
    exit('File not found');
}

$mime = mime_content_type($full) ?: 'application/octet-stream';
$size = filesize($full);
$filename = basename($full);

// Header dasar
header('X-Content-Type-Options: nosniff');
header('Content-Type: ' . $mime);
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Accept-Ranges: bytes');
header('Cache-Control: private, max-age=3600');

// === RANGE SUPPORT (penting untuk PDF.js) ===
$range = $_SERVER['HTTP_RANGE'] ?? null;

if ($range && preg_match('/bytes=(\d*)-(\d*)/', $range, $m)) {
    $start = ($m[1] === '') ? 0 : (int)$m[1];
    $end   = ($m[2] === '') ? ($size - 1) : (int)$m[2];

    if ($start > $end || $end >= $size) {
        http_response_code(416);
        header("Content-Range: bytes */{$size}");
        exit;
    }

    $length = $end - $start + 1;

    http_response_code(206);
    header("Content-Range: bytes {$start}-{$end}/{$size}");
    header("Content-Length: {$length}");

    $fp = fopen($full, 'rb');
    fseek($fp, $start);

    $buffer = 8192;
    while (!feof($fp) && $length > 0) {
        $read = ($length > $buffer) ? $buffer : $length;
        $data = fread($fp, $read);
        echo $data;
        flush();
        $length -= strlen($data);
    }
    fclose($fp);
    exit;
}

// Non-range (normal)
header('Content-Length: ' . $size);
readfile($full);
exit;
