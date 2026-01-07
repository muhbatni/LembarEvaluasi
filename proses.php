<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil data dari form
    $judul_pelatihan = pg_escape_string($conn, $_POST['judul_pelatihan']);
    $nama = pg_escape_string($conn, $_POST['nama']);
    $waktu = pg_escape_string($conn, $_POST['waktu']);
    
    // Handle upload file sertifikasi
    $sertifikasi = null;
    if (isset($_FILES['sertifikasi']) && $_FILES['sertifikasi']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        $filename = $_FILES['sertifikasi']['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $filesize = $_FILES['sertifikasi']['size'];
        
        // Validasi
        if (!in_array($filetype, $allowed)) {
            die("Error: Format file tidak didukung. Hanya JPG, PNG, dan PDF yang diperbolehkan.");
        }
        
        if ($filesize > 5 * 1024 * 1024) { // 5MB
            die("Error: Ukuran file terlalu besar. Maksimal 5MB.");
        }
        
        // Buat folder uploads jika belum ada
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Generate nama file unik
        $new_filename = uniqid() . '_' . time() . '.' . $filetype;
        $upload_path = $upload_dir . $new_filename;
        
        // Upload file
        if (move_uploaded_file($_FILES['sertifikasi']['tmp_name'], $upload_path)) {
            $sertifikasi = $new_filename;
        } else {
            die("Error: Gagal mengupload file.");
        }
    }
    
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
    
    // Text fields
    $rencana_tindakan = pg_escape_string($conn, $_POST['rencana_tindakan']);
    $komentar_tambahan = pg_escape_string($conn, $_POST['komentar_tambahan']);
    
    // Tanda Tangan (New fields)
    $tanggal_surat = pg_escape_string($conn, $_POST['tanggalSurat']);
    $nama_kepala = pg_escape_string($conn, $_POST['kepala']);
    $nip_kepala = pg_escape_string($conn, $_POST['nipKepala']);
    $nama_ketua = pg_escape_string($conn, $_POST['ketua']);
    $nip_ketua = pg_escape_string($conn, $_POST['nipKetua']);
    
    // Query INSERT untuk PostgreSQL
    $query = "INSERT INTO evaluasi (
        judul_pelatihan, nama, waktu, sertifikasi,
        tema_pelatihan, ketepatan_waktu, suasana, kelengkapan_materi,
        servis_penyelenggara, alat_bantu_pelaksanaan, nilai_keseluruhan_pelaksanaan,
        penguasaan_masalah_pembicara, cara_penyajian_pembicara, manfaat_materi,
        interaksi_peserta_pembicara, alat_bantu_pembicara, nilai_keseluruhan_pembicara,
        penguasaan_masalah_narasumber, cara_penyajian_narasumber, manfaat_materi_narasumber,
        interaksi_peserta_narasumber, alat_bantu_narasumber, nilai_komentar_saran,
        makanan, sound_system, layanan_hotel,
        rencana_tindakan, komentar_tambahan,
        tanggal_surat, nama_kepala, nip_kepala, nama_ketua, nip_ketua
    ) VALUES (
        '$judul_pelatihan', '$nama', '$waktu', " . ($sertifikasi ? "'$sertifikasi'" : "NULL") . ",
        $tema_pelatihan, $ketepatan_waktu, $suasana, $kelengkapan_materi,
        $servis_penyelenggara, $alat_bantu_pelaksanaan, $nilai_keseluruhan_pelaksanaan,
        $penguasaan_masalah_pembicara, $cara_penyajian_pembicara, $manfaat_materi,
        $interaksi_peserta_pembicara, $alat_bantu_pembicara, $nilai_keseluruhan_pembicara,
        $penguasaan_masalah_narasumber, $cara_penyajian_narasumber, $manfaat_materi_narasumber,
        $interaksi_peserta_narasumber, $alat_bantu_narasumber, $nilai_komentar_saran,
        $makanan, $sound_system, $layanan_hotel,
        '$rencana_tindakan', '$komentar_tambahan',
        '$tanggal_surat', '$nama_kepala', '$nip_kepala', '$nama_ketua', '$nip_ketua'
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
                <a href='form.php' class='btn'>Isi Form Lagi</a>
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
                    max-width: 500px;
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
                <a href='form.php' class='btn'>Kembali ke Form</a>
            </div>
        </body>
        </html>";
    }
    
    pg_close($conn);
}
?>