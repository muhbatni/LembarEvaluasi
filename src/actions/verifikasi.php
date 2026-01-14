<?php
date_default_timezone_set('Asia/Jakarta');

require_once BASE_PATH . '/src/includes/session.php';
require_once BASE_PATH . '/src/includes/koneksi.php';
require_once BASE_PATH . '/src/includes/auth.php';

// wajib login admin
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = $_GET['action'] ?? '';

if ($id <= 0 || !in_array($action, ['verify', 'unverify'], true)) {
    http_response_code(400);
    exit('Bad request');
}

$status = ($action === 'verify') ? 't' : 'f';

$result = pg_query_params(
    $conn,
    "UPDATE evaluasi SET is_verified = $1 WHERE id = $2",
    [$status, $id]
);

if (!$result) {
    http_response_code(500);
    exit('Gagal update verifikasi');
}

// kembali ke halaman sebelumnya
$back = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header("Location: $back");
exit;
