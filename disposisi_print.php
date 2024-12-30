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
          DATE_FORMAT(sm.tanggal_masuk, '%d/%m/%Y') as tgl_masuk,
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

function numberToRoman($number)
{
    $map = array(
        'M' => 1000,
        'CM' => 900,
        'D' => 500,
        'CD' => 400,
        'C' => 100,
        'XC' => 90,
        'L' => 50,
        'XL' => 40,
        'X' => 10,
        'IX' => 9,
        'V' => 5,
        'IV' => 4,
        'I' => 1
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
            font-size: 15px;
        }

        .kop-space {
            width: 1653px;
            height: 219px;
        }

        .title {
            text-align: center;
            color: #1e40af;
            margin-bottom: 5px;
            font-size: 20px;
            font-weight: bold;
        }

        .nomor-surat {
            text-align: center;
            margin-bottom: 10px;
            font-size: 15px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
            max-width: 1000px;
            font-size: 15px;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 0px;
            vertical-align: middle;
        }

        .info-label {
            white-space: nowrap;
        }

        .separator {
            text-align: center;
        }

        .info-content {
            padding-left: 5px;
            max-width: 250px; /* Atur Atur disini */
            overflow-wrap: break-word;
            word-wrap: break-word;
            hyphens: auto;
        }

        .disposisi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 15px;
        }

        .disposisi-table td,
        .disposisi-table th {
            padding: 5px;
            border: 0.5px solid #999;
            vertical-align: top;
        }

        .disposisi-table td:first-child {
            width: 0, 5%;
        }

        .disposisi-table td:nth-child(2) {
            width: 0, 5%;
        }

        .disposisi-table td:nth-child(3) {
            width: 0, 5%;
        }

        .yth-section {
            margin: 10px 0;
        }

        .yth-box {
            border: 1px solid #000;
            min-height: 100px;
            margin-bottom: 10px;
            padding: 10px;
        }

        .bottom-section {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
        }

        .catatan {
            width: 30%;
        }

        .ttd {
            width: 30%;
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
            width: 30%;
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

        .disposisi-table td,
        .disposisi-table th {
            border: 0.5px solid #000;
        }

        .form-select {
            display: block;
            width: 100%;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        @media print {

            .no-print,
            .form-select {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="kop-space"></div>

    <div class="title">LEMBAR DISPOSISI</div>
    <div class="nomor-surat">Nomor: <?php echo $nomor_agenda; ?></div>

    <table class="info-table">
        <tr>
            <td class="info-label">Tanggal Masuk</td>
            <td class="separator">:</td>
            <td width="30%"><div class="info-content"><?php echo $data['tgl_masuk']; ?></div></td>
            <td class="info-label">Dari</td>
            <td class="separator">:</td>
            <td width="30%"><div class="info-content"><?php echo $data['dari']; ?></div></td>
        </tr>
        <tr>
            <td class="info-label">Tanggal Surat</td>
            <td class="separator">:</td>
            <td><div class="info-content"><?php echo $data['tanggal_surat']; ?></div></td>
            <td class="info-label">Ditujukan Yth</td>
            <td class="separator">:</td>
            <td><div class="info-content"><?php echo $data['tujuan']; ?></div></td>
        </tr>
        <tr>
            <td class="info-label">Lampiran</td>
            <td class="separator">:</td>
            <td><div class="info-content"></div></td>
            <td class="info-label">No Surat</td>
            <td class="separator">:</td>
            <td><div class="info-content"><?php echo $data['nomor_surat']; ?></div></td>
        </tr>
        <tr>
            <td class="info-label">Perihal</td>
            <td class="separator">:</td>
            <td ><div class="info-content"><?php echo $data['perihal']; ?></div></td>
        </tr>
    </table>

    <table class="disposisi-table">
        <tr>
            <th style="text-align: left;">Diteruskan Kepada Yth:</th>
            <th colspan="2">Isi Disposisi</th>
        </tr>
        <tr>
            <td>
                <div class="checkbox-item"><span class="item-space"></span>Wakil Dekan I</div>
                <div class="checkbox-item"><span class="item-space"></span>Wakil Dekan II</div>
                <div class="checkbox-item"><span class="item-space"></span>Wakil Dekan III</div>
                <div class="checkbox-item"><span class="item-space"></span>Ka.Prodi. Kimia</div>
                <div class="checkbox-item"><span class="item-space"></span>Ka. Prodi.Informatika</div>
                <div class="checkbox-item"><span class="item-space"></span>Ka. Prodi Sistem Informasi</div>
                <div class="checkbox-item"><span class="item-space"></span>Ka. Prodi. Magister (S2) Kimia</div>
                <div class="checkbox-item"><span class="item-space"></span>Ka. Prodi. Magister (S2)Informatika</div>
                <div class="checkbox-item"><span class="item-space"></span>Ka. Bag.Tata Usaha</div>
                <div class="checkbox-item"><span class="item-space"></span>Kaur ..........</div>
                <div class="checkbox-item"><span class="item-space"></span>Kaur ..........</div>
                <div class="checkbox-item"><span class="item-space"></span>Kaur ..........</div>
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
        </div>

        <div class="ttd">
            <?php if (!isset($_GET['preview'])): ?>
                <form id="ttdForm">
                    <select id="ttdSelect" class="form-select mb-3" onchange="updateTTD()">
                        <option value="">Pilih Tanda Tangan</option>
                        <option value="ttddani.jpg" <?php echo ($data['ttd_pejabat'] == 'ttddani.jpg' ? 'selected' : ''); ?>>Dani</option>
                        <option value="ttdmuhidan.jpg" <?php echo ($data['ttd_pejabat'] == 'ttdmuhidan.jpg' ? 'selected' : ''); ?>>Muhidin</option>
                    </select>
                </form>
            <?php endif; ?>

            <p>Kaur. Administrasi Umum</p>
            <?php if (isset($data['ttd_pejabat']) && !empty($data['ttd_pejabat'])): ?>
                <?php $ttd_path = "public/ttd/" . $data['ttd_pejabat']; ?>
                <img src="<?php echo $ttd_path; ?>"
                    alt="Tanda Tangan"
                    style="height: 100px; margin: 10px 0;">
            <?php else: ?>
                <div class="ttd-line"></div>
            <?php endif; ?>
            <p>Tanda Tangan & Nama Jelas</p>
        </div>
    </div>

    <div class="no-print" style="margin-top: 15px; text-align: center;">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Tutup</button>
    </div>

    <script>
        function updateTTD() {
            const ttdSelect = document.getElementById('ttdSelect');
            const selectedTTD = ttdSelect.value;
            const suratId = <?php echo $id; ?>;

            fetch('update_ttd.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${suratId}&ttd=${selectedTTD}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Gagal mengupdate tanda tangan');
                }
            });
        }
    </script>
</body>

</html>