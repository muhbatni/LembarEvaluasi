<?php
include 'koneksi.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int) $_GET['id'];
$query = "SELECT * FROM evaluasi WHERE id = $id";
$result = pg_query($conn, $query);

if (pg_num_rows($result) == 0) {
    header('Location: index.php');
    exit;
}

$data = pg_fetch_assoc($result);

function getNilaiText($nilai)
{
    $labels = [
        1 => 'Buruk',
        2 => 'Kurang',
        3 => 'Cukup',
        4 => 'Bagus',
        5 => 'Memuaskan'
    ];
    return $labels[$nilai] ?? '-';
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Evaluasi - <?= htmlspecialchars($data['nama']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 15px;
        }

        .info-box {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .info-row {
            display: flex;
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: bold;
            width: 200px;
            color: #555;
        }

        .info-value {
            flex: 1;
            color: #333;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #4CAF50;
            margin-top: 30px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4CAF50;
        }

        .rating-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .rating-item {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #4CAF50;
        }

        .rating-label {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }

        .rating-value {
            font-size: 24px;
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 3px;
            font-size: 14px;
            font-weight: bold;
            margin-left: 10px;
        }

        .badge-1 {
            background-color: #f44336;
            color: white;
        }

        .badge-2 {
            background-color: #ff9800;
            color: white;
        }

        .badge-3 {
            background-color: #ffc107;
            color: black;
        }

        .badge-4 {
            background-color: #8bc34a;
            color: white;
        }

        .badge-5 {
            background-color: #4caf50;
            color: white;
        }

        .text-content {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #2196F3;
            margin: 10px 0;
            white-space: pre-wrap;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .btn-back {
            background-color: #2196F3;
            color: white;
        }

        .btn-back:hover {
            background-color: #0b7dda;
        }

        .btn-print {
            background-color: #4CAF50;
            color: white;
        }

        .btn-print:hover {
            background-color: #45a049;
        }

        .actions {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }

        .certificate-box {
            margin: 20px 0;
            padding: 20px;
            background: #e3f2fd;
            border-radius: 5px;
            text-align: center;
        }

        .certificate-img {
            max-width: 100%;
            height: auto;
            border: 2px solid #2196F3;
            border-radius: 5px;
            margin-top: 10px;
        }

        .signature-section {
            margin-top: 40px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
        }

        .signature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 20px;
        }

        .signature-box {
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .signature-space {
            height: 80px;
            margin: 20px 0;
        }

        .signature-name {
            font-weight: bold;
            font-size: 16px;
            border-top: 1px solid #333;
            padding-top: 10px;
            margin-top: 10px;
        }

        .signature-nip {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }

        @media print {

            .actions,
            .btn {
                display: none;
            }
        }

        /* RESPONSIVE DETAIL PAGE */

        /* Tablet */
        @media (max-width: 1024px) {
            .container {
                padding: 25px;
            }

            h1 {
                font-size: 22px;
            }

            .rating-grid {
                grid-template-columns: 1fr;
            }

            .info-label {
                width: 160px;
            }
        }

        /* Mobile */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 20px;
                border-radius: 6px;
            }

            h1 {
                font-size: 18px;
                padding-bottom: 10px;
            }

            .info-row {
                flex-direction: column;
                gap: 4px;
                margin-bottom: 12px;
            }

            .info-label {
                width: auto;
                font-size: 13px;
            }

            .info-value {
                font-size: 14px;
            }

            .section-title {
                font-size: 16px;
            }

            .rating-item {
                padding: 12px;
            }

            .rating-value {
                font-size: 20px;
            }

            .badge {
                display: inline-block;
                margin-top: 6px;
            }

            /* Sertifikat */
            .certificate-box {
                padding: 15px;
            }

            /* Tanda tangan jadi vertikal */
            .signature-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .signature-box {
                padding: 15px;
            }

            .actions {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
        }

        /* Small Mobile */
        @media (max-width: 480px) {
            h1 {
                font-size: 16px;
            }

            .rating-label {
                font-size: 13px;
            }

            .rating-value {
                font-size: 18px;
            }

            .badge {
                font-size: 12px;
                padding: 4px 10px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Detail Evaluasi Pelatihan</h1>

        <div class="info-box">
            <div class="info-row">
                <div class="info-label">Judul Pelatihan:</div>
                <div class="info-value"><?= htmlspecialchars($data['judul_pelatihan']) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Nama Peserta:</div>
                <div class="info-value"><?= htmlspecialchars($data['nama']) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Waktu:</div>
                <div class="info-value"><?= htmlspecialchars($data['waktu']) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal Input:</div>
                <div class="info-value"><?= date('d F Y, H:i', strtotime($data['created_at'])) ?> WIB</div>
            </div>
        </div>

        <?php if ($data['sertifikasi']): ?>
            <div class="certificate-box">
                <h3>📄 Sertifikat</h3>
                <?php
                $ext = strtolower(pathinfo($data['sertifikasi'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png'])):
                    ?>
                    <img src="uploads/<?= htmlspecialchars($data['sertifikasi']) ?>" alt="Sertifikasi" class="certificate-img">
                <?php else: ?>
                    <p>
                        <a href="uploads/<?= htmlspecialchars($data['sertifikasi']) ?>" target="_blank" class="btn btn-back">📄
                            Lihat PDF</a>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="section-title">Pelaksanaan Pelatihan</div>
        <div class="rating-grid">
            <div class="rating-item">
                <div class="rating-label">Tema Pelatihan</div>
                <div class="rating-value">
                    <?= $data['tema_pelatihan'] ?>
                    <span class="badge badge-<?= $data['tema_pelatihan'] ?>">
                        <?= getNilaiText($data['tema_pelatihan']) ?>
                    </span>
                </div>
            </div>
            <div class="rating-item">
                <div class="rating-label">Ketepatan Waktu</div>
                <div class="rating-value">
                    <?= $data['ketepatan_waktu'] ?>
                    <span class="badge badge-<?= $data['ketepatan_waktu'] ?>">
                        <?= getNilaiText($data['ketepatan_waktu']) ?>
                    </span>
                </div>
            </div>
            <div class="rating-item">
                <div class="rating-label">Suasana</div>
                <div class="rating-value">
                    <?= $data['suasana'] ?>
                    <span class="badge badge-<?= $data['suasana'] ?>">
                        <?= getNilaiText($data['suasana']) ?>
                    </span>
                </div>
            </div>
            <div class="rating-item">
                <div class="rating-label">Kelengkapan Materi</div>
                <div class="rating-value">
                    <?= $data['kelengkapan_materi'] ?>
                    <span class="badge badge-<?= $data['kelengkapan_materi'] ?>">
                        <?= getNilaiText($data['kelengkapan_materi']) ?>
                    </span>
                </div>
            </div>
            <div class="rating-item">
                <div class="rating-label">Servis Penyelenggara</div>
                <div class="rating-value">
                    <?= $data['servis_penyelenggara'] ?>
                    <span class="badge badge-<?= $data['servis_penyelenggara'] ?>">
                        <?= getNilaiText($data['servis_penyelenggara']) ?>
                    </span>
                </div>
            </div>
            <div class="rating-item">
                <div class="rating-label">Alat Bantu</div>
                <div class="rating-value">
                    <?= $data['alat_bantu_pelaksanaan'] ?>
                    <span class="badge badge-<?= $data['alat_bantu_pelaksanaan'] ?>">
                        <?= getNilaiText($data['alat_bantu_pelaksanaan']) ?>
                    </span>
                </div>
            </div>
            <div class="rating-item">
                <div class="rating-label">Nilai Keseluruhan</div>
                <div class="rating-value">
                    <?= $data['nilai_keseluruhan_pelaksanaan'] ?>
                    <span class="badge badge-<?= $data['nilai_keseluruhan_pelaksanaan'] ?>">
                        <?= getNilaiText($data['nilai_keseluruhan_pelaksanaan']) ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="section-title">Pembicara</div>
        <div class="rating-grid">
            <div class="rating-item">
                <div class="rating-label">Penguasaan Masalah</div>
                <div class="rating-value">
                    <?= $data['penguasaan_masalah_pembicara'] ?>
                    <span class="badge badge-<?= $data['penguasaan_masalah_pembicara'] ?>">
                        <?= getNilaiText($data['penguasaan_masalah_pembicara']) ?>
                    </span>
                </div>
            </div>
            <div class="rating-item">
                <div class="rating-label">Cara Penyajian</div>
                <div class="rating-value">
                    <?= $data['cara_penyajian_pembicara'] ?>
                    <span class="badge badge-<?= $data['cara_penyajian_pembicara'] ?>">
                        <?= getNilaiText($data['cara_penyajian_pembicara']) ?>
                    </span>
                </div>
            </div>
            <div class="rating-item">
                <div class="rating-label">Manfaat Materi</div>
                <div class="rating-value">
                    <?= $data['manfaat_materi'] ?>
                    <span class="badge badge-<?= $data['manfaat_materi'] ?>">
                        <?= getNilaiText($data['manfaat_materi']) ?>
                    </span>
                </div>
            </div>
            <div class="rating-item">
                <div class="rating-label">Interaksi dengan Peserta</div>
                <div class="rating-value">
                    <?= $data['interaksi_peserta_pembicara'] ?>
                    <span class="badge badge-<?= $data['interaksi_peserta_pembicara'] ?>">
                        <?= getNilaiText($data['interaksi_peserta_pembicara']) ?>
                    </span>
                </div>
            </div>
            <div class="rating-item">
                <div class="rating-label">Alat Bantu</div>
                <div class="rating-value">
                    <?= $data['alat_bantu_pembicara'] ?>
                    <span class="badge badge-<?= $data['alat_bantu_pembicara'] ?>">
                        <?= getNilaiText($data['alat_bantu_pembicara']) ?>
                    </span>
                </div>
            </div>
            <div class="rating-item">
                <div class="rating-label">Nilai Keseluruhan</div>
                <div class="rating-value">
                    <?= $data['nilai_keseluruhan_pembicara'] ?>
                    <span class="badge badge-<?= $data['nilai_keseluruhan_pembicara'] ?>">
                        <?= getNilaiText($data['nilai_keseluruhan_pembicara']) ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="section-title">Narasumber</div>
        <div class="rating-grid">
            <div class="rating-item">
                <div class="rating-label">Penguasaan Masalah</div>
                <div class="rating-value">
                    <?= $data['penguasaan_masalah_narasumber'] ?>
                    <span class="badge badge-<?= $data['penguasaan_masalah_narasumber'] ?>">
                        <?= getNilaiText($data['penguasaan_masalah_narasumber']) ?>
                    </span>
                </div>
            </div>
            <div class="rating-item">
                <div class="rating-label">Cara Penyajian</div>
                <div class="rating-value">
                    <?= $data['cara_penyajian_narasumber'] ?>
                    <span class="badge badge-<?= $data['cara_penyajian_narasumber'] ?>">
                        <?= getNilaiText($data['cara_penyajian_narasumber']) ?>
                    </span>
                </div>
            </div>
            <div class="rating-item">
                <div class="rating-label">Manfaat Materi</div>
                <div class="rating-value">
                    <?= $data['manfaat_materi_narasumber'] ?>
                    <span class="badge badge-<?= $data['manfaat_materi_narasumber'] ?>">
                        <?= getNilaiText($data['manfaat_materi_narasumber']) ?>
                    </span>
                </div>
            </div>
            <div class="rating-item">
                <div class="rating-label">Interaksi dengan Peserta</div>
                <div class="rating-value">
                    <?= $data['interaksi_peserta_narasumber'] ?>
                    <span class="badge badge-<?= $data['interaksi_peserta_narasumber'] ?>">
                        <?= getNilaiText($data['interaksi_peserta_narasumber']) ?>
                    </span>
                </div>
            </div>
            <div class="rating-item">
                <div class="rating-label">Alat Bantu</div>
                <div class="rating-value">
                    <?= $data['alat_bantu_narasumber'] ?>
                    <span class="badge badge-<?= $data['alat_bantu_narasumber'] ?>">
                        <?= getNilaiText($data['alat_bantu_narasumber']) ?>
                    </span>
                </div>
            </div>
            <div class="rating-item">
                <div class="rating-label">Komentar & Saran</div>
                <div class="rating-value">
                    <?= $data['nilai_komentar_saran'] ?>
                    <span class="badge badge-<?= $data['nilai_komentar_saran'] ?>">
                        <?= getNilaiText($data['nilai_komentar_saran']) ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="section-title">Lain-lain</div>
        <div class="rating-grid">
            <div class="rating-item">
                <div class="rating-label">Makanan</div>
                <div class="rating-value">
                    <?= $data['makanan'] ?>
                    <span class="badge badge-<?= $data['makanan'] ?>">
                        <?= getNilaiText($data['makanan']) ?>
                    </span>
                </div>
            </div>
            <div class="rating-item">
                <div class="rating-label">Sound System</div>
                <div class="rating-value">
                    <?= $data['sound_system'] ?>
                    <span class="badge badge-<?= $data['sound_system'] ?>">
                        <?= getNilaiText($data['sound_system']) ?>
                    </span>
                </div>
            </div>
            <div class="rating-item">
                <div class="rating-label">Layanan Hotel</div>
                <div class="rating-value">
                    <?= $data['layanan_hotel'] ?>
                    <span class="badge badge-<?= $data['layanan_hotel'] ?>">
                        <?= getNilaiText($data['layanan_hotel']) ?>
                    </span>
                </div>
            </div>
        </div>

        <?php if ($data['rencana_tindakan']): ?>
            <div class="section-title">Rencana Tindakan Penerapan</div>
            <div class="text-content"><?= htmlspecialchars($data['rencana_tindakan']) ?></div>
        <?php endif; ?>

        <?php if ($data['komentar_tambahan']): ?>
            <div class="section-title">Komentar Tambahan</div>
            <div class="text-content"><?= htmlspecialchars($data['komentar_tambahan']) ?></div>
        <?php endif; ?>

        <!-- SECTION TANDA TANGAN (DITAMBAHKAN) -->
        <div class="section-title">Tanda Tangan</div>
        <div class="signature-section">
            <div style="text-align: right; margin-bottom: 20px;">
                <strong>Sidoarjo, <?= htmlspecialchars($data['tanggal_surat']) ?></strong>
            </div>

            <div class="signature-grid">
                <div class="signature-box">
                    <div class="signature-title">Mengetahui,</div>
                    <div style="font-weight: bold; margin: 10px 0;">
                        KEPALA SUB BAGIAN<br>
                        TATA USAHA
                    </div>
                    <div class="signature-space"></div>
                    <div class="signature-name"><?= htmlspecialchars($data['nama_kepala']) ?></div>
                    <div class="signature-nip">NIP. <?= htmlspecialchars($data['nip_kepala']) ?></div>
                </div>

                <div class="signature-box">
                    <div class="signature-title">Menyetujui,</div>
                    <div style="font-weight: bold; margin: 10px 0;">
                        KETUA TEAM
                    </div>
                    <div class="signature-space"></div>
                    <div class="signature-name"><?= htmlspecialchars($data['nama_ketua']) ?></div>
                    <div class="signature-nip">NIP. <?= htmlspecialchars($data['nip_ketua']) ?></div>
                </div>
            </div>
        </div>

        <div class="actions">
            <a href="index.php" class="btn btn-back">← Kembali ke Daftar</a>
            <a href="javascript:window.print()" class="btn btn-print">🖨️ Cetak</a>
        </div>
    </div>
</body>

</html>