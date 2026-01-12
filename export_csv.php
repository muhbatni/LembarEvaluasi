<?php
date_default_timezone_set('Asia/Jakarta');
require_once 'session.php';
include 'koneksi.php';

// Hanya admin yang boleh export
if (!isset($_SESSION['admin'])) {
    die('Akses ditolak');
}

// Ambil search jika ada (opsional)
$search = isset($_GET['search']) ? pg_escape_string($conn, $_GET['search']) : '';
$where = '';
if ($search !== '') {
    $where = "WHERE judul_pelatihan ILIKE '%$search%' 
              OR nama ILIKE '%$search%' 
              OR waktu ILIKE '%$search%'";
}

// Query semua data (tanpa pagination)
$query = "SELECT * FROM evaluasi $where ORDER BY created_at DESC";
$result = pg_query($conn, $query);

// Nama file
$filename = "lembarevaluasi_" . date('Ymd_His') . ".csv";

// Header CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

// Output stream
$output = fopen('php://output', 'w');

// BOM UTF-8 agar Excel tidak rusak karakter
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Header kolom
fputcsv($output, [
    'No',
    'Judul Pelatihan',
    'Nama',
    'Waktu',
    'Rata-rata',
    'Status',
    'Tanggal Input'
]);

$no = 1;
while ($row = pg_fetch_assoc($result)) {

    // Hitung rata-rata (sama seperti di index.php)
    $total_nilai =
        $row['tema_pelatihan'] +
        $row['ketepatan_waktu'] +
        $row['suasana'] +
        $row['kelengkapan_materi'] +
        $row['servis_penyelenggara'] +
        $row['alat_bantu_pelaksanaan'] +
        $row['penguasaan_masalah_pembicara'] +
        $row['cara_penyajian_pembicara'] +
        $row['manfaat_materi'] +
        $row['interaksi_peserta_pembicara'] +
        $row['penguasaan_masalah_narasumber'] +
        $row['cara_penyajian_narasumber'] +
        $row['makanan'] +
        $row['sound_system'] +
        $row['layanan_hotel'];

    $rata_rata = round($total_nilai / 15, 1);

    fputcsv($output, [
        $no++,
        $row['judul_pelatihan'],
        $row['nama'],
        $row['waktu'],
        $rata_rata,
        ($row['is_verified'] === 't') ? 'Verified' : 'Pending',
        date('d/m/Y H:i', strtotime($row['created_at']))
    ]);
}

fclose($output);
exit;
