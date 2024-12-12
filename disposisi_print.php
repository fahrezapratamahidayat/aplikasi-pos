<?php
session_start();
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'pos_system';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];

$query = "SELECT sm.*, d.*, 
          DATE_FORMAT(sm.tanggal, '%d/%m/%Y') as tanggal_surat,
          DATE_FORMAT(d.tanggal_masuk, '%d/%m/%Y') as tgl_masuk
          FROM pos_masuk sm 
          LEFT JOIN disposisi d ON sm.id = d.surat_masuk_id 
          WHERE sm.id = $id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    header("Location: surat_masuk.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Print Disposisi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.4;
            margin: 0;
            padding: 15px;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            position: relative;
        }

        .header img {
            height: 60px;
            position: absolute;
            top: 0;
        }

        .header img.left-logo {
            left: 0;
        }

        .header img.right-logo {
            right: 0;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 16px;
        }

        .header p {
            margin: 3px 0;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
        }

        table td, table th {
            padding: 5px;
            border: 1px solid #000;
            vertical-align: top;
        }

        .yth-section {
            margin: 10px 0;
        }

        .yth-box {
            border: 1px solid #000;
            min-height: 50px;
            margin-bottom: 10px;
            padding: 5px;
        }

        .bottom-section {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
        }

        .catatan {
            width: 45%;
        }

        .ttd {
            width: 45%;
            text-align: center;
        }

        .ttd-line {
            border-bottom: 1px solid #000;
            width: 150px;
            margin: 50px auto 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="logo-kiri.png" alt="Logo Kiri" class="left-logo">
        <img src="logo-kanan.png" alt="Logo Kanan" class="right-logo">
        <h2>YAYASAN KARTIKA EKA PAKSI</h2>
        <p>UNIVERSITAS JENDERAL ACHMAD YANI (UNJANI)</p>
        <p>FAKULTAS SAINS DAN INFORMATIKA (FSI)</p>
        <p>Kampus Cimahi : Jl. Terusan Jenderal Sudirman P.O.BOX 148 Telp. (022) 6650646</p>
        <h2>LEMBAR DISPOSISI</h2>
    </div>

    <table>
        <tr>
            <td class="label">Tanggal Masuk</td>
            <td>: <?php echo date('d/m/Y', strtotime($data['tanggal_masuk'])); ?></td>
            <td class="label">Tanggal Surat</td>
            <td>: <?php echo date('d/m/Y', strtotime($data['tanggal'])); ?></td>
        </tr>
        <tr>
            <td class="label">Dari</td>
            <td>: Rektor Unjani</td>
            <td class="label">Dituju Yth</td>
            <td>: Dekan FSI</td>
        </tr>
        <tr>
            <td class="label">Nomor Surat</td>
            <td>: B/1364/Unjani/X/2024</td>
            <td class="label">Perihal</td>
            <td>: Undangan Pembukaan dan Penutupan LDKK</td>
        </tr>
    </table>

    <table>
        <tr>
            <th>Diteruskan Kepada Yth</th>
            <th>ISI DISPOSISI</th>
        </tr>
        <tr>
            <td>Wakil Dekan I</td>
            <td>Untuk Diketahui</td>
        </tr>
        <!-- Tambahkan baris lain sesuai kebutuhan -->
    </table>

    <div class="yth-section">
        <p><strong>Yth:</strong></p>
        <div class="yth-box"></div>
        
        <p><strong>Yth:</strong></p>
        <div class="yth-box"></div>
        
        <p><strong>Yth:</strong></p>
        <div class="yth-box"></div>
    </div>

    <div class="bottom-section">
        <div class="catatan">
            <p><strong>Catatan:</strong></p>
            <div style="min-height: 80px; border-bottom: 1px solid #000;"></div>
        </div>
        
        <div class="ttd">
            <p>Kaur. Administrasi Umum</p>
            <div class="ttd-line"></div>
            <p>Tanda Tangan & Nama Jelas</p>
        </div>
    </div>

    <div class="no-print" style="margin-top: 15px; text-align: center;">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Tutup</button>
    </div>
</body>
</html> 