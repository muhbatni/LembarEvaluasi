<?php
date_default_timezone_set('Asia/Jakarta');
require_once 'session.php';
include 'koneksi.php';

// Hanya admin
if (!isset($_SESSION['admin'])) {
    http_response_code(403);
    exit('Akses ditolak');
}

// Search (opsional)
$search = isset($_GET['search']) ? pg_escape_string($conn, $_GET['search']) : '';
$where = '';
if ($search !== '') {
    $where = "WHERE judul_pelatihan ILIKE '%$search%'
              OR nama ILIKE '%$search%'
              OR waktu ILIKE '%$search%'";
}

// Ambil SEMUA kolom
$query = "SELECT * FROM evaluasi $where ORDER BY created_at DESC";
$result = pg_query($conn, $query);

// Nama file
$filename = "lembarevaluasi_full_" . date('Ymd_His') . ".csv";

// Header CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Output stream
$output = fopen('php://output', 'w');

// BOM UTF-8 (Excel aman)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// ======================
// HEADER DINAMIS DARI DB
// ======================
$fields = pg_num_fields($result);
$headers = ['No'];

for ($i = 0; $i < $fields; $i++) {
    $headers[] = pg_field_name($result, $i);
}

fputcsv($output, $headers);

// ======================
// ISI DATA
// ======================
$no = 1;
while ($row = pg_fetch_assoc($result)) {

    $line = [$no++];

    foreach ($row as $value) {
        $line[] = $value;
    }

    fputcsv($output, $line);
}

fclose($output);
exit;
