<?php
include 'koneksi.php';

// POST Data
$judul_pelatihan = $_POST['judul_pelatihan'];
$nama            = $_POST['nama'];
$waktu           = $_POST['waktu'];

// Upload Sertifikat
$sertifikasi = null;

if (!empty($_FILES['sertifikasi']['name'])) {
    $folder = "uploads/sertifikat/";
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $fileName   = time() . "_" . basename($_FILES['sertifikasi']['name']);
    $targetFile = $folder . $fileName;

    $fileSize = $_FILES['sertifikasi']['size'];
    $fileType = pathinfo($targetFile, PATHINFO_EXTENSION);

    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

    if (!in_array(strtolower($fileType), $allowed)) {
        die("Format file tidak diizinkan!");
    }

    if ($fileSize > 5 * 1024 * 1024) {
        die("Ukuran file maksimal 5MB!");
    }

    if (move_uploaded_file($_FILES['sertifikasi']['tmp_name'], $targetFile)) {
        $sertifikasi = $fileName;
    } else {
        die("Upload sertifikat gagal!");
    }
}

//SQL Query

$query = "
INSERT INTO evaluasi (
    judul_pelatihan, nama, waktu, sertifikasi,

    tema_pelatihan, ketepatan_waktu, suasana, kelengkapan_materi,
    servis_penyelenggara, alat_bantu_pelaksanaan, nilai_keseluruhan_pelaksanaan,

    penguasaan_masalah_pembicara, cara_penyajian_pembicara, manfaat_materi,
    interaksi_peserta_pembicara, alat_bantu_pembicara, nilai_keseluruhan_pembicara,

    penguasaan_masalah_narasumber, cara_penyajian_narasumber, manfaat_materi_narasumber,
    interaksi_peserta_narasumber, alat_bantu_narasumber, nilai_komentar_saran,

    makanan, sound_system, layanan_hotel,
    rencana_tindakan, komentar_tambahan
) VALUES (
    $1,$2,$3,$4,
    $5,$6,$7,$8,$9,$10,$11,
    $12,$13,$14,$15,$16,$17,
    $18,$19,$20,$21,$22,$23,
    $24,$25,$26,
    $27,$28
)
";

//Parameter

$params = [
    $judul_pelatihan,
    $nama,
    $waktu,
    $sertifikasi,

    $_POST['tema_pelatihan'],
    $_POST['ketepatan_waktu'],
    $_POST['suasana'],
    $_POST['kelengkapan_materi'],
    $_POST['servis_penyelenggara'],
    $_POST['alat_bantu_pelaksanaan'],
    $_POST['nilai_keseluruhan_pelaksanaan'],

    $_POST['penguasaan_masalah_pembicara'],
    $_POST['cara_penyajian_pembicara'],
    $_POST['manfaat_materi'],
    $_POST['interaksi_peserta_pembicara'],
    $_POST['alat_bantu_pembicara'],
    $_POST['nilai_keseluruhan_pembicara'],

    $_POST['penguasaan_masalah_narasumber'],
    $_POST['cara_penyajian_narasumber'],
    $_POST['manfaat_materi_narasumber'],
    $_POST['interaksi_peserta_narasumber'],
    $_POST['alat_bantu_narasumber'],
    $_POST['nilai_komentar_saran'],

    $_POST['makanan'],
    $_POST['sound_system'],
    $_POST['layanan_hotel'],

    $_POST['rencana_tindakan'],
    $_POST['komentar_tambahan']
];

//Eksekusi Query

$result = pg_query_params($conn, $query, $params);

if ($result) {
    echo "<script>
        alert('Evaluasi berhasil disimpan!');
        window.location='index.php';
    </script>";
} else {
    echo "Gagal menyimpan data: " . pg_last_error($conn);
}
?>
