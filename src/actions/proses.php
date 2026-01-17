<?php
date_default_timezone_set('Asia/Jakarta'); // Set timezone to Jakarta

require_once BASE_PATH . '/src/includes/session.php';
require_once BASE_PATH . '/src/includes/koneksi.php';

function uploadByRole($file, $baseDir, $prefix, $allowedExt, $maxSize)
{
    if (!isset($file) || $file['error'] !== 0) {
        return null;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt)) {
        die("Format file tidak valid");
    }

    if ($file['size'] > $maxSize) {
        die("Ukuran file terlalu besar");
    }

    $tanggal = date('Y-m-d');
    $targetDir = $baseDir . '/' . $tanggal . '/';

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $time = date('Hi');
    $counter = 1;

    do {
        $filename = "{$prefix}_{$time}_{$counter}.{$ext}";
        $targetPath = $targetDir . $filename;
        $counter++;
    } while (file_exists($targetPath));

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        die("Gagal upload file");
    }

    // simpan RELATIF dari storage/uploads/
    $targetPath = str_replace('\\', '/', $targetPath);
    $base = str_replace('\\', '/', BASE_PATH . '/storage/uploads/');

    return ltrim(str_replace($base, '', $targetPath), '/');
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Ambil data dari form
    $judul_pelatihan = pg_escape_string($conn, $_POST['judul_pelatihan']);

    // Data Peserta
    $nama = pg_escape_string($conn, $_POST['nama']);
    $nip = pg_escape_string($conn, $_POST['nip']);
    $jabatan = pg_escape_string($conn, $_POST['jabatan']);
    $unit_kerja = pg_escape_string($conn, $_POST['unit_kerja']);
    // ambil waktu gabungan (dari hidden "waktu" atau gabung dari 3 field)
    $waktuRaw = trim($_POST['waktu'] ?? '');
    if ($waktuRaw === '') {
        $tgl = trim($_POST['waktu_tgl'] ?? '');
        $bln = trim($_POST['waktu_bln'] ?? '');
        $thn = trim($_POST['waktu_thn'] ?? '');
        if ($tgl !== '' && $bln !== '' && $thn !== '') {
            $waktuRaw = $tgl . ' ' . $bln . ' ' . $thn; // contoh: 10 Januari 2025
        }
    }
    // baru di-escape untuk query
    $waktu = pg_escape_string($conn, $waktuRaw);
    $jam_pelajaran = (int)$_POST['jam_pelajaran'];
    $jenis_kompetensi = pg_escape_string($conn, $_POST['jenis_kompetensi']);
    $penyelenggara = pg_escape_string($conn, $_POST['penyelenggara']);

    // Upload Sertifikasi
    $sertifikasi = uploadByRole(
        $_FILES['sertifikasi'],
        BASE_PATH . '/storage/uploads/sertifikat',
        'sertifikat',
        ['jpg', 'jpeg', 'png', 'pdf'],
        5 * 1024 * 1024
    );

    // Pelaksanaan Pelatihan
    $tema_pelatihan = (int)$_POST['tema_pelatihan'];
    $ketepatan_waktu = (int)$_POST['ketepatan_waktu'];
    $suasana = (int)$_POST['suasana'];
    $kelengkapan_materi = (int)$_POST['kelengkapan_materi'];
    $servis_penyelenggara = (int)$_POST['servis_penyelenggara'];
    $alat_bantu_pelaksanaan = (int)$_POST['alat_bantu_pelaksanaan'];
    $nilai_keseluruhan_pelaksanaan = (int)$_POST['nilai_keseluruhan_pelaksanaan'];

    // Pembicara
    $penguasaan_masalah_pembicara = (int)$_POST['penguasaan_masalah_pembicara'];
    $cara_penyajian_pembicara = (int)$_POST['cara_penyajian_pembicara'];
    $manfaat_materi = (int)$_POST['manfaat_materi'];
    $interaksi_peserta_pembicara = (int)$_POST['interaksi_peserta_pembicara'];
    $alat_bantu_pembicara = (int)$_POST['alat_bantu_pembicara'];
    $nilai_keseluruhan_pembicara = (int)$_POST['nilai_keseluruhan_pembicara'];

    // Narasumber
    $penguasaan_masalah_narasumber = (int)$_POST['penguasaan_masalah_narasumber'];
    $cara_penyajian_narasumber = (int)$_POST['cara_penyajian_narasumber'];
    $manfaat_materi_narasumber = (int)$_POST['manfaat_materi_narasumber'];
    $interaksi_peserta_narasumber = (int)$_POST['interaksi_peserta_narasumber'];
    $alat_bantu_narasumber = (int)$_POST['alat_bantu_narasumber'];
    $nilai_komentar_saran = (int)$_POST['nilai_komentar_saran'];

    // Lain-lain
    $makanan = (int)$_POST['makanan'];
    $sound_system = (int)$_POST['sound_system'];
    $layanan_hotel = (int)$_POST['layanan_hotel'];

    // Text fields (Field tambahan)
    $rencana_tindakan = pg_escape_string($conn, $_POST['rencana_tindakan']);
    $komentar_saran = pg_escape_string($conn, $_POST['Komentar_saran']);
    $dampak_kompetensi = pg_escape_string($conn, $_POST['dampak_kompetensi']);

    // Tanda Tangan (3 orang)
    $tanggal_surat = pg_escape_string($conn, $_POST['tanggalSurat']);
    $nama_pegawai = pg_escape_string($conn, $_POST['pegawai']);
    $nip_pegawai = pg_escape_string($conn, $_POST['nipPegawai']);
    $nama_kepala = pg_escape_string($conn, $_POST['kepala']);
    $nip_kepala = pg_escape_string($conn, $_POST['nipKepala']);
    $nama_ketua = pg_escape_string($conn, $_POST['ketua']);
    $nip_ketua = pg_escape_string($conn, $_POST['nipKetua']);

    // ===== UPLOAD TTD =====
    $ttd_pegawai = uploadByRole(
        $_FILES['ttdPegawai'],
        BASE_PATH . '/storage/uploads/ttd/pegawai',
        'ttd_pegawai',
        ['jpg', 'jpeg', 'png'],
        2 * 1024 * 1024
    );

    $ttd_kepala = uploadByRole(
        $_FILES['ttdKepala'],
        BASE_PATH . '/storage/uploads/ttd/kepala',
        'ttd_kepala',
        ['jpg', 'jpeg', 'png'],
        2 * 1024 * 1024
    );

    $ttd_ketua = uploadByRole(
        $_FILES['ttdKetua'],
        BASE_PATH . '/storage/uploads/ttd/ketua',
        'ttd_ketua',
        ['jpg', 'jpeg', 'png'],
        2 * 1024 * 1024
    );

    // Query INSERT untuk PostgreSQL (pg_query)
    $query = "INSERT INTO evaluasi (
        judul_pelatihan, nama, nip, jabatan, unit_kerja, waktu, jam_pelajaran, jenis_kompetensi, penyelenggara, sertifikasi,
        tema_pelatihan, ketepatan_waktu, suasana, kelengkapan_materi,
        servis_penyelenggara, alat_bantu_pelaksanaan, nilai_keseluruhan_pelaksanaan,
        penguasaan_masalah_pembicara, cara_penyajian_pembicara, manfaat_materi,
        interaksi_peserta_pembicara, alat_bantu_pembicara, nilai_keseluruhan_pembicara,
        penguasaan_masalah_narasumber, cara_penyajian_narasumber, manfaat_materi_narasumber,
        interaksi_peserta_narasumber, alat_bantu_narasumber, nilai_komentar_saran,
        makanan, sound_system, layanan_hotel,
        rencana_tindakan, komentar_saran, dampak_kompetensi,
        tanggal_surat, nama_pegawai, nip_pegawai, nama_kepala, nip_kepala, nama_ketua, nip_ketua,
        ttd_pegawai, ttd_kepala, ttd_ketua
    ) VALUES (
        '$judul_pelatihan', '$nama', '$nip', '$jabatan', '$unit_kerja', '$waktu', $jam_pelajaran, '$jenis_kompetensi', '$penyelenggara', " . ($sertifikasi ? "'$sertifikasi'" : "NULL") . ",
        $tema_pelatihan, $ketepatan_waktu, $suasana, $kelengkapan_materi,
        $servis_penyelenggara, $alat_bantu_pelaksanaan, $nilai_keseluruhan_pelaksanaan,
        $penguasaan_masalah_pembicara, $cara_penyajian_pembicara, $manfaat_materi,
        $interaksi_peserta_pembicara, $alat_bantu_pembicara, $nilai_keseluruhan_pembicara,
        $penguasaan_masalah_narasumber, $cara_penyajian_narasumber, $manfaat_materi_narasumber,
        $interaksi_peserta_narasumber, $alat_bantu_narasumber, $nilai_komentar_saran,
        $makanan, $sound_system, $layanan_hotel,
        '$rencana_tindakan', '$komentar_saran', '$dampak_kompetensi',
        '$tanggal_surat', '$nama_pegawai', '$nip_pegawai', '$nama_kepala', '$nip_kepala', '$nama_ketua', '$nip_ketua',
        " . ($ttd_pegawai ? "'$ttd_pegawai'" : "NULL") . ",
        " . ($ttd_kepala ? "'$ttd_kepala'" : "NULL") . ",
        " . ($ttd_ketua ? "'$ttd_ketua'" : "NULL") . "
    )";

    $result = pg_query($conn, $query);

    if ($result) {
        echo "<!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Berhasil</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    margin: 0;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                }
                .success-box {
                    background: white;
                    padding: 40px;
                    border-radius: 10px;
                    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                    text-align: center;
                    max-width: 500px;
                }
                .success-icon {
                    font-size: 80px;
                    color: #4CAF50;
                    margin-bottom: 20px;
                }
                h1 {
                    color: #333;
                    margin-bottom: 10px;
                }
                p {
                    color: #666;
                    margin-bottom: 30px;
                    line-height: 1.6;
                }
                .btn {
                    display: inline-block;
                    padding: 12px 30px;
                    background-color: #4CAF50;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    transition: background-color 0.3s;
                    margin: 5px;
                }
                .btn:hover {
                    background-color: #45a049;
                }
                .btn-secondary {
                    background-color: #2196F3;
                }
                .btn-secondary:hover {
                    background-color: #0b7dda;
                }
            </style>
        </head>
        <body>
            <div class='success-box'>
                <div class='success-icon'>✓</div>
                <h1>Berhasil!</h1>
                <p>Data evaluasi pelatihan telah berhasil disimpan ke database.</p>
                <a href='index.php?p=form' class='btn'>Isi Form Lagi</a>
                <a href='index.php' class='btn btn-secondary'>Lihat Data</a>
            </div>
        </body>
        </html>";
    } else {
        echo "<!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Error</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    margin: 0;
                    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                }
                .error-box {
                    background: white;
                    padding: 40px;
                    border-radius: 10px;
                    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                    text-align: center;
                    max-width: 600px;
                }
                .error-icon {
                    font-size: 80px;
                    color: #f44336;
                    margin-bottom: 20px;
                }
                h1 {
                    color: #333;
                    margin-bottom: 10px;
                }
                p {
                    color: #666;
                    margin-bottom: 30px;
                    line-height: 1.6;
                }
                .error-detail {
                    background: #ffebee;
                    padding: 15px;
                    border-radius: 5px;
                    color: #c62828;
                    margin-bottom: 20px;
                    font-size: 14px;
                    max-height: 200px;
                    overflow-y: auto;
                    text-align: left;
                    word-wrap: break-word;
                }
                .btn {
                    display: inline-block;
                    padding: 12px 30px;
                    background-color: #f44336;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    transition: background-color 0.3s;
                }
                .btn:hover {
                    background-color: #da190b;
                }
            </style>
        </head>
        <body>
            <div class='error-box'>
                <div class='error-icon'>✗</div>
                <h1>Gagal!</h1>
                <p>Terjadi kesalahan saat menyimpan data.</p>
                <div class='error-detail'>" . pg_last_error($conn) . "</div>
                <a href='index.php?p=form' class='btn'>Kembali ke Form</a>
            </div>
        </body>
        </html>";
    }
}
