<?php
session_start();
include 'koneksi.php';

/* CEK LOGIN ADMIN */
if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit;
}

/* VALIDASI ID */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int) $_GET['id'];

/* AMBIL FILE SERTIFIKASI*/
$query = "SELECT sertifikasi FROM evaluasi WHERE id = $id";
$result = pg_query($conn, $query);

if (!$result || pg_num_rows($result) === 0) {
    header('Location: index.php?deleted=error');
    exit;
}

$data = pg_fetch_assoc($result);

/* HAPUS FILE (JIKA ADA) */
if (!empty($data['sertifikasi'])) {
    $file_path = 'uploads/' . $data['sertifikasi'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}

/* HAPUS DATA DATABASE */
$delete_query = "DELETE FROM evaluasi WHERE id = $id";
$delete_result = pg_query($conn, $delete_query);

/* REDIRECT HASIL */
if ($delete_result) {
    header('Location: index.php?deleted=success');
} else {
    header('Location: index.php?deleted=error');
}
exit;
?>
