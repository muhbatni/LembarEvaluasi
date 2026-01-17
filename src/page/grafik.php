<?php
require_once BASE_PATH . '/src/includes/session.php';
require_once BASE_PATH . '/src/includes/koneksi.php';

if (!isset($_SESSION['admin'])) {
    die('Akses ditolak');
}

//FILTER (bisa kombinasi)
$year  = $_GET['year']  ?? '';
$month = $_GET['month'] ?? '';
$nama  = $_GET['nama']  ?? '';
$judul = $_GET['judul'] ?? '';

// Fungsi konversi string waktu ke date (pgadmin varchar ke date)
$waktuSql = "
to_date(
    regexp_replace(
        replace(
            replace(
                replace(
                    replace(
                        replace(
                            replace(
                                replace(
                                    replace(
                                        replace(
                                            replace(
                                                replace(
                                                    replace(
                                                        trim(waktu),
                                                        'Januari','01'
                                                    ),
                                                    'Februari','02'
                                                ),
                                                'Maret','03'
                                            ),
                                            'April','04'
                                        ),
                                        'Mei','05'
                                    ),
                                    'Juni','06'
                                ),
                                'Juli','07'
                            ),
                            'Agustus','08'
                        ),
                        'September','09'
                    ),
                    'Oktober','10'
                ),
                'November','11'
            ),
            'Desember','12'
        ),
        '.*?([0-9]{1,2} [0-9]{2} [0-9]{4}).*',
        '\\1'
    ),
    'DD MM YYYY'
)
";

//validasi format waktu
$validWaktu = "waktu ~ '[0-9]{1,2} (Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember) [0-9]{4}'";

// 1) Dropdown Tahun (softcode dari DB)
$listYear = [];
$qYear = "SELECT DISTINCT EXTRACT(YEAR FROM $waktuSql)::int AS y
          FROM evaluasi
          WHERE $validWaktu
          ORDER BY y DESC";
$rYear = pg_query($conn, $qYear);
while ($row = pg_fetch_assoc($rYear)) {
    $listYear[] = (int)$row['y'];
}

// 2) Dropdown Bulan (tergantung Tahun)
$listMonth = [];
if ($year !== '') {
    $qMonth = "SELECT DISTINCT EXTRACT(MONTH FROM $waktuSql)::int AS m
                FROM evaluasi
                WHERE
             $validWaktu
             AND EXTRACT(YEAR FROM $waktuSql)::int = $1
           ORDER BY m ASC";
    $rMonth = pg_query_params($conn, $qMonth, [(int)$year]);
    while ($row = pg_fetch_assoc($rMonth)) {
        $listMonth[] = (int)$row['m'];
    }

    if ($month !== '' && !in_array((int)$month, $listMonth, true)) {
        $month = '';
    }
}

// 3) Dropdown Nama & Judul (softcode dari DB)
$listNama = [];
$qNama = "SELECT DISTINCT nama FROM evaluasi WHERE nama IS NOT NULL AND nama <> '' ORDER BY nama ASC";
$rNama = pg_query($conn, $qNama);
while ($row = pg_fetch_assoc($rNama)) {
    $listNama[] = $row['nama'];
}

$listJudul = [];
$qJudul = "SELECT DISTINCT judul_pelatihan FROM evaluasi WHERE judul_pelatihan IS NOT NULL AND judul_pelatihan <> '' ORDER BY judul_pelatihan ASC";
$rJudul = pg_query($conn, $qJudul);
while ($row = pg_fetch_assoc($rJudul)) {
    $listJudul[] = $row['judul_pelatihan'];
}

// ------------------------
// 4) Query data grafik
// ------------------------
$conditions = [];
$params = [];
$idx = 1;

$titleParts = [];

if ($year !== '') {
    $conditions[] = "EXTRACT(YEAR FROM $waktuSql)::int = $" . $idx;
    $params[] = (int)$year;
    $titleParts[] = "Tahun $year";
    $idx++;
}

if ($month !== '') {
    $conditions[] = "EXTRACT(MONTH FROM $waktuSql)::int = $" . $idx;
    $params[] = (int)$month;
    $titleParts[] = "Bulan " . str_pad((string)$month, 2, '0', STR_PAD_LEFT);
    $idx++;
}

if ($nama !== '') {
    $conditions[] = "nama = $" . $idx;
    $params[] = $nama;
    $titleParts[] = "Nama: $nama";
    $idx++;
}

if ($judul !== '') {
    $conditions[] = "judul_pelatihan = $" . $idx;
    $params[] = $judul;
    $titleParts[] = "Judul: $judul";
    $idx++;
}

$where = '';
if (!empty($conditions)) {
    $where = 'WHERE ' . implode(' AND ', $conditions);
}
if ($where !== '') {
    $where .= " AND $validWaktu";
} else {
    $where = "WHERE $validWaktu";
}

$title = !empty($titleParts) ? implode(' | ', $titleParts) : 'Semua Data';

$resetUrl = "index.php?p=grafik";

// MODE = HORIZONTAL BAR PER PESERTA

//batas max peserta (supaya rapi)
$MAX_PESERTA = 30; 

//query ambil jumlah pelatihan per peserta pada periode filter (bulan/tahun sudah difilter lewat $where)
$query = "
    SELECT
        MIN(nama) AS peserta,
        COUNT(*)::int AS jumlah_pelatihan
    FROM evaluasi
    $where
    GROUP BY lower(trim(nama))
    ORDER BY jumlah_pelatihan DESC, peserta ASC
    LIMIT $MAX_PESERTA
";

$result = pg_query_params($conn, $query, $params);
if (!$result) {
    die("Query gagal: " . pg_last_error($conn));
}

//labels = nama peserta, values = jumlah pelatihan
$labels = [];
$values = [];
while ($row = pg_fetch_assoc($result)) {
    $labels[] = $row['peserta'];
    $values[] = (int)$row['jumlah_pelatihan'];
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
            flex-wrap: wrap;
        }

        select,
        button,
        a.btn {
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

        a.btn {
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .btn-reset {
            background: #f44336;
            color: #fff;
            border: none;
        }

        .btn-reset:hover {
            background: #da190b;
        }

        .hint {
            color: #666;
            margin-top: -10px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .topbar {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 12px;
        }

        .btn-back {
            background: transparent;
            color: #667eea;
            border: 1.5px solid #667eea;
        }

        .btn-back:hover {
            background: #667eea;
            color: white;
        }
    </style>
</head>

<body>

    <div class="container">

        <div class="topbar">
            <a href="index.php" class="btn btn-back">← Kembali ke Index</a>
        </div>

        <h2>📊 Grafik Evaluasi Pelatihan (<?= htmlspecialchars($title) ?>)</h2>
        <div class="hint">
            Bar kesamping: tiap bar = nama peserta, nilai = jumlah pelatihan pada periode yang dipilih (top <?= (int)$MAX_PESERTA ?>).
        </div>

        <form method="GET" action="index.php" class="filter">
            <input type="hidden" name="p" value="grafik">

            <!-- YEAR -->
            <select name="year" onchange="this.form.submit()">
                <option value="">Semua Tahun</option>
                <?php foreach ($listYear as $y): ?>
                    <option value="<?= $y ?>" <?= ($year !== '' && (int)$year === $y) ? 'selected' : '' ?>>
                        <?= $y ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- MONTH -->
            <select name="month" <?= ($year === '') ? 'disabled' : '' ?>>
                <option value="">
                    <?= ($year === '') ? 'Pilih Tahun dulu' : 'Semua Bulan' ?>
                </option>
                <?php foreach ($listMonth as $m): $mm = str_pad((string)$m, 2, '0', STR_PAD_LEFT); ?>
                    <option value="<?= $m ?>" <?= ($month !== '' && (int)$month === $m) ? 'selected' : '' ?>>
                        <?= $mm ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- NAMA -->
            <select name="nama">
                <option value="">Semua Nama</option>
                <?php foreach ($listNama as $nm): ?>
                    <option value="<?= htmlspecialchars($nm) ?>" <?= ($nama === $nm) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($nm) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- JUDUL -->
            <select name="judul">
                <option value="">Semua Judul</option>
                <?php foreach ($listJudul as $jd): ?>
                    <option value="<?= htmlspecialchars($jd) ?>" <?= ($judul === $jd) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($jd) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Terapkan</button>
            <a href="<?= $resetUrl ?>" class="btn btn-reset">Reset</a>
        </form>

        <canvas id="grafikEvaluasi"></canvas>
    </div>

    <script>
        //vertical bar (nama = X, jumlah = Y)
        const labels = <?= json_encode($labels) ?>;
        const values = <?= json_encode($values) ?>;

        //palette profesional
        const palette = [
            '#334155', '#475569', '#64748b', '#0f766e', '#0ea5e9',
            '#2563eb', '#4f46e5', '#7c3aed', '#a21caf', '#be123c',
            '#9a3412', '#a16207', '#15803d', '#0f172a', '#1f2937'
        ];

        const barColors = labels.map((_, i) => palette[i % palette.length]);

        new Chart(document.getElementById('grafikEvaluasi'), {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Jumlah Pelatihan',
                    data: values,
                    backgroundColor: barColors, 
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.raw} pelatihan`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        ticks: {
                            autoSkip: false, 
                            maxRotation: 45, //nama panjang gak tabrakan
                            minRotation: 0 
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>