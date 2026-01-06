<?php
include 'koneksi.php';

// Pagination
$limit = 10; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search functionality
$search = isset($_GET['search']) ? pg_escape_string($conn, $_GET['search']) : '';
$where = "";
if ($search != '') {
    $where = "WHERE judul_pelatihan ILIKE '%$search%' OR nama ILIKE '%$search%'";
}

// Count total records
$count_query = "SELECT COUNT(*) as total FROM evaluasi $where";
$count_result = pg_query($conn, $count_query);
$total_records = pg_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $limit);

// Get data with pagination
$query = "SELECT * FROM evaluasi $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = pg_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Evaluasi Pelatihan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #667eea;
        }

        h1 {
            color: #333;
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-box input {
            flex: 1;
            padding: 12px 20px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .search-box input:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
            font-weight: 500;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn:hover {
            background-color: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        }

        .btn-primary {
            background-color: #667eea;
        }

        .btn-primary:hover {
            background-color: #5568d3;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            font-size: 32px;
            margin-bottom: 5px;
        }

        .stat-card p {
            font-size: 14px;
            opacity: 0.9;
        }

        .table-container {
            overflow-x: auto;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }

        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        tr:hover {
            background-color: #f8f9ff;
        }

        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .no-data-icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }

        .badge-1 {
            background-color: #ffebee;
            color: #c62828;
        }

        .badge-2 {
            background-color: #fff3e0;
            color: #e65100;
        }

        .badge-3 {
            background-color: #fff9c4;
            color: #f57f17;
        }

        .badge-4 {
            background-color: #f1f8e9;
            color: #558b2f;
        }

        .badge-5 {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .action-btn {
            padding: 8px 16px;
            margin: 2px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            font-weight: 500;
        }

        .btn-detail {
            background-color: #2196F3;
            color: white;
        }

        .btn-detail:hover {
            background-color: #0b7dda;
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(33, 150, 243, 0.3);
        }

        .btn-delete {
            background-color: #f44336;
            color: white;
        }

        .btn-delete:hover {
            background-color: #da190b;
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(244, 67, 54, 0.3);
        }

        .file-link {
            color: #2196F3;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
        }

        .file-link:hover {
            text-decoration: underline;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .pagination a,
        .pagination span {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
        }

        .pagination a:hover {
            background-color: #667eea;
            color: white;
            border-color: #667eea;
        }

        .pagination .active {
            background-color: #667eea;
            color: white;
            border-color: #667eea;
        }

        .pagination .disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .header-actions {
                width: 100%;
                flex-direction: column;
            }

            .btn {
                width: 100%;
                text-align: center;
            }

            table {
                font-size: 12px;
            }

            th,
            td {
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <?php if (isset($_GET['deleted'])): ?>
            <?php if ($_GET['deleted'] == 'success'): ?>
                <div class="alert alert-success">
                    ✓ Data berhasil dihapus!
                </div>
            <?php else: ?>
                <div class="alert alert-error">
                    ✗ Gagal menghapus data!
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="header">
            <h1>📊 Data Evaluasi Pelatihan</h1>
            <div class="header-actions">
                <a href="form.php" class="btn">+ Tambah Evaluasi Baru</a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-container">
            <div class="stat-card">
                <h3><?= $total_records ?></h3>
                <p>Total Evaluasi</p>
            </div>
            <div class="stat-card">
                <h3><?= $total_pages ?></h3>
                <p>Total Halaman</p>
            </div>
            <div class="stat-card">
                <h3><?= $page ?></h3>
                <p>Halaman Saat Ini</p>
            </div>
        </div>

        <!-- Search Box -->
        <form method="GET" class="search-box">
            <input type="text" name="search" placeholder="🔍 Cari berdasarkan judul pelatihan atau nama..."
                value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary">Cari</button>
            <?php if ($search != ''): ?>
                <a href="lihat_data.php" class="btn" style="background-color: #f44336;">Reset</a>
            <?php endif; ?>
        </form>

        <?php if (pg_num_rows($result) > 0): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Judul Pelatihan</th>
                            <th>Nama</th>
                            <th>Waktu</th>
                            <th style="text-align: center;">Sertifikat</th>
                            <th style="text-align: center;">Rata-rata</th>
                            <th>Tanggal Input</th>
                            <th style="text-align: center; width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = $offset + 1;
                        while ($row = pg_fetch_assoc($result)):
                            // Hitung rata-rata nilai dari semua aspek
                            $total_nilai = $row['tema_pelatihan'] + $row['ketepatan_waktu'] +
                                $row['suasana'] + $row['kelengkapan_materi'] +
                                $row['servis_penyelenggara'] + $row['alat_bantu_pelaksanaan'] +
                                $row['penguasaan_masalah_pembicara'] + $row['cara_penyajian_pembicara'] +
                                $row['manfaat_materi'] + $row['interaksi_peserta_pembicara'] +
                                $row['penguasaan_masalah_narasumber'] + $row['cara_penyajian_narasumber'] +
                                $row['makanan'] + $row['sound_system'] + $row['layanan_hotel'];
                            $rata_rata = round($total_nilai / 15, 1);
                        ?>
                            <tr>
                                <td style="font-weight: bold; color: #667eea;"><?= $no++ ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($row['judul_pelatihan']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td><?= htmlspecialchars($row['waktu']) ?></td>
                                <td style="text-align: center;">
                                    <?php if ($row['sertifikasi']): ?>
                                        <a href="uploads/<?= htmlspecialchars($row['sertifikasi']) ?>" target="_blank"
                                            class="file-link" title="Lihat Sertifikat">
                                            📄 Lihat
                                        </a>
                                    <?php else: ?>
                                        <span style="color: #999;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge badge-<?= floor($rata_rata) ?>">
                                        ⭐ <?= $rata_rata ?> / 5.0
                                    </span>
                                </td>
                                <td>
                                    <?= date('d/m/Y', strtotime($row['created_at'])) ?><br>
                                    <small style="color: #999;"><?= date('H:i', strtotime($row['created_at'])) ?> WIB</small>
                                </td>
                                <td style="text-align: center;">
                                    <a href="detail.php?id=<?= $row['id'] ?>" class="action-btn btn-detail">📋 Detail</a>
                                    <a href="delete.php?id=<?= $row['id'] ?>" class="action-btn btn-delete"
                                        onclick="return confirm('Yakin ingin menghapus data evaluasi dari <?= htmlspecialchars($row['nama']) ?>?')">🗑️
                                        Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=1<?= $search ? '&search=' . urlencode($search) : '' ?>">« Pertama</a>
                        <a href="?page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">‹ Sebelumnya</a>
                    <?php else: ?>
                        <span class="disabled">« Pertama</span>
                        <span class="disabled">‹ Sebelumnya</span>
                    <?php endif; ?>

                    <?php
                    // Show page numbers
                    $start = max(1, $page - 2);
                    $end = min($total_pages, $page + 2);

                    for ($i = $start; $i <= $end; $i++):
                    ?>
                        <?php if ($i == $page): ?>
                            <span class="active"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">Berikutnya ›</a>
                        <a href="?page=<?= $total_pages ?><?= $search ? '&search=' . urlencode($search) : '' ?>">Terakhir »</a>
                    <?php else: ?>
                        <span class="disabled">Berikutnya ›</span>
                        <span class="disabled">Terakhir »</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="no-data">
                <div class="no-data-icon">📝</div>
                <h2>Belum Ada Data Evaluasi</h2>
                <?php if ($search != ''): ?>
                    <p>Tidak ditemukan hasil untuk pencarian "<?= htmlspecialchars($search) ?>"</p>
                    <a href="lihat_data.php" class="btn" style="margin-top: 20px;">Lihat Semua Data</a>
                <?php else: ?>
                    <p>Mulai dengan mengisi form evaluasi pelatihan.</p>
                    <a href="form.php" class="btn" style="margin-top: 20px;">Isi Form Evaluasi</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>