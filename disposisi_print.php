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

// Tambahkan fungsi untuk konversi angka ke romawi
function numberToRoman($number) {
    $map = array(
        'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
        'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
        'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1
    );
    $result = '';
    foreach ($map as $roman => $value) {
        while ($number >= $value) {
            $result .= $roman;
            $number -= $value;
        }
    }
    return $result;
}

// Setelah query data, tambahkan:
$bulan = date('n', strtotime($data['tanggal_masuk'])); // Ambil bulan dari tanggal masuk
$tahun = date('Y', strtotime($data['tanggal_masuk'])); // Ambil tahun
$bulan_romawi = numberToRoman($bulan); // Konversi bulan ke romawi

// Generate nomor agenda
$nomor_urut = str_pad($data['nomor_urut'], 4, '0', STR_PAD_LEFT);
$nomor_agenda = "{$nomor_urut}/U/FSI-UNJANI/{$bulan_romawi}/{$tahun}";
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

        .title {
            text-align: center;
            margin-bottom: 5px;
            font-size: 16px;
            font-weight: bold;
        }

        .nomor-surat {
            text-align: center;
            margin-bottom: 20px;
            font-size: 12px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
        }

        .info-table td {
            padding: 5px;
            border: 1px solid #000;
        }

        .disposisi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
        }

        .disposisi-table td, .disposisi-table th {
            padding: 5px;
            border: 1px solid #000;
            vertical-align: top;
        }

        .disposisi-table td:first-child {
            width: 30%;
        }

        .disposisi-table td:nth-child(2) {
            width: 35%;
        }

        .disposisi-table td:nth-child(3) {
            width: 35%;
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

        .disposisi-table td.isi-disposisi {
            display: flex;
            justify-content: space-between;
        }

        .disposisi-column {
            width: 48%;
        }
    </style>
</head>
<body>
    <div class="title">LEMBAR DISPOSISI</div>
    <div class="nomor-surat">Nomor: <?php echo $nomor_agenda; ?></div>

    <table class="info-table">
        <tr>
            <td width="20%">Tanggal Masuk</td>
            <td width="30%">: <?php echo $data['tgl_masuk']; ?></td>
            <td width="20%">Dari</td>
            <td width="30%">: <?php echo $data['dari']; ?></td>
        </tr>
        <tr>
            <td>Tanggal Surat</td>
            <td>: <?php echo $data['tanggal_surat']; ?></td>
            <td>Ditujukan Yth</td>
            <td>: <?php echo $data['tujuan']; ?></td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>: </td>
            <td>No Surat</td>
            <td>: <?php echo $data['nomor_agenda']; ?></td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td colspan="3">: <?php echo $data['perihal']; ?></td>
        </tr>
    </table>

    <table class="disposisi-table">
        <tr>
            <th>Diteruskan Kepada</th>
            <th colspan="2">Isi Disposisi</th>
        </tr>
        <tr>
            <td>
                 Wakil Dekan 1<br>
                 Wakil Dekan 2<br>
                 Wakil Dekan 3<br>
                 Kaprodi 1<br>
                 Kaprodi 2<br>
                 Kaprodi 3<br>
                 Kaprodi 4<br>
                 Kaprodi 5<br>
                 Kaprodi 6<br>
                 Kaur 1<br>
                 Kaur 2<br>
                 Kaur 3
            </td>
            <td>
                 Untuk diketahui<br>
                 Untuk dilaporkan<br>
                 Untuk ditindaklanjuti<br>
                 Untuk diproses lebih lanjut<br>
                 Untuk diarsipkan<br>
                 Untuk ditelaah dan saran<br>
                 Untuk dibicarakan dengan saya<br>
                 Untuk dikoordinasikan<br>
                 Untuk diselesaikan
            </td>
            <td>
                 Edarkan<br>
                 Jadwalkan<br>
                 Pelajari<br>
                 Persiapkan<br>
                 Selesaikan<br>
                 Setuju<br>
                 Tolak<br>
                 Tunggu<br>
                 Monitor
            </td>
        </tr>
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