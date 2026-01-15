<?php
date_default_timezone_set('Asia/Jakarta');
define('BASE_URL', '/LembarEvaluasi/public');
define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/src/includes/session.php';
require_once BASE_PATH . '/src/includes/koneksi.php';

// auto logout 1 hari
if (isset($_SESSION['admin'])) {
    if (isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] > 86400) {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit;
    }
    $_SESSION['LAST_ACTIVITY'] = time();
}

// routing view pages
$view = $_GET['p'] ?? 'home';

$routes = [
    'home'   => null, // handled below
    'form'   => BASE_PATH . '/src/page/form.php',
    'detail' => BASE_PATH . '/src/page/detail.php',
    'grafik' => BASE_PATH . '/src/page/grafik.php',
    //login routes
    'admin_login' => BASE_PATH . '/src/page/admin_login.php',
    'logout'      => BASE_PATH . '/src/actions/logout.php',
    //controller routes 
    'proses' => BASE_PATH . '/src/actions/proses.php',
    //action buttons
    'verifikasi' => BASE_PATH . '/src/actions/verifikasi.php',
    'delete' => BASE_PATH . '/src/actions/delete.php',
];

if ($view !== 'home') {
    if (!isset($routes[$view])) {
        http_response_code(404);
        exit('Page not found');
    }
    require $routes[$view];
    exit;
}

// Pagination
$limit = 5; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search functionality
$search = trim($_GET['search'] ?? '');

$params = [];
$where  = '';
if ($search !== '') {
    $where = "WHERE judul_pelatihan ILIKE $1 OR nama ILIKE $1 OR waktu ILIKE $1";
    $params[] = "%{$search}%";
}

// Count total records
$count_sql = "SELECT COUNT(*) as total FROM evaluasi $where";
$count_result = ($search !== '')
    ? pg_query_params($conn, $count_sql, $params)
    : pg_query($conn, $count_sql);

$total_records = (int) pg_fetch_assoc($count_result)['total'];
$total_pages = (int) ceil($total_records / $limit);

// Get data with pagination
if ($search !== '') {
    $sql = "SELECT * FROM evaluasi $where ORDER BY created_at DESC LIMIT $2 OFFSET $3";
    $result = pg_query_params($conn, $sql, [$params[0], $limit, $offset]);
} else {
    $sql = "SELECT * FROM evaluasi ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
    $result = pg_query($conn, $sql);
}

// Get data with pagination
$page = (isset($_GET['page']) && (int)$_GET['page'] > 0) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
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
            background: #F5F7FB;
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
            background-color: #667eea;
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
            background: #F0F2FF;
            color: #3F3D56;
            border: 1px solid #E2E4FF;
            box-shadow: none;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .stat-card h3 {
            color: #667eea;
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
            background: #ECEBFF;
            color: #3F3D56;
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
            background: #EEF2FF;
            color: #4338CA;
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
            background-color: #667eea;
        }

        .btn-detail:hover {
            background-color: #5568d3;
            box-shadow: none;
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

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s;
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            animation: slideDown 0.3s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .modal-header h2 {
            color: #f44336;
            font-size: 24px;
        }

        .modal-body {
            margin-bottom: 25px;
            line-height: 1.6;
            color: #666;
        }

        .modal-footer {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .close-btn {
            background-color: #999;
        }

        .close-btn:hover {
            background-color: #777;
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

            .modal-content {
                margin: 30% auto;
                width: 95%;
            }
        }

        /* Compact style */

        /* Tab */
        @media (max-width: 1024px) {
            .container {
                padding: 20px;
            }

            h1 {
                font-size: 22px;
            }

            th,
            td {
                padding: 10px;
                font-size: 13px;
            }

            .btn {
                padding: 10px 16px;
                font-size: 13px;
            }
        }

        /* Mobile */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 15px;
                border-radius: 10px;
            }

            .header {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }

            .header-actions {
                flex-direction: column;
                gap: 8px;
            }

            .search-box {
                flex-direction: column;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 12px;
            }

            th {
                font-size: 11px;
                white-space: nowrap;
            }

            td {
                font-size: 12px;
            }

            /* Table scroll on mobile */
            .table-container {
                overflow-x: auto;
            }

            /* Button aksi jadi vertikal */
            .action-btn {
                display: block;
                width: 100%;
                margin-bottom: 6px;
            }
        }

        /* Small Mobile */
        @media (max-width: 480px) {
            h1 {
                font-size: 18px;
            }

            .stat-card h3 {
                font-size: 26px;
            }

            .stat-card p {
                font-size: 12px;
            }

            .pagination a,
            .pagination span {
                padding: 6px 10px;
                font-size: 12px;
            }
        }

        .btn-admin {
            background: transparent;
            color: #667eea;
            border: 1.5px solid #667eea;
        }

        .btn-admin:hover {
            background: #667eea;
            color: white;
        }

        .btn-logout {
            background: transparent;
            color: #555;
            border: 1.5px solid #ccc;
        }

        .btn-logout:hover {
            background: #667eea;
            color: white;
            box-shadow: none;
        }

        /* CSS Modal Login */
        .modern-modal {
            padding: 32px;
            border-radius: 18px;
            max-width: 420px;
            animation: popIn 0.35s ease;
        }

        .modern-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .icon-circle {
            width: 60px;
            height: 60px;
            background: #667eea;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin: 0 auto 12px;
        }

        .modern-header h2 {
            font-size: 22px;
            color: #333;
            margin-bottom: 4px;
        }

        .modern-header p {
            font-size: 13px;
            color: #777;
        }

        .input-group {
            margin-bottom: 18px;
        }

        .input-group label {
            font-size: 13px;
            color: #555;
            display: block;
            margin-bottom: 6px;
        }

        .input-group input {
            width: 100%;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1.5px solid #ddd;
            font-size: 14px;
            transition: 0.3s;
        }

        .input-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
        }

        .error-text {
            color: #e53935;
            font-size: 13px;
            display: none;
            margin-top: 6px;
        }

        .modern-footer {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 25px;
        }

        .btn-outline {
            flex: 1;
            padding: 12px;
            border-radius: 10px;
            border: 1.5px solid #ccc;
            background: transparent;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-outline:hover {
            background: #f5f5f5;
        }

        .btn-solid {
            flex: 1;
            padding: 12px;
            border-radius: 10px;
            border: none;
            background: #667eea;
            color: white;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-solid:hover {
            background: #5568d3;
        }

        @keyframes popIn {
            from {
                transform: scale(0.9);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        th.custom-header {
            background: #ECEBFF !important;
        }

        /* Button Verifikasi */
        .btn-verify {
            background: #E6F4EA;
            color: #2E7D32;
        }

        .btn-verify:hover {
            background: #C8E6C9;
            box-shadow: none;
        }

        /* Button Batal Verifikasi */
        .btn-unverify {
            background: #FFF3E0;
            color: #EF6C00;
        }

        .btn-unverify:hover {
            background: #FFE0B2;
            box-shadow: none;
        }

        .admin-layout {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: flex-end;
        }

        .admin-top,
        .admin-bottom {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
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
            <div class="header-actions admin-layout">

                <!-- BARIS ATAS -->
                <div class="admin-top">
                    <a href="index.php?p=form" class="btn">
                        + Tambah Evaluasi
                    </a>

                    <?php if (isset($_SESSION['admin'])): ?>
                        <span class="btn btn-admin">
                            👔 <?= $_SESSION['admin'] ?>
                        </span>
                        <a href="index.php?p=logout" class="btn btn-logout">
                            Logout
                        </a>
                    <?php else: ?>
                        <button onclick="openAdminModal()" class="btn btn-admin">
                            🔐 Login Admin
                        </button>
                    <?php endif; ?>
                </div>

                <!-- BARIS BAWAH -->
                <?php if (isset($_SESSION['admin'])): ?>
                    <div class="admin-bottom">
                        <a href="index.php?p=grafik" class="btn btn-secondary">
                            📊 Grafik
                        </a>

                        <a href="export_csv.php<?= $search ? '?search=' . urlencode($search) : '' ?>"
                            class="btn btn-primary">
                            📥 Export CSV
                        </a>
                    </div>
                <?php endif; ?>
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
                <a href="index.php" class="btn" style="background-color: #f44336;">Reset</a>
            <?php endif; ?>
        </form>

        <?php if (pg_num_rows($result) > 0): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th class="custom-header" style="width: 50px;">No</th>
                            <th class="custom-header">Judul Pelatihan</th>
                            <th class="custom-header">Nama</th>
                            <th class="custom-header">Waktu</th>
                            <th class="custom-header" style="text-align: center;">Sertifikat</th>
                            <th class="custom-header" style="text-align: center;">Rata-rata</th>
                            <th class="custom-header">Tanggal Input</th>
                            <th class="custom-header" style="text-align:center;">Status</th>
                            <th class="custom-header" style="text-align: center; width: 180px;">Aksi</th>
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
                                        <a href="file.php?path=<?= urlencode($row['sertifikasi']) ?>" target="_blank"
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
                                <td style="text-align:center;">
                                    <?php if ($row['is_verified'] === 't'): ?>
                                        <span class="badge badge-5">✔ Verified</span>
                                    <?php else: ?>
                                        <span class="badge badge-2">⏳ Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <!-- Tombol Detail (SEMUA USER) -->
                                    <a href="index.php?p=detail&id=<?= (int)$row['id'] ?>" class="action-btn btn-detail">
                                        📋 Detail
                                    </a>
                                    <?php if (isset($_SESSION['admin'])): ?>
                                        <!-- Tombol Verifikasi -->
                                        <?php if ($row['is_verified'] === 't'): ?>
                                            <a href="index.php?p=verifikasi&id=<?= (int)$row['id'] ?>&action=unverify"
                                                class="action-btn btn-unverify">
                                                ❌ Batal
                                            </a>
                                        <?php else: ?>
                                            <a href="index.php?p=verifikasi&id=<?= (int)$row['id'] ?>&action=verify"
                                                class="action-btn btn-verify">
                                                ✅ Verifikasi
                                            </a>
                                        <?php endif; ?>
                                        <!-- Tombol Hapus -->
                                        <button type="button"
                                            class="action-btn btn-delete"
                                            onclick="openDeleteModal(
                                            <?= $row['id'] ?>,
                                            '<?= htmlspecialchars(addslashes($row['judul_pelatihan'])) ?>',
                                            '<?= htmlspecialchars(addslashes($row['nama'])) ?>'
                                            )">🗑️ Hapus
                                        </button>
                                    <?php endif; ?>
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
                    <a href="index.php" class="btn" style="margin-top: 20px;">Lihat Semua Data</a>
                <?php else: ?>
                    <p>Mulai dengan mengisi form evaluasi pelatihan.</p>
                    <a href="index.php?p=form" class="btn" style="margin-top: 20px;">Isi Form Evaluasi</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div id="adminModal" class="modal">
        <div class="modal-content modern-modal">
            <div class="modal-header modern-header">
                <div class="icon-circle">👤</div>
                <h2>Admin Login</h2>
                <p>Masuk untuk mengelola data evaluasi pelatihan</p>
            </div>

            <div class="modal-body modern-body">
                <div class="input-group">
                    <label>Username</label>
                    <input type="text" id="adminUser" placeholder="Masukkan username" required>
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <input type="password" id="adminPass" placeholder="Masukkan password" required>
                </div>

                <p id="adminError" class="error-text"></p>
            </div>

            <div class="modal-footer modern-footer">
                <button onclick="closeAdminModal()" class="btn-outline">Batal</button>
                <button onclick="loginAdmin()" class="btn-solid">Login</button>
            </div>
        </div>
    </div>

    <div id="loginSuccessModal" class="modal">
        <div class="modal-content modern-modal">
            <div class="modal-header modern-header">
                <div class="icon-circle" style="background:#4CAF50;">✔</div>
                <h2>LOGIN BERHASIL</h2>
                <p>Selamat datang, Admin</p>
            </div>

            <div class="modal-footer modern-footer">
                <button onclick="closeLoginSuccess()" class="btn-solid">
                    OK
                </button>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="modal">
        <div class="modal-content modern-modal">
            <div class="modal-header modern-header">
                <div class="icon-circle" style="background:#f44336;">🗑️</div>
                <h2>Konfirmasi Hapus</h2>
                <p>Data yang dihapus tidak dapat dikembalikan</p>
            </div>

            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus evaluasi:</p>
                <p style="margin-top:10px;">
                    <strong id="deleteJudul"></strong><br>
                    <span id="deleteNama" style="color:#777;"></span>
                </p>
            </div>

            <div class="modal-footer modern-footer">
                <button onclick="closeDeleteModal()" class="btn-outline">Batal</button>
                <a id="deleteConfirmBtn" class="btn-solid" style="background:#f44336;">
                    Ya, Hapus
                </a>
            </div>
        </div>
    </div>

    <script>
        function openDeleteAllModal() {
            document.getElementById('deleteAllModal').style.display = 'block';
        }

        function closeDeleteAllModal() {
            document.getElementById('deleteAllModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            var modal = document.getElementById('deleteAllModal');
            if (event.target == modal) {
                closeDeleteAllModal();
            }
        }
    </script>

    <script>
        // Admin Login Modal
        function openAdminModal() {
            document.getElementById('adminModal').style.display = 'block';
        }

        function closeAdminModal() {
            document.getElementById('adminModal').style.display = 'none';
        }

        function loginAdmin() {
            const username = document.getElementById('adminUser').value;
            const password = document.getElementById('adminPass').value;

            fetch('index.php?p=admin_login&nocache=' + Date.now(), {
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        closeAdminModal();
                        document.getElementById('loginSuccessModal').style.display = 'block';

                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        document.getElementById('adminError').innerText = data.message;
                        document.getElementById('adminError').style.display = 'block';
                    }
                });
        }

        function closeLoginSuccess() {
            document.getElementById('loginSuccessModal').style.display = 'none';
            location.reload();
        }
    </script>
    <script>
        // enter key to submit login
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                const modal = document.getElementById('adminModal');
                if (modal.style.display === 'block') {
                    loginAdmin();
                }
            }
        });
    </script>
    <script>
        function openDeleteModal(id, judul, nama) {
            document.getElementById('deleteJudul').innerText = judul;
            document.getElementById('deleteNama').innerText = 'Oleh: ' + nama;
            document.getElementById('deleteConfirmBtn').href = 'index.php?p=delete&id=' + id;
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Klik di luar modal untuk menutup
        window.addEventListener('click', function(e) {
            const modal = document.getElementById('deleteModal');
            if (e.target === modal) {
                closeDeleteModal();
            }
        });
    </script>
</body>

</html>