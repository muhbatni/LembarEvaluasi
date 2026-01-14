<?php
session_start();
require_once 'session.php';
include 'koneksi.php';

if (!isset($_SESSION['admin'])) {
    die('Akses ditolak');
}

// Filter
$filter = $_GET['filter'] ?? 'all';
$year   = $_GET['year'] ?? date('Y');
$month  = $_GET['month'] ?? date('m');

$where = '';
$title = 'Semua Data';

if ($filter === 'year') {
    $where = "WHERE EXTRACT(YEAR FROM created_at) = '$year'";
    $title = "Tahun $year";
} elseif ($filter === 'month') {
    $where = "WHERE EXTRACT(YEAR FROM created_at) = '$year' AND EXTRACT(MONTH FROM created_at) = '$month'";
    $title = "Bulan $month / $year";
}

$query = "
    SELECT 
        DATE(created_at) AS tanggal,
        ROUND(AVG(
            (
                tema_pelatihan + ketepatan_waktu + suasana + kelengkapan_materi +
                servis_penyelenggara + alat_bantu_pelaksanaan + penguasaan_masalah_pembicara +
                cara_penyajian_pembicara + manfaat_materi + interaksi_peserta_pembicara +
                penguasaan_masalah_narasumber + cara_penyajian_narasumber + makanan +
                sound_system + layanan_hotel
            ) / 15.0
        ),1) AS rata_rata
    FROM evaluasi
    $where
    GROUP BY tanggal
    ORDER BY tanggal ASC
";

$result = pg_query($conn, $query);
$data = [];
while ($row = pg_fetch_assoc($result)) {
    $data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Grafik Evaluasi</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #F5F7FB;
            padding: 30px;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            max-width: 1200px;
            margin: auto;
        }
        h2 {
            margin-bottom: 20px;
        }
        .filter {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        select, button {
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        button {
            background: #667eea;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📈 Grafik Rata-rata Evaluasi (<?= $title ?>)</h2>

    <form method="GET" class="filter">
        <select name="filter">
            <option value="all" <?= $filter=='all'?'selected':'' ?>>Semua</option>
            <option value="year" <?= $filter=='year'?'selected':'' ?>>Per Tahun</option>
            <option value="month" <?= $filter=='month'?'selected':'' ?>>Per Bulan</option>
        </select>

        <select name="year">
            <?php for ($y = date('Y'); $y >= date('Y')-5; $y--): ?>
                <option value="<?= $y ?>" <?= $year==$y?'selected':'' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>

        <select name="month">
            <?php for ($m=1; $m<=12; $m++): $mm=str_pad($m,2,'0',STR_PAD_LEFT); ?>
                <option value="<?= $mm ?>" <?= $month==$mm?'selected':'' ?>><?= $mm ?></option>
            <?php endfor; ?>
        </select>

        <button type="submit">Terapkan</button>
    </form>

    <canvas id="grafikEvaluasi"></canvas>
</div>

<script>
const labels = <?= json_encode(array_column($data,'tanggal')) ?>;
const values = <?= json_encode(array_column($data,'rata_rata')) ?>;

new Chart(document.getElementById('grafikEvaluasi'), {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Rata-rata Evaluasi',
            data: values,
            borderWidth: 3,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                min: 0,
                max: 5
            }
        }
    }
});
</script>

</body>
</html>