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

$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Surat Keluar - Sistem arsip surat</title>
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
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            margin-bottom: 0;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            border: 1px solid #dee2e6;
            padding: 1rem;
            text-align: center;
            color: var(--primary-color);
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        /* .table tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
            transform: scale(1.01);
        } */

        .table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .d-flex.gap-1 {
            display: flex;
            gap: 5px;
            justify-content: center;
        }

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
            margin: 0;
        }

        .btn-sm i {
            margin-right: 0;
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

        .main-content {
            margin-left: 280px;
            width: calc(100% - 280px);
            transition: all 0.3s ease;
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

        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .btn {
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, .1);
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

            .top-navbar img {
                width: 40px;
                height: 40px;
                object-fit: contain;
            }
        }

        .card-header .form-control {
            width: 300px;
            border-radius: 8px;
            padding: 0.6rem 1rem;
            border: 1px solid #e0e6ed;
            transition: all 0.3s ease;
        }

        .card-header .form-control:focus {
            border-color: #3498DB;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
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
                <img src="public/logo.JPG" alt="logo" style="width: 40px; height: 40px; object-fit: contain;">
            </div>

            <div class="container py-4">
                <div class="row mb-4">
                    <div class="col">
                        <?php if ($user_role == 'admin'): ?>
                            <a href="pos_keluar_add.php" class="btn btn-primary">
                                <i class="bi bi-plus-circle-fill"></i> Tambah Surat Keluar
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0"><i class="bi bi-send-fill me-2"></i>Data Surat Keluar</h4>
                            <form action="" method="GET" class="d-flex gap-2" style="max-width: 500px;">
                                <input type="text" name="search" class="form-control" placeholder="Cari nomor surat atau perihal..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i>
                                </button>
                                <?php if(isset($_GET['search'])): ?>
                                    <a href="surat_keluar.php" class="btn btn-secondary">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.main-content').classList.toggle('active');
        });
    </script>
</body>

</html>