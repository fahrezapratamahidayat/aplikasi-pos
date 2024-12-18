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
          DATE_FORMAT(d.tanggal_masuk, '%d/%m/%Y') as tgl_masuk,
          sm.tipe_surat
          FROM pos_masuk sm 
          LEFT JOIN disposisi d ON sm.id = d.surat_masuk_id 
          WHERE sm.id = $id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    header("Location: surat_masuk.php");
    exit();
}

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

$bulan = date('n', strtotime($data['tanggal_masuk']));
$tahun = date('Y', strtotime($data['tanggal_masuk']));  
$bulan_romawi = numberToRoman($bulan);

// Generate nomor agenda berdasarkan tipe surat
if ($data['tipe_surat'] == 'dana') {
    $nomor_urut = str_pad($data['nomor_urut_dana'], 4, '0', STR_PAD_LEFT);
} else {
    $nomor_urut = str_pad($data['nomor_urut_umum'], 4, '0', STR_PAD_LEFT);
}
if ($data['tipe_surat'] == 'dana') {
    $nomor_agenda = "{$nomor_urut}/D/FSI-UNJANI/{$bulan_romawi}/{$tahun}";
} else {
    $nomor_agenda = "{$nomor_urut}/U/FSI-UNJANI/{$bulan_romawi}/{$tahun}";
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

        .kop-space {
            width: 1655px;
            height: 219px;
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
        }

        .disposisi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
        }

        .disposisi-table td, .disposisi-table th {
            padding: 5px;
            border: 0.5px solid #999;
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

        .checkbox-item {
            border-bottom: 0.5px solid #999;
            margin: 0;
            padding: 0;
            line-height: normal;
            height: 22px;
            display: flex;
            align-items: center;
        }

        .item-space {
            width: 25px;
            height: 100%;
            border-right: 0.5px solid #999;
            margin-right: 8px;
        }

        .disposisi-table td {
            padding: 0;
            vertical-align: top;
        }

        .disposisi-table td, .disposisi-table th {
            border: 0.5px solid #000;
        }
    </style>
</head>
<body>
    <div class="kop-space"></div>

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
            <td>: <?php echo $data['nomor_surat']; ?></td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td colspan="3">: <?php echo $data['perihal']; ?></td>
        </tr>
    </table>

    <table class="disposisi-table">
        <tr>
            <th style="text-align: left;">Diteruskan Kepada Yth:</th>
            <th colspan="2">Isi Disposisi</th>
        </tr>
        <tr>
            <td>
                <div class="checkbox-item"><span class="item-space"></span>Wakil Dekan 1</div>
                <div class="checkbox-item"><span class="item-space"></span>Wakil Dekan 2</div>
                <div class="checkbox-item"><span class="item-space"></span>Wakil Dekan 3</div>
                <div class="checkbox-item"><span class="item-space"></span>Kaprodi 1</div>
                <div class="checkbox-item"><span class="item-space"></span>Kaprodi 2</div>
                <div class="checkbox-item"><span class="item-space"></span>Kaprodi 3</div>
                <div class="checkbox-item"><span class="item-space"></span>Kaprodi 4</div>
                <div class="checkbox-item"><span class="item-space"></span>Kaprodi 5</div>
                <div class="checkbox-item"><span class="item-space"></span>Kaprodi 6</div>
                <div class="checkbox-item"><span class="item-space"></span>Kaur 1</div>
                <div class="checkbox-item"><span class="item-space"></span>Kaur 2</div>
                <div class="checkbox-item"><span class="item-space"></span>Kaur 3</div>
            </td>
            <td>
                <div class="checkbox-item"><span class="item-space"></span>Untuk diketahui</div>
                <div class="checkbox-item"><span class="item-space"></span>Untuk dilaporkan</div>
                <div class="checkbox-item"><span class="item-space"></span>Untuk ditindaklanjuti</div>
                <div class="checkbox-item"><span class="item-space"></span>Untuk diproses lebih lanjut</div>
                <div class="checkbox-item"><span class="item-space"></span>Untuk diarsipkan</div>
                <div class="checkbox-item"><span class="item-space"></span>Untuk ditelaah dan saran</div>
                <div class="checkbox-item"><span class="item-space"></span>Untuk dibicarakan dengan saya</div>
                <div class="checkbox-item"><span class="item-space"></span>Untuk dikoordinasikan</div>
                <div class="checkbox-item"><span class="item-space"></span>Untuk diselesaikan</div>
            </td>
            <td>
                <div class="checkbox-item"><span class="item-space"></span>Edarkan</div>
                <div class="checkbox-item"><span class="item-space"></span>Jadwalkan</div>
                <div class="checkbox-item"><span class="item-space"></span>Pelajari</div>
                <div class="checkbox-item"><span class="item-space"></span>Persiapkan</div>
                <div class="checkbox-item"><span class="item-space"></span>Selesaikan</div>
                <div class="checkbox-item"><span class="item-space"></span>Setuju</div>
                <div class="checkbox-item"><span class="item-space"></span>Tolak</div>
                <div class="checkbox-item"><span class="item-space"></span>Tunggu</div>
                <div class="checkbox-item"><span class="item-space"></span>Monitor</div>
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