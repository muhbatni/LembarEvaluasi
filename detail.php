<?php
include 'koneksi.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];
$query = "SELECT * FROM evaluasi WHERE id = $id";
$result = pg_query($conn, $query);

if (pg_num_rows($result) == 0) {
    header('Location: index.php');
    exit;
}

$data = pg_fetch_assoc($result);

function renderText($text)
{
    if (trim($text) === '') {
        return '<div class="text-empty">(Belum diisi)</div>';
    }
    return nl2br(htmlspecialchars($text));
}

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
            font-size: 20px;
            text-transform: uppercase;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .info-item {
            background: #f9f9f9;
            padding: 12px;
            border-radius: 5px;
        }

        .info-label {
            font-weight: bold;
            font-size: 13px;
            color: #555;
            margin-bottom: 5px;
        }

        .info-value {
            color: #333;
            font-size: 14px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #4CAF50;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #4CAF50;
            text-transform: uppercase;
        }

        .rating-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
        }

        .rating-item {
            background: #fafafa;
            padding: 10px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .rating-label {
            font-size: 13px;
            color: #555;
            flex: 1;
        }

        .rating-value {
            font-size: 16px;
            font-weight: bold;
            margin-right: 8px;
        }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
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

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            font-size: 14px;
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
            margin: 15px 0;
            padding: 15px;
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
            margin-top: 30px;
        }

        .signature-date {
            text-align: right;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .signature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        .signature-box {
            text-align: center;
            padding: 15px;
            background: #fafafa;
            border-radius: 5px;

            display: flex;
            flex-direction: column;
            height: 260px;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 13px;
        }

        .signature-space {
            flex-grow: 1;
        }

        .signature-name {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #333;
            padding-top: 8px;
            margin-top: 0;
        }

        .signature-nip {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        .two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        /* WATERMARK VERIFIED */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 90px;
            font-weight: 900;
            color: rgba(76, 175, 80, 0.15);
            letter-spacing: 10px;
            z-index: 999;
            pointer-events: none;
            user-select: none;
        }

        @media print {

            .actions,
            .btn {
                display: none;
            }

            body {
                background: white;
                padding: 0;
            }

            .container {
                box-shadow: none;
                padding: 20px;
            }

            .watermark {
                color: rgba(76, 175, 80, 0.25);
                font-size: 110px;
            }

            .page-break {
                page-break-before: always;
            }
        }

        @media (max-width: 768px) {

            .info-grid,
            .rating-grid,
            .two-columns,
            .signature-grid {
                grid-template-columns: 1fr;
            }

            .rating-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
        }

        /* css print */
        @page {
            size: A4 portrait;
            margin: 20mm;
        }

        @media print {

            .actions,
            .btn {
                display: none !important;
            }

            body {
                background: white;
                padding: 0;
            }

            .container {
                box-shadow: none;
                padding: 15px;
            }

            /* === PAGE CONTROL === */
            .page-1 {
                page-break-after: always;
            }

            /* === SERTIFIKAT DIKECILKAN PAKSA === */
            .certificate-img {
                max-height: 180px;
                object-fit: contain;
            }

            .certificate-box {
                padding: 8px;
                margin: 8px 0;
            }

            /* === GRID TETAP 2 KOLOM === */
            .info-grid,
            .two-columns {
                grid-template-columns: 1fr 1fr !important;
                gap: 12px;
            }

            .rating-grid {
                grid-template-columns: 1fr !important;
            }

            /* === TANDA TANGAN ANTI PECAH === */
            .signature-section,
            .signature-grid,
            .signature-box {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .signature-box {
                height: 210px;
                /* DIPERKECIL */
                padding: 10px;
            }

            .signature-grid {
                gap: 12px;
            }

            .signature-name {
                font-size: 13px;
            }

            .signature-nip {
                font-size: 11px;
            }

            /* === TEKS DIPADATKAN === */
            .text-content {
                text-align: left !important;

                background: repeating-linear-gradient(white,
                        white 22px,
                        #eee 23px);

                padding: 10px 12px;
                border: 1px solid #ddd;
                border-radius: 4px;

                font-size: 13px;
                line-height: 1.5;
                color: #333;

                min-height: 60px;
            }
        }

        .text-empty {
            display: block;
            text-align: left;
            color: #999;
            font-style: italic;
        }
    </style>
</head>

<body>
    <?php if ($data['is_verified'] === 't'): ?>
        <div class="watermark">VERIFIED</div>
    <?php endif; ?>

    <div class="container">
        <div class="page-1">
            <h1>Lembar Evaluasi Pelatihan</h1>

            <!-- INFORMASI PELATIHAN -->
            <div style="background: #f0f7ff; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
                <div style="font-weight: bold; margin-bottom: 10px;">Judul Pelatihan / Workshop:</div>
                <div style="font-size: 15px;"><?= htmlspecialchars($data['judul_pelatihan']) ?></div>
            </div>

            <!-- DATA PESERTA -->
            <div style="font-weight: bold; font-size: 15px; margin: 15px 0 10px 0;">📋 Data Peserta</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Nama:</div>
                    <div class="info-value"><?= htmlspecialchars($data['nama']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">NIP:</div>
                    <div class="info-value"><?= htmlspecialchars($data['nip']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Jabatan:</div>
                    <div class="info-value"><?= htmlspecialchars($data['jabatan']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Unit Kerja:</div>
                    <div class="info-value"><?= htmlspecialchars($data['unit_kerja']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Waktu / Tanggal Pelaksanaan:</div>
                    <div class="info-value"><?= htmlspecialchars($data['waktu']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Jam Pelajaran:</div>
                    <div class="info-value"><?= htmlspecialchars($data['jam_pelajaran']) ?> JP</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Jenis Pengembangan Kompetensi:</div>
                    <div class="info-value"><?= htmlspecialchars($data['jenis_kompetensi']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Penyelenggara:</div>
                    <div class="info-value"><?= htmlspecialchars($data['penyelenggara']) ?></div>
                </div>
            </div>

            <!-- Certificate -->
            <?php if ($data['sertifikasi']): ?>
                <div class="certificate-box">
                    <strong>📄 Sertifikat</strong>
                    <?php
                    $ext = strtolower(pathinfo($data['sertifikasi'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png'])):
                    ?>
                        <br><img src="uploads/<?= htmlspecialchars($data['sertifikasi']) ?>" alt="Sertifikasi"
                            class="certificate-img">
                    <?php else: ?>
                        <br><a href="uploads/<?= htmlspecialchars($data['sertifikasi']) ?>" target="_blank"
                            class="btn btn-back" style="margin-top:10px;">📄 Lihat PDF</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div style="margin: 15px 0; padding: 12px; background: #fff9e6; border-left: 4px solid #ffc107; font-style: italic; font-size: 13px; color: #555;">
                <strong>Keterangan Nilai:</strong> 1 = Buruk | 2 = Kurang | 3 = Cukup | 4 = Bagus | 5 = Memuaskan
            </div>

        </div>
        <div class="page-break"></div>
        <div class="    page-2">

            <!-- PELAKSANAAN PELATIHAN & PEMBICARA -->
            <div class="two-columns">
                <div>
                    <div class="section-title">Pelaksanaan Pelatihan</div>
                    <div class="rating-grid" style="grid-template-columns: 1fr;">
                        <div class="rating-item">
                            <span class="rating-label">Tema Pelatihan</span>
                            <span>
                                <span class="rating-value"><?= $data['tema_pelatihan'] ?></span>
                                <span class="badge badge-<?= $data['tema_pelatihan'] ?>">
                                    <?= getNilaiText($data['tema_pelatihan']) ?>
                                </span>
                            </span>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Ketepatan Waktu</span>
                            <span>
                                <span class="rating-value"><?= $data['ketepatan_waktu'] ?></span>
                                <span class="badge badge-<?= $data['ketepatan_waktu'] ?>">
                                    <?= getNilaiText($data['ketepatan_waktu']) ?>
                                </span>
                            </span>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Suasana</span>
                            <span>
                                <span class="rating-value"><?= $data['suasana'] ?></span>
                                <span class="badge badge-<?= $data['suasana'] ?>">
                                    <?= getNilaiText($data['suasana']) ?>
                                </span>
                            </span>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Kelengkapan Materi</span>
                            <span>
                                <span class="rating-value"><?= $data['kelengkapan_materi'] ?></span>
                                <span class="badge badge-<?= $data['kelengkapan_materi'] ?>">
                                    <?= getNilaiText($data['kelengkapan_materi']) ?>
                                </span>
                            </span>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Servis Penyelenggara</span>
                            <span>
                                <span class="rating-value"><?= $data['servis_penyelenggara'] ?></span>
                                <span class="badge badge-<?= $data['servis_penyelenggara'] ?>">
                                    <?= getNilaiText($data['servis_penyelenggara']) ?>
                                </span>
                            </span>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Alat Bantu</span>
                            <span>
                                <span class="rating-value"><?= $data['alat_bantu_pelaksanaan'] ?></span>
                                <span class="badge badge-<?= $data['alat_bantu_pelaksanaan'] ?>">
                                    <?= getNilaiText($data['alat_bantu_pelaksanaan']) ?>
                                </span>
                            </span>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Nilai Keseluruhan</span>
                            <span>
                                <span class="rating-value"><?= $data['nilai_keseluruhan_pelaksanaan'] ?></span>
                                <span class="badge badge-<?= $data['nilai_keseluruhan_pelaksanaan'] ?>">
                                    <?= getNilaiText($data['nilai_keseluruhan_pelaksanaan']) ?>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="section-title">Pembicara</div>
                    <div class="rating-grid" style="grid-template-columns: 1fr;">
                        <div class="rating-item">
                            <span class="rating-label">Penguasaan Masalah</span>
                            <span>
                                <span class="rating-value"><?= $data['penguasaan_masalah_pembicara'] ?></span>
                                <span class="badge badge-<?= $data['penguasaan_masalah_pembicara'] ?>">
                                    <?= getNilaiText($data['penguasaan_masalah_pembicara']) ?>
                                </span>
                            </span>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Cara Penyajian</span>
                            <span>
                                <span class="rating-value"><?= $data['cara_penyajian_pembicara'] ?></span>
                                <span class="badge badge-<?= $data['cara_penyajian_pembicara'] ?>">
                                    <?= getNilaiText($data['cara_penyajian_pembicara']) ?>
                                </span>
                            </span>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Manfaat Materi</span>
                            <span>
                                <span class="rating-value"><?= $data['manfaat_materi'] ?></span>
                                <span class="badge badge-<?= $data['manfaat_materi'] ?>">
                                    <?= getNilaiText($data['manfaat_materi']) ?>
                                </span>
                            </span>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Interaksi dengan Peserta</span>
                            <span>
                                <span class="rating-value"><?= $data['interaksi_peserta_pembicara'] ?></span>
                                <span class="badge badge-<?= $data['interaksi_peserta_pembicara'] ?>">
                                    <?= getNilaiText($data['interaksi_peserta_pembicara']) ?>
                                </span>
                            </span>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Alat Bantu</span>
                            <span>
                                <span class="rating-value"><?= $data['alat_bantu_pembicara'] ?></span>
                                <span class="badge badge-<?= $data['alat_bantu_pembicara'] ?>">
                                    <?= getNilaiText($data['alat_bantu_pembicara']) ?>
                                </span>
                            </span>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Nilai Keseluruhan</span>
                            <span>
                                <span class="rating-value"><?= $data['nilai_keseluruhan_pembicara'] ?></span>
                                <span class="badge badge-<?= $data['nilai_keseluruhan_pembicara'] ?>">
                                    <?= getNilaiText($data['nilai_keseluruhan_pembicara']) ?>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- NARASUMBER & LAIN-LAIN -->
            <div class="two-columns">
                <div>
                    <div class="section-title">Narasumber</div>
                    <div class="rating-grid" style="grid-template-columns: 1fr;">
                        <div class="rating-item">
                            <span class="rating-label">Penguasaan Masalah</span>
                            <span>
                                <span class="rating-value"><?= $data['penguasaan_masalah_narasumber'] ?></span>
                                <span class="badge badge-<?= $data['penguasaan_masalah_narasumber'] ?>">
                                    <?= getNilaiText($data['penguasaan_masalah_narasumber']) ?>
                                </span>
                            </span>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Cara Penyajian</span>
                            <span>
                                <span class="rating-value"><?= $data['cara_penyajian_narasumber'] ?></span>
                                <span class="badge badge-<?= $data['cara_penyajian_narasumber'] ?>">
                                    <?= getNilaiText($data['cara_penyajian_narasumber']) ?>
                                </span>
                            </span>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Manfaat Materi</span>
                            <span>
                                <span class="rating-value"><?= $data['manfaat_materi_narasumber'] ?></span>
                                <span class="badge badge-<?= $data['manfaat_materi_narasumber'] ?>">
                                    <?= getNilaiText($data['manfaat_materi_narasumber']) ?>
                                </span>
                            </span>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Interaksi dengan Peserta</span>
                            <span>
                                <span class="rating-value"><?= $data['interaksi_peserta_narasumber'] ?></span>
                                <span class="badge badge-<?= $data['interaksi_peserta_narasumber'] ?>">
                                    <?= getNilaiText($data['interaksi_peserta_narasumber']) ?>
                                </span>
                            </span>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Alat Bantu</span>
                            <span>
                                <span class="rating-value"><?= $data['alat_bantu_narasumber'] ?></span>
                                <span class="badge badge-<?= $data['alat_bantu_narasumber'] ?>">
                                    <?= getNilaiText($data['alat_bantu_narasumber']) ?>
                                </span>
                            </span>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Komentar & Saran</span>
                            <span>
                                <span class="rating-value"><?= $data['nilai_komentar_saran'] ?></span>
                                <span class="badge badge-<?= $data['nilai_komentar_saran'] ?>">
                                    <?= getNilaiText($data['nilai_komentar_saran']) ?>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="section-title">Lain-lain</div>
                    <div class="rating-grid" style="grid-template-columns: 1fr;">
                        <div class="rating-item">
                            <span class="rating-label">Makanan</span>
                            <span>
                                <span class="rating-value"><?= $data['makanan'] ?></span>
                                <span class="badge badge-<?= $data['makanan'] ?>">
                                    <?= getNilaiText($data['makanan']) ?>
                                </span>
                            </span>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Sound System</span>
                            <span>
                                <span class="rating-value"><?= $data['sound_system'] ?></span>
                                <span class="badge badge-<?= $data['sound_system'] ?>">
                                    <?= getNilaiText($data['sound_system']) ?>
                                </span>
                            </span>
                        </div>
                        <div class="rating-item">
                            <span class="rating-label">Layanan Hotel</span>
                            <span>
                                <span class="rating-value"><?= $data['layanan_hotel'] ?></span>
                                <span class="badge badge-<?= $data['layanan_hotel'] ?>">
                                    <?= getNilaiText($data['layanan_hotel']) ?>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- rencana tindakan penerapan -->
            <div class="two-columns" style="margin-top: 15px;">
                <div>
                    <div class="section-title">Rencana Tindakan Penerapan</div>
                    <div class="text-content"><?= renderText($data['rencana_tindakan'] ?? '') ?></div>
                </div>
            </div>

            <!-- Komentar dan Saran, Dampak Pengembangan Kompetensi -->
            <div class="two-columns" style="font-weight: bold; font-size: 14px; margin-bottom: 8px;">

                <div>
                    <div class="section-title">Komentar dan Saran</div>
                    <div class="text-content">
                        <?= renderText($data['komentar_saran'] ?? '') ?>
                    </div>
                </div>

                <div>
                    <div class="section-title">Dampak Pengembangan Kompetensi</div>
                    <div class="text-content">
                        <?= renderText($data['dampak_kompetensi'] ?? '') ?>
                    </div>
                </div>
            </div>

            <!-- TANDA TANGAN -->
            <div class="signature-section">
                <div class="signature-date">
                    Sidoarjo, <?= htmlspecialchars($data['tanggal_surat']) ?>
                </div>

                <div class="signature-grid">
                    <div class="signature-box">
                        <div class="signature-title">Mengetahui,</div>
                        <div style="font-weight: bold; font-size: 12px; margin: 8px 0;">
                            PEGAWAI YANG MELAKSANAKAN<br>
                            PENGEMBANGAN KOMPETENSI
                        </div>
                        <div class="signature-space"></div>
                        <div class="signature-name"><?= htmlspecialchars($data['nama_pegawai']) ?></div>
                        <div class="signature-nip">NIP. <?= htmlspecialchars($data['nip_pegawai']) ?></div>
                    </div>

                    <div class="signature-box">
                        <div class="signature-title">Mengetahui,</div>
                        <div style="font-weight: bold; font-size: 12px; margin: 8px 0;">
                            KEPALA SUB BAGIAN<br>
                            TATA USAHA
                        </div>
                        <div class="signature-space"></div>
                        <div class="signature-name"><?= htmlspecialchars($data['nama_kepala']) ?></div>
                        <div class="signature-nip">NIP. <?= htmlspecialchars($data['nip_kepala']) ?></div>
                    </div>

                    <div class="signature-box">
                        <div class="signature-title">Menyetujui,</div>
                        <div style="font-weight: bold; font-size: 12px; margin: 8px 0;">
                            KETUA TEAM
                        </div>
                        <div class="signature-space"></div>
                        <div class="signature-name"><?= htmlspecialchars($data['nama_ketua']) ?></div>
                        <div class="signature-nip">NIP. <?= htmlspecialchars($data['nip_ketua']) ?></div>
                    </div>
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