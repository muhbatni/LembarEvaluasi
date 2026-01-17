<?php
require_once BASE_PATH . '/src/includes/session.php';
require_once BASE_PATH . '/src/includes/koneksi.php';

if (!isset($_SESSION['admin'])) {
    die('Akses ditolak');
}

/**
 * FILTER (bisa kombinasi)
 * - year: "2026"
 * - month: "1".."12" (diambil dari DB sesuai year)
 * - nama: string (dropdown dari DB)
 * - judul: string (dropdown dari DB)
 */
$year  = $_GET['year']  ?? '';
$month = $_GET['month'] ?? '';
$nama  = $_GET['nama']  ?? '';
$judul = $_GET['judul'] ?? '';

// Fungsi konversi string waktu ke date (pgadmin varchar ke date)
$waktuSql = "
to_date(
  (
    (regexp_match(waktu, '([0-9]{1,2})\\s+(Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)\\s+([0-9]{4})'))[1]
    || ' ' ||
    case (regexp_match(waktu, '([0-9]{1,2})\\s+(Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)\\s+([0-9]{4})'))[2]
      when 'Januari' then '01'
      when 'Februari' then '02'
      when 'Maret' then '03'
      when 'April' then '04'
      when 'Mei' then '05'
      when 'Juni' then '06'
      when 'Juli' then '07'
      when 'Agustus' then '08'
      when 'September' then '09'
      when 'Oktober' then '10'
      when 'November' then '11'
      when 'Desember' then '12'
    end
    || ' ' ||
    (regexp_match(waktu, '([0-9]{1,2})\\s+(Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)\\s+([0-9]{4})'))[3]
  ),
  'DD MM YYYY'
)
";

//validasi format waktu
$validWaktu = "waktu ~ '([0-9]{1,2})\\s+(Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)\\s+([0-9]{4})'";


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
// - kalau $year dipilih: hanya bulan yang ada di tahun tsb
// - kalau $year kosong: bisa tampil semua bulan yang ada di DB (opsional)
//   di sini saya buat: kalau year kosong -> bulan kosong (user pilih tahun dulu)
// ------------------------
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

    // kalau user sebelumnya pilih bulan yang tidak tersedia di tahun itu, reset
    if ($month !== '' && !in_array((int)$month, $listMonth, true)) {
        $month = '';
    }
}

// 3) Dropdown Nama & Judul (softcode dari DB)
// (tidak tergantung year supaya sederhana; kalau mau tergantung year juga bisa)

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
// 4) Query data grafik (rata-rata per bulan atau per tanggal)
// - Jika year dipilih => GROUP BY bulan (output bulan saja, seperti kamu minta)
// - Jika year kosong => GROUP BY tanggal (biar tetap bisa lihat tren harian semua data)
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
// wajib: filter data waktu yang valid supaya $waktuSql tidak error
if ($where !== '') {
    $where .= " AND $validWaktu";
} else {
    $where = "WHERE $validWaktu";
}


$title = !empty($titleParts) ? implode(' | ', $titleParts) : 'Semua Data';

// Expression rata-rata (biar gak ketik ulang)
$avgExpr = "ROUND(AVG((
    tema_pelatihan + ketepatan_waktu + suasana + kelengkapan_materi +
    servis_penyelenggara + alat_bantu_pelaksanaan + penguasaan_masalah_pembicara +
    cara_penyajian_pembicara + manfaat_materi + interaksi_peserta_pembicara +
    penguasaan_masalah_narasumber + cara_penyajian_narasumber + makanan +
    sound_system + layanan_hotel
) / 15.0), 1)";

// Kalau year dipilih -> output BULAN (sesuai permintaan)
if ($year !== '') {
    $query = "
        SELECT
            EXTRACT(MONTH FROM $waktuSql)::int AS label,
            $avgExpr AS rata_rata
        FROM evaluasi
        $where
        GROUP BY label
        ORDER BY label ASC
    ";
} else {
    // Kalau tidak pilih year -> output per TANGGAL (default semua data)
    $query = "
        SELECT
            DATE($waktuSql) AS label,
            $avgExpr AS rata_rata
        FROM evaluasi
        $where
        GROUP BY label
        ORDER BY label ASC
    ";
}

$result = pg_query_params($conn, $query, $params);
$data = [];
while ($row = pg_fetch_assoc($result)) {
    $data[] = $row;
}

// Labels untuk chart (kalau year dipilih, label bulan dipad jadi 01..12)
$labels = [];
foreach ($data as $r) {
    if ($year !== '') {
        $labels[] = str_pad((string)$r['label'], 2, '0', STR_PAD_LEFT);
    } else {
        $labels[] = $r['label'];
    }
}
$values = array_map('floatval', array_column($data, 'rata_rata'));

// ------------------------
// 5) Reset link yang "sesuai"
// - kalau user sedang filter apa pun, reset balik ke grafik kosong
// ------------------------
$resetUrl = "index.php?p=grafik";
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

        <h2>📊 Grafik Evaluasi (<?= htmlspecialchars($title) ?>)</h2>
        <div class="hint">
            Pilih Tahun untuk melihat ringkasan per bulan. Nama & Judul bisa digabung.
        </div>

        <form method="GET" action="index.php" class="filter">
            <input type="hidden" name="p" value="grafik">

            <!-- YEAR (softcode) -->
            <select name="year" onchange="this.form.submit()">
                <option value="">Semua Tahun</option>
                <?php foreach ($listYear as $y): ?>
                    <option value="<?= $y ?>" <?= ($year !== '' && (int)$year === $y) ? 'selected' : '' ?>>
                        <?= $y ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- MONTH (softcode, tergantung year) -->
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
        const labels = <?= json_encode($labels) ?>;
        const values = <?= json_encode($values) ?>;

        new Chart(document.getElementById('grafikEvaluasi'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Rata-rata Evaluasi',
                    data: values,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        min: 0,
                        max: 5,
                        ticks: {
                            stepSize: 0.5
                        }
                    }
                }
            }
        });
    </script>

</body>

</html>