<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Evaluasi Pelatihan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 20px;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .info-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .info-box .left {
            width: 48%;
        }

        .info-box .right {
            width: 48%;
        }

        table.legend {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        table.legend td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .note {
            background-color: #f9f9f9;
            padding: 15px;
            border-left: 4px solid #4CAF50;
            margin: 20px 0;
            font-style: italic;
            color: #555;
        }

        .divider {
            border-top: 2px solid #333;
            margin: 30px 0;
        }

        .section-title {
            font-weight: bold;
            font-size: 16px;
            margin: 20px 0 15px 0;
            text-transform: uppercase;
        }

        .rating-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #fafafa;
            border-radius: 4px;
        }

        .rating-row label {
            flex: 1;
            margin: 0;
        }

        .rating-options {
            display: flex;
            gap: 15px;
        }

        .rating-options input[type="radio"] {
            margin-right: 5px;
        }

        .two-columns {
            display: flex;
            justify-content: space-between;
            gap: 30px;
        }

        .column {
            flex: 1;
        }

        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            resize: vertical;
            min-height: 100px;
        }

        .signature-section {
            margin-top: 40px;
            text-align: center;
        }

        .signature-box {
            display: inline-block;
            margin: 20px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
            min-width: 200px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }

        button:hover {
            background-color: #45a049;
        }

        .submit-section {
            text-align: center;
            margin-top: 30px;
        }

        /* Style untuk upload file */
        .upload-box {
            border: 2px dashed #4CAF50;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background-color: #f9fff9;
            margin-top: 10px;
            transition: all 0.3s ease;
        }

        .upload-box:hover {
            border-color: #45a049;
            background-color: #f0fff0;
        }

        .upload-box input[type="file"] {
            display: none;
        }

        .upload-label {
            cursor: pointer;
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .upload-label:hover {
            background-color: #45a049;
        }

        .file-info {
            margin-top: 10px;
            color: #666;
            font-size: 14px;
        }

        .file-name {
            margin-top: 10px;
            padding: 8px;
            background-color: #e8f5e9;
            border-radius: 4px;
            color: #2e7d32;
            font-weight: bold;
        }

        .upload-icon {
            font-size: 48px;
            color: #4CAF50;
            margin-bottom: 10px;
        }

        .preview-image {
            max-width: 300px;
            max-height: 300px;
            margin-top: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
        }

        .text-like {
            border: none;
            background: transparent;
            outline: none;

            font-family: inherit;
            font-size: inherit;
            font-weight: inherit;
            line-height: inherit;

            padding: 0;
            margin: 0;
            text-align: center;
        }

        .line-input {
            border: none;
            border-bottom: 1px solid #333;
            background: transparent;
            outline: none;

            font-family: inherit;
            font-size: inherit;
            text-align: center;
        }

        .ttd-wrapper {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .ttd-box {
            width: 45%;
            text-align: center;
        }

        .ttd-space {
            height: 70px;
        }

        .line-input {
            width: 100%;
            border: none;
            border-bottom: 1px solid #000;
            text-align: center;
            padding: 5px 0;
        }

        .line-input:focus {
            outline: none;
        }

        .nip-row {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
        }

        .nip-row input {
            border: none;
            border-bottom: 1px dotted #000;
            margin-left: 5px;
            width: 150px;
            text-align: center;
        }

        .nip-row input:focus {
            outline: none;
        }

        .ttd-title {
            min-height: 50px;
        }

        .essay {
            font-family: inherit;
            font-size: inherit;
            font-weight: inherit;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Lembar Evaluasi Pelatihan</h1>
        </div>

        <form action="proses.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Judul Pelatihan / Workshop:</label>
                <input type="text" name="judul_pelatihan" required class="essay">
            </div>

            <table class="legend">
                <tr>
                    <td><strong>Nilai</strong></td>
                    <td><strong>Keterangan</strong></td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Buruk</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Kurang</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Cukup</td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>Bagus</td>
                </tr>
                <tr>
                    <td>5</td>
                    <td>Memuaskan</td>
                </tr>
            </table>

            <div class="info-box">
                <div class="left">
                    <div class="form-group">
                        <label>Nama:</label>
                        <input type="text" name="nama" required class="essay">
                    </div>
                </div>
                <div class="right">
                    <div class="form-group">
                        <label>Waktu:</label>
                        <input type="text" name="waktu" required class="essay">
                    </div>
                </div>
            </div>

            <!-- Upload Sertifikasi -->
            <div class="form-group">
                <label>Upload Sertifikat (JPG/PNG/PDF):</label>
                <div class="upload-box">
                    <div class="upload-icon">📄</div>
                    <label for="sertifikasi" class="upload-label">Pilih File</label>
                    <input type="file" id="sertifikasi" name="sertifikasi" accept=".jpg,.jpeg,.png,.pdf"
                        onchange="displayFileName(this)">
                    <div class="file-info">Format yang didukung: JPG, PNG, PDF (Max: 5MB)</div>
                    <div id="fileNameDisplay"></div>
                    <div id="imagePreview"></div>
                </div>
            </div>

            <div class="note">
                Kuesioner ini dipergunakan untuk perbaikan berkelanjutan, mohon diisi dengan sungguh-sungguh sesuai
                kondisi. Jika anda lupa atau ragu silahkan diisi mana yang mendekati kondisi saat itu.
            </div>

            <div class="divider"></div>

            <div class="two-columns">
                <div class="column">
                    <div class="section-title">Pelaksanaan Pelatihan</div>

                    <div class="rating-row">
                        <label>Tema Pelatihan</label>
                        <div class="rating-options">
                            <label><input type="radio" name="tema_pelatihan" value="1" required> 1</label>
                            <label><input type="radio" name="tema_pelatihan" value="2"> 2</label>
                            <label><input type="radio" name="tema_pelatihan" value="3"> 3</label>
                            <label><input type="radio" name="tema_pelatihan" value="4"> 4</label>
                            <label><input type="radio" name="tema_pelatihan" value="5"> 5</label>
                        </div>
                    </div>

                    <div class="rating-row">
                        <label>Ketepatan Waktu</label>
                        <div class="rating-options">
                            <label><input type="radio" name="ketepatan_waktu" value="1" required> 1</label>
                            <label><input type="radio" name="ketepatan_waktu" value="2"> 2</label>
                            <label><input type="radio" name="ketepatan_waktu" value="3"> 3</label>
                            <label><input type="radio" name="ketepatan_waktu" value="4"> 4</label>
                            <label><input type="radio" name="ketepatan_waktu" value="5"> 5</label>
                        </div>
                    </div>

                    <div class="rating-row">
                        <label>Suasana</label>
                        <div class="rating-options">
                            <label><input type="radio" name="suasana" value="1" required> 1</label>
                            <label><input type="radio" name="suasana" value="2"> 2</label>
                            <label><input type="radio" name="suasana" value="3"> 3</label>
                            <label><input type="radio" name="suasana" value="4"> 4</label>
                            <label><input type="radio" name="suasana" value="5"> 5</label>
                        </div>
                    </div>

                    <div class="rating-row">
                        <label>Kelengkapan Materi</label>
                        <div class="rating-options">
                            <label><input type="radio" name="kelengkapan_materi" value="1" required> 1</label>
                            <label><input type="radio" name="kelengkapan_materi" value="2"> 2</label>
                            <label><input type="radio" name="kelengkapan_materi" value="3"> 3</label>
                            <label><input type="radio" name="kelengkapan_materi" value="4"> 4</label>
                            <label><input type="radio" name="kelengkapan_materi" value="5"> 5</label>
                        </div>
                    </div>

                    <div class="rating-row">
                        <label>Servis / Sikap Penyelenggara</label>
                        <div class="rating-options">
                            <label><input type="radio" name="servis_penyelenggara" value="1" required> 1</label>
                            <label><input type="radio" name="servis_penyelenggara" value="2"> 2</label>
                            <label><input type="radio" name="servis_penyelenggara" value="3"> 3</label>
                            <label><input type="radio" name="servis_penyelenggara" value="4"> 4</label>
                            <label><input type="radio" name="servis_penyelenggara" value="5"> 5</label>
                        </div>
                    </div>

                    <div class="rating-row">
                        <label>Alat Bantu</label>
                        <div class="rating-options">
                            <label><input type="radio" name="alat_bantu_pelaksanaan" value="1" required> 1</label>
                            <label><input type="radio" name="alat_bantu_pelaksanaan" value="2"> 2</label>
                            <label><input type="radio" name="alat_bantu_pelaksanaan" value="3"> 3</label>
                            <label><input type="radio" name="alat_bantu_pelaksanaan" value="4"> 4</label>
                            <label><input type="radio" name="alat_bantu_pelaksanaan" value="5"> 5</label>
                        </div>
                    </div>

                    <div class="rating-row">
                        <label>Nilai Keseluruhan</label>
                        <div class="rating-options">
                            <label><input type="radio" name="nilai_keseluruhan_pelaksanaan" value="1" required>
                                1</label>
                            <label><input type="radio" name="nilai_keseluruhan_pelaksanaan" value="2"> 2</label>
                            <label><input type="radio" name="nilai_keseluruhan_pelaksanaan" value="3"> 3</label>
                            <label><input type="radio" name="nilai_keseluruhan_pelaksanaan" value="4"> 4</label>
                            <label><input type="radio" name="nilai_keseluruhan_pelaksanaan" value="5"> 5</label>
                        </div>
                    </div>
                </div>

                <div class="column">
                    <div class="section-title">Pembicara</div>

                    <div class="rating-row">
                        <label>Penguasaan Masalah</label>
                        <div class="rating-options">
                            <label><input type="radio" name="penguasaan_masalah_pembicara" value="1" required> 1</label>
                            <label><input type="radio" name="penguasaan_masalah_pembicara" value="2"> 2</label>
                            <label><input type="radio" name="penguasaan_masalah_pembicara" value="3"> 3</label>
                            <label><input type="radio" name="penguasaan_masalah_pembicara" value="4"> 4</label>
                            <label><input type="radio" name="penguasaan_masalah_pembicara" value="5"> 5</label>
                        </div>
                    </div>

                    <div class="rating-row">
                        <label>Cara Penyajian</label>
                        <div class="rating-options">
                            <label><input type="radio" name="cara_penyajian_pembicara" value="1" required> 1</label>
                            <label><input type="radio" name="cara_penyajian_pembicara" value="2"> 2</label>
                            <label><input type="radio" name="cara_penyajian_pembicara" value="3"> 3</label>
                            <label><input type="radio" name="cara_penyajian_pembicara" value="4"> 4</label>
                            <label><input type="radio" name="cara_penyajian_pembicara" value="5"> 5</label>
                        </div>
                    </div>

                    <div class="rating-row">
                        <label>Manfaat Materi</label>
                        <div class="rating-options">
                            <label><input type="radio" name="manfaat_materi" value="1" required> 1</label>
                            <label><input type="radio" name="manfaat_materi" value="2"> 2</label>
                            <label><input type="radio" name="manfaat_materi" value="3"> 3</label>
                            <label><input type="radio" name="manfaat_materi" value="4"> 4</label>
                            <label><input type="radio" name="manfaat_materi" value="5"> 5</label>
                        </div>
                    </div>

                    <div class="rating-row">
                        <label>Interaksi dengan Peserta</label>
                        <div class="rating-options">
                            <label><input type="radio" name="interaksi_peserta_pembicara" value="1" required> 1</label>
                            <label><input type="radio" name="interaksi_peserta_pembicara" value="2"> 2</label>
                            <label><input type="radio" name="interaksi_peserta_pembicara" value="3"> 3</label>
                            <label><input type="radio" name="interaksi_peserta_pembicara" value="4"> 4</label>
                            <label><input type="radio" name="interaksi_peserta_pembicara" value="5"> 5</label>
                        </div>
                    </div>

                    <div class="rating-row">
                        <label>Penggunaan Alat Bantu</label>
                        <div class="rating-options">
                            <label><input type="radio" name="alat_bantu_pembicara" value="1" required> 1</label>
                            <label><input type="radio" name="alat_bantu_pembicara" value="2"> 2</label>
                            <label><input type="radio" name="alat_bantu_pembicara" value="3"> 3</label>
                            <label><input type="radio" name="alat_bantu_pembicara" value="4"> 4</label>
                            <label><input type="radio" name="alat_bantu_pembicara" value="5"> 5</label>
                        </div>
                    </div>

                    <div class="rating-row">
                        <label>Nilai Keseluruhan</label>
                        <div class="rating-options">
                            <label><input type="radio" name="nilai_keseluruhan_pembicara" value="1" required> 1</label>
                            <label><input type="radio" name="nilai_keseluruhan_pembicara" value="2"> 2</label>
                            <label><input type="radio" name="nilai_keseluruhan_pembicara" value="3"> 3</label>
                            <label><input type="radio" name="nilai_keseluruhan_pembicara" value="4"> 4</label>
                            <label><input type="radio" name="nilai_keseluruhan_pembicara" value="5"> 5</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            <div class="two-columns">
                <div class="column">
                    <div class="section-title">Narasumber</div>

                    <div class="rating-row">
                        <label>Penguasaan Masalah</label>
                        <div class="rating-options">
                            <label><input type="radio" name="penguasaan_masalah_narasumber" value="1" required>
                                1</label>
                            <label><input type="radio" name="penguasaan_masalah_narasumber" value="2"> 2</label>
                            <label><input type="radio" name="penguasaan_masalah_narasumber" value="3"> 3</label>
                            <label><input type="radio" name="penguasaan_masalah_narasumber" value="4"> 4</label>
                            <label><input type="radio" name="penguasaan_masalah_narasumber" value="5"> 5</label>
                        </div>
                    </div>

                    <div class="rating-row">
                        <label>Cara Penyajian</label>
                        <div class="rating-options">
                            <label><input type="radio" name="cara_penyajian_narasumber" value="1" required> 1</label>
                            <label><input type="radio" name="cara_penyajian_narasumber" value="2"> 2</label>
                            <label><input type="radio" name="cara_penyajian_narasumber" value="3"> 3</label>
                            <label><input type="radio" name="cara_penyajian_narasumber" value="4"> 4</label>
                            <label><input type="radio" name="cara_penyajian_narasumber" value="5"> 5</label>
                        </div>
                    </div>

                    <div class="rating-row">
                        <label>Manfaat Materi</label>
                        <div class="rating-options">
                            <label><input type="radio" name="manfaat_materi_narasumber" value="1" required> 1</label>
                            <label><input type="radio" name="manfaat_materi_narasumber" value="2"> 2</label>
                            <label><input type="radio" name="manfaat_materi_narasumber" value="3"> 3</label>
                            <label><input type="radio" name="manfaat_materi_narasumber" value="4"> 4</label>
                            <label><input type="radio" name="manfaat_materi_narasumber" value="5"> 5</label>
                        </div>
                    </div>

                    <div class="rating-row">
                        <label>Interaksi dengan Peserta</label>
                        <div class="rating-options">
                            <label><input type="radio" name="interaksi_peserta_narasumber" value="1" required> 1</label>
                            <label><input type="radio" name="interaksi_peserta_narasumber" value="2"> 2</label>
                            <label><input type="radio" name="interaksi_peserta_narasumber" value="3"> 3</label>
                            <label><input type="radio" name="interaksi_peserta_narasumber" value="4"> 4</label>
                            <label><input type="radio" name="interaksi_peserta_narasumber" value="5"> 5</label>
                        </div>
                    </div>

                    <div class="rating-row">
                        <label>Penggunaan Alat Bantu</label>
                        <div class="rating-options">
                            <label><input type="radio" name="alat_bantu_narasumber" value="1" required> 1</label>
                            <label><input type="radio" name="alat_bantu_narasumber" value="2"> 2</label>
                            <label><input type="radio" name="alat_bantu_narasumber" value="3"> 3</label>
                            <label><input type="radio" name="alat_bantu_narasumber" value="4"> 4</label>
                            <label><input type="radio" name="alat_bantu_narasumber" value="5"> 5</label>
                        </div>
                    </div>

                    <div class="rating-row">
                        <label>Nilai Komentar & Saran</label>
                        <div class="rating-options">
                            <label><input type="radio" name="nilai_komentar_saran" value="1" required> 1</label>
                            <label><input type="radio" name="nilai_komentar_saran" value="2"> 2</label>
                            <label><input type="radio" name="nilai_komentar_saran" value="3"> 3</label>
                            <label><input type="radio" name="nilai_komentar_saran" value="4"> 4</label>
                            <label><input type="radio" name="nilai_komentar_saran" value="5"> 5</label>
                        </div>
                    </div>
                </div>

                <div class="column">
                    <div class="section-title">Lain-Lain</div>

                    <div class="rating-row">
                        <label>Makanan</label>
                        <div class="rating-options">
                            <label><input type="radio" name="makanan" value="1" required> 1</label>
                            <label><input type="radio" name="makanan" value="2"> 2</label>
                            <label><input type="radio" name="makanan" value="3"> 3</label>
                            <label><input type="radio" name="makanan" value="4"> 4</label>
                            <label><input type="radio" name="makanan" value="5"> 5</label>
                        </div>
                    </div>

                    <div class="rating-row">
                        <label>Sound System</label>
                        <div class="rating-options">
                            <label><input type="radio" name="sound_system" value="1" required> 1</label>
                            <label><input type="radio" name="sound_system" value="2"> 2</label>
                            <label><input type="radio" name="sound_system" value="3"> 3</label>
                            <label><input type="radio" name="sound_system" value="4"> 4</label>
                            <label><input type="radio" name="sound_system" value="5"> 5</label>
                        </div>
                    </div>

                    <div class="rating-row">
                        <label>Layanan Hotel</label>
                        <div class="rating-options">
                            <label><input type="radio" name="layanan_hotel" value="1" required> 1</label>
                            <label><input type="radio" name="layanan_hotel" value="2"> 2</label>
                            <label><input type="radio" name="layanan_hotel" value="3"> 3</label>
                            <label><input type="radio" name="layanan_hotel" value="4"> 4</label>
                            <label><input type="radio" name="layanan_hotel" value="5"> 5</label>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 30px;">
                        <label>Rencana Tindakan Penerapan:</label>
                        <textarea name="rencana_tindakan" rows="4" class="essay"></textarea>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Komentar Tambahan:</label>
                <textarea name="komentar_tambahan" rows="5" class="essay"></textarea>
            </div>

            <div class="signature-section">
                <p style="text-align: right;">
                    Sidoarjo,
                    <input type="text" name="tanggalSurat" required class="text-like"
                        placeholder="Tanggal Surat Dibuat">
                </p>

                <div class="ttd-wrapper">

                    <!-- KIRI -->
                    <div class="ttd-box">
                        <p><strong>Mengetahui,</strong></p>

                        <p class="ttd-title">
                            KEPALA SUB BAGIAN<br>
                            TATA USAHA
                        </p>

                        <div class="ttd-space"></div>

                        <input type="text" name="kepala" class="line-input" placeholder="Nama Lengkap" required>

                        <div class="nip-row">
                            <span>NIP.</span>
                            <input type="text" name="nipKepala" required class="essay">
                        </div>
                    </div>

                    <!-- KANAN -->
                    <div class="ttd-box">
                        <p><strong>Menyetujui,</strong></p>

                        <p class="ttd-title">
                            KETUA TEAM
                        </p>

                        <div class="ttd-space"></div>

                        <input type="text" name="ketua" class="line-input" placeholder="Nama Lengkap" required>

                        <div class="nip-row">
                            <span>NIP.</span>
                            <input type="text" name="nipKetua" required class="essay">
                        </div>
                    </div>

                </div>

                <div class="submit-section">
                    <button type="submit">Kirim Evaluasi</button>
                </div>
        </form>
    </div>

    <script>
        function displayFileName(input) {
            const fileNameDisplay = document.getElementById('fileNameDisplay');
            const imagePreview = document.getElementById('imagePreview');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileName = file.name;
                const fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB

                // Validasi ukuran file (maksimal 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar! Maksimal 5MB');
                    input.value = '';
                    fileNameDisplay.innerHTML = '';
                    imagePreview.innerHTML = '';
                    return;
                }

                // Tampilkan nama file
                fileNameDisplay.innerHTML = `<div class="file-name">✓ ${fileName} (${fileSize} MB)</div>`;

                // Preview untuk gambar
                if (file.type.match('image.*')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        imagePreview.innerHTML = `<img src="${e.target.result}" class="preview-image" alt="Preview">`;
                    };
                    reader.readAsDataURL(file);
                } else if (file.type === 'application/pdf') {
                    imagePreview.innerHTML = '<div style="margin-top: 10px; color: #666;">📄 File PDF siap diupload</div>';
                }
            }
        }
    </script>
</body>

</html>