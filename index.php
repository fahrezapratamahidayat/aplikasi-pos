<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: user.php");
    exit();
}
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'pos_system';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';

$active_table = isset($_GET['active_table']) ? $_GET['active_table'] : '';

// query untuk hitung total surat masuk
$query_masuk = "SELECT COUNT(*) as total FROM pos_masuk";
$result_masuk = mysqli_query($conn, $query_masuk);
$total_masuk = mysqli_fetch_assoc($result_masuk)['total'];

// query untuk hitung total surat keluar
$query_keluar = "SELECT COUNT(*) as total FROM pos_keluar";
$result_keluar = mysqli_query($conn, $query_keluar);
$total_keluar = mysqli_fetch_assoc($result_keluar)['total'];

// query untuk hitung surat masuk hari ini
$query_masuk_today = "SELECT COUNT(*) as total FROM pos_masuk WHERE DATE(tanggal) = CURDATE()";
$result_masuk_today = mysqli_query($conn, $query_masuk_today);
$total_masuk_today = mysqli_fetch_assoc($result_masuk_today)['total'];

// query untuk hitung surat keluar hari ini bawaan
$query_keluar_today = "SELECT COUNT(*) as total FROM pos_keluar WHERE DATE(tanggal) = CURDATE()";
$result_keluar_today = mysqli_query($conn, $query_keluar_today);
$total_keluar_today = mysqli_fetch_assoc($result_keluar_today)['total'];
?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard - Sistem arsip surat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #2C3E50;
            --secondary-color: #34495E;
            --accent-color: #3498DB;
            --success-color: #27AE60;
            --warning-color: #F39C12;
            --danger-color: #E74C3C;
        }

        body {
            background-color: #F5F6FA;
            font-family: 'Segoe UI', sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 1rem 0;
            box-shadow: 0 2px 15px rgba(0, 0, 0, .1);
        }

        .navbar-brand {
            font-size: 1.4rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .brand-icon-wrapper {
            background: rgba(255, 255, 255, .15);
            padding: 8px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .navbar-brand:hover .brand-icon-wrapper {
            background: rgba(255, 255, 255, .25);
            transform: translateY(-2px);
        }

        .brand-text {
            background: linear-gradient(to right, #fff, rgba(255, 255, 255, .9));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-link {
            color: rgba(255, 255, 255, .9) !important;
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            display: flex;
            align-items: center;
            margin: 0 0.2rem;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, .15);
            color: white !important;
            transform: translateY(-2px);
        }

        .nav-link i {
            font-size: 1.1rem;
        }

        .navbar-toggler {
            padding: 0.5rem;
            transition: all 0.3s ease;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .navbar-toggler:hover {
            background: rgba(255, 255, 255, .1);
        }

        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: rgba(0, 0, 0, .05);
                margin-top: 1rem;
                padding: 1rem;
                border-radius: 10px;
                backdrop-filter: blur(10px);
            }

            .nav-link {
                padding: 0.8rem 1.2rem;
                margin: 0.2rem 0;
            }
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .05);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background-color: white;
            border-bottom: 2px solid #f8f9fa;
            padding: 1.25rem;
            border-radius: 15px 15px 0 0 !important;
        }

        .btn {
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn i {
            margin-right: 0.5rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, .1);
        }

        .table {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
            padding: 1rem;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
        }

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
        }

        .document-icon {
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }

        .section-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--accent-color);
            display: inline-block;
        }

        .action-buttons .btn {
            margin: 0 0.2rem;
        }

        .table-responsive {
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .05);
        }

        .file-link {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 500;
        }

        .file-link:hover {
            text-decoration: underline;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            position: fixed;
            height: 100vh;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-logo {
            width: 60px;
            height: 60px;
            margin-bottom: 10px;
        }

        .sidebar-brand {
            color: white;
            font-size: 1.4rem;
            font-weight: 600;
            text-decoration: none;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-label {
            color: rgba(255, 255, 255, 0.6);
            padding: 0 20px;
            margin-bottom: 10px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .nav-link {
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8) !important;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white !important;
            border-left-color: var(--accent-color);
        }

        .nav-link i {
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .main-content {
            margin-left: 280px;
            width: calc(100% - 280px);
            transition: all 0.3s ease;
        }

        .top-navbar {
            background: white;
            height: 60px;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .navbar-logo {
            width: 40px;
            height: 40px;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                margin-left: -280px;
            }

            .sidebar.active {
                margin-left: 0;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .main-content.active {
                margin-left: 280px;
            }
        }

        .logout-link {
            margin-top: auto;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
        }

        /* Tambahkan style untuk dashboard cards */
        .dashboard-card {
            border-radius: 15px;
            padding: 1.5rem;
            height: 100%;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .05);
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, .1);
        }

        .dashboard-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .dashboard-title {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }

        .dashboard-value {
            font-size: 2rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .bg-soft-primary {
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--accent-color);
        }

        .bg-soft-success {
            background-color: rgba(39, 174, 96, 0.1);
            color: var(--success-color);
        }

        .welcome-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }

        .top-navbar img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>

        <div class="main-content">
            <div class="top-navbar">
                <button class="btn btn-link d-lg-none" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <img src="public/logo.JPG" alt="logo">
            </div>

            <div class="container py-4">
                <div class="welcome-card">
                    <h2 class="mb-0">Selamat Datang di Sistem arsip surat</h2>
                    <?php if ($user_role == 'admin'): ?>
                        <p class="mb-0 mt-2 opacity-75">Overview statistik surat masuk dan keluar</p>
                    <?php endif; ?>
                </div>
                <div class="row g-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="dashboard-card" style="cursor: pointer;" onclick="showTable('surat_masuk')">
                            <div class="dashboard-icon bg-soft-primary">
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                            <div class="dashboard-title">Total Surat Masuk</div>
                            <div class="dashboard-value"><?php echo $total_masuk; ?></div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="dashboard-card" style="cursor: pointer;" onclick="showTable('surat_keluar')">
                            <div class="dashboard-icon bg-soft-success">
                                <i class="bi bi-send-fill"></i>
                            </div>
                            <div class="dashboard-title">Total Surat Keluar</div>
                            <div class="dashboard-value"><?php echo $total_keluar; ?></div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <a href="surat_masuk.php">
                            <div class="dashboard-card">
                                <div class="dashboard-icon bg-soft-primary">
                                    <i class="bi bi-inbox-fill"></i>
                                </div>
                                <div class="dashboard-title">Surat Masuk Hari Ini</div>
                                <div class="dashboard-value"><?php echo $total_masuk_today; ?></div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <a href="surat_keluar.php">
                            <div class="dashboard-card">
                                <div class="dashboard-icon bg-soft-success">
                                    <i class="bi bi-cursor-fill"></i>
                                </div>
                                <div class="dashboard-title">Surat Keluar Hari Ini</div>
                                <div class="dashboard-value"><?php echo $total_keluar_today; ?></div>
                            </div>
                        </a>
                    </div>
                </div>

                <div id="surat_masuk_table" class="mt-4" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <h4 class="mb-0"><i class="bi bi-inbox-fill me-2"></i>Data Surat Masuk</h4>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="hideTable('surat_masuk')">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                                <form action="" method="GET" class="d-flex gap-2" style="max-width: 500px;">
                                    <input type="hidden" name="active_table" value="surat_masuk">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Cari nomor surat atau perihal..."
                                        value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i>
                                    </button>
                                    <?php if (isset($_GET['search'])): ?>
                                        <a href="?active_table=surat_masuk" class="btn btn-secondary">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal Masuk</th>
                                            <th>Tanggal Surat</th>
                                            <th>Nomor Surat</th>
                                            <th>Jenis Surat</th>
                                            <th>File</th>
                                            <th>Dari</th>
                                            <th>Perihal</th>
                                            <th>Ditujukan</th>
                                            <?php if ($user_role == 'admin'): ?>
                                                <th>Aksi</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
                                        $query = "SELECT sm.*, d.tanggal_masuk, d.nomor_agenda, d.dari, d.tujuan 
                                             FROM pos_masuk sm 
                                             LEFT JOIN disposisi d ON sm.id = d.surat_masuk_id";

                                        if (!empty($search)) {
                                            $query .= " WHERE d.nomor_agenda LIKE '%$search%' 
                                                    OR sm.perihal LIKE '%$search%'";
                                        }

                                        $query .= " ORDER BY d.tanggal_masuk DESC";
                                        $result = mysqli_query($conn, $query);
                                        $no = 1;
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . $no++ . "</td>";
                                            echo "<td>" . ($row['tanggal_masuk'] ? date('d/m/Y', strtotime($row['tanggal_masuk'])) : '-') . "</td>";
                                            echo "<td>" . date('d/m/Y', strtotime($row['tanggal'])) . "</td>";
                                            echo "<td>" . ($row['nomor_agenda'] ?? '-') . "</td>";
                                            echo "<td>" . $row['jenis_surat'] . "</td>";
                                            echo "<td><a href='uploads/" . $row['file_surat'] . "' target='_blank' class='btn btn-sm btn-info'><i class='bi bi-eye'></i> Lihat</a></td>";
                                            echo "<td>" . ($row['dari'] ?? $row['asal_surat']) . "</td>";
                                            echo "<td>" . $row['perihal'] . "</td>";
                                            echo "<td>" . ($row['tujuan'] ?? '-') . "</td>";
                                            if ($user_role == 'admin') {
                                                echo "<td class='actions'>
                                                <div class='d-flex gap-1'>
                                                    <a href='pos_masuk_edit.php?id=" . $row['id'] . "' class='btn btn-sm btn-warning' title='Edit'>
                                                        <i class='bi bi-pencil'>
                                                        Edit
                                                        </i>
                                                    </a>
                                                    <a href='pos_masuk_delete.php?id=" . $row['id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin hapus?\")' title='Hapus'>
                                                        <i class='bi bi-trash'>
                                                        Hapus
                                                        </i>
                                                    </a>
                                                    <a href='disposisi_view.php?id=" . $row['id'] . "' class='btn btn-sm btn-info' title='Lihat Disposisi'>
                                                        <i class='bi bi-eye'>
                                                        Dispo
                                                        </i>
                                                    </a>
                                                    <a href='disposisi_print.php?id=" . $row['id'] . "' class='btn btn-sm btn-success' target='_blank' title='Print Disposisi'>
                                                        <i class='bi bi-printer'>
                                                        print
                                                        </i>
                                                    </a>
                                                </div>
                                            </td>";
                                            }
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="surat_keluar_table" class="mt-4" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <h4 class="mb-0"><i class="bi bi-send-fill me-2"></i>Data Surat Keluar</h4>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="hideTable('surat_keluar')">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                                <form action="" method="GET" class="d-flex gap-2" style="max-width: 500px;">
                                    <input type="hidden" name="active_table" value="surat_keluar">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Cari nomor surat atau perihal..."
                                        value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i>
                                    </button>
                                    <?php if (isset($_GET['search'])): ?>
                                        <a href="?active_table=surat_keluar" class="btn btn-secondary">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Jenis Surat</th>
                                            <th>Perihal</th>
                                            <th>Tujuan Surat</th>
                                            <th>File</th>
                                            <th>Keterangan</th>
                                            <?php if ($user_role == 'admin'): ?>
                                                <th>Aksi</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
                                        $query = "SELECT * FROM pos_keluar";

                                        if (!empty($search)) {
                                            $query .= " WHERE jenis_surat LIKE '%$search%' 
                                                            OR perihal LIKE '%$search%'";
                                        }

                                        $query .= " ORDER BY tanggal DESC";
                                        $result = mysqli_query($conn, $query);
                                        $no = 1;
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . $no++ . "</td>";
                                            echo "<td>" . date('d/m/Y', strtotime($row['tanggal'])) . "</td>";
                                            echo "<td>" . $row['jenis_surat'] . "</td>";
                                            echo "<td>" . $row['perihal'] . "</td>";
                                            echo "<td>" . $row['tujuan_surat'] . "</td>";
                                            echo "<td><a href='uploads/" . $row['file_surat'] . "' target='_blank' class='btn btn-sm btn-info'><i class='bi bi-eye'></i> Lihat</a></td>";
                                            echo "<td>" . $row['keterangan'] . "</td>";
                                            if ($user_role == 'admin') {
                                                echo "<td>
                                                <div class='d-flex gap-1'>
                                                    <a href='pos_keluar_edit.php?id=" . $row['id'] . "' class='btn btn-sm btn-warning'><i class='bi bi-pencil'></i></a>
                                                    <a href='pos_keluar_delete.php?id=" . $row['id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin hapus?\")'><i class='bi bi-trash'></i></a>
                                                </div>
                                            </td>";
                                            }
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.main-content').classList.toggle('active');
        });

        // untuk aktigin menu aktive
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function () {
                navLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });

        function showTable(type) {
            const url = new URL(window.location);
            url.searchParams.set('active_table', type);
            window.history.pushState({}, '', url);

            document.getElementById('surat_masuk_table').style.display = 'none';
            document.getElementById('surat_keluar_table').style.display = 'none';

            document.getElementById(type + '_table').style.display = 'block';

            document.getElementById(type + '_table').scrollIntoView({
                behavior: 'smooth'
            });
        }

        function hideTable(type) {
            document.getElementById(type + '_table').style.display = 'none';
            const url = new URL(window.location);
            url.searchParams.delete('active_table');
            window.history.pushState({}, '', url);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const activeTable = '<?php echo $active_table; ?>';
            if (activeTable) {
                showTable(activeTable);
            }
        });
    </script>
</body>

</html>