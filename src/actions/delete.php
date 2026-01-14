<?php
date_default_timezone_set('Asia/Jakarta');

require_once BASE_PATH . '/src/includes/session.php';
require_once BASE_PATH . '/src/includes/koneksi.php';
require_once BASE_PATH . '/src/includes/auth.php';

// wajib login admin
require_admin();

/* VALIDASI ID */
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: index.php?deleted=error');
    exit;
}

/* AMBIL PATH FILE DARI DB */
$res = pg_query_params($conn, "SELECT sertifikasi FROM evaluasi WHERE id = $1", [$id]);
if (!$res || pg_num_rows($res) === 0) {
    header('Location: index.php?deleted=error');
    exit;
}

$data = pg_fetch_assoc($res);

/* HAPUS FILE (JIKA ADA) */
if (!empty($data['sertifikasi'])) {
    // DB menyimpan path RELATIF dari storage/uploads
    $fullPath = BASE_PATH . '/storage/uploads/' . ltrim($data['sertifikasi'], '/\\');

    // normalisasi agar aman di Windows
    $fullPath = str_replace(['\\', '//'], ['/', '/'], $fullPath);

    if (is_file($fullPath)) {
        @unlink($fullPath);
    }
}

/* HAPUS DATA DATABASE */
$del = pg_query_params($conn, "DELETE FROM evaluasi WHERE id = $1", [$id]);

/* REDIRECT HASIL */
if ($del) {
    header('Location: index.php?deleted=success');
} else {
    header('Location: index.php?deleted=error');
}
exit;
