<?php
include 'koneksi.php';

if (!isset($_GET['id'])) {
    header('Location: lihat_data.php');
    exit;
}

$id = (int) $_GET['id'];

// Ambil nama file sertifikasi sebelum dihapus
$query = "SELECT sertifikasi FROM evaluasi WHERE id = $id";
$result = pg_query($conn, $query);
$data = pg_fetch_assoc($result);

// Hapus file sertifikasi jika ada
if ($data && $data['sertifikasi']) {
    $file_path = 'uploads/' . $data['sertifikasi'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}

// Hapus data dari database
$delete_query = "DELETE FROM evaluasi WHERE id = $id";
$result = pg_query($conn, $delete_query);

if ($result) {
    header('Location: lihat_data.php?deleted=success');
} else {
    header('Location: lihat_data.php?deleted=error');
}

?>