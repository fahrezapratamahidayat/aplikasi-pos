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

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$query = "SELECT * FROM pos_masuk WHERE id = $id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = $_POST['tanggal'];
    $tanggal_masuk = $_POST['tanggal_masuk'];
    $jenis_surat = $_POST['jenis_surat'];
    $perihal = $_POST['perihal'];
    $asal_surat = $_POST['asal_surat'];
    $keterangan = $_POST['keterangan'];

    $file_surat = $data['file_surat'];

    if (isset($_FILES['file_surat']) && $_FILES['file_surat']['error'] == 0) {
        $target_dir = "uploads/";
        $file_extension = pathinfo($_FILES["file_surat"]["name"], PATHINFO_EXTENSION);
        $file_surat = date('YmdHis') . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $file_surat;

        if (!empty($data['file_surat']) && file_exists($target_dir . $data['file_surat'])) {
            unlink($target_dir . $data['file_surat']);
        }

        move_uploaded_file($_FILES["file_surat"]["tmp_name"], $target_file);
    }

    $query = "UPDATE pos_masuk SET 
              tanggal = '$tanggal',
              tanggal_masuk = '$tanggal_masuk',
              jenis_surat = '$jenis_surat',
              perihal = '$perihal',
              asal_surat = '$asal_surat',
              file_surat = '$file_surat',
              keterangan = '$keterangan' WHERE id = $id";

    if (mysqli_query($conn, $query)) {
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Surat Masuk - Sistem arsip surat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #2C3E50;
            --secondary-color: #34495E;
            --accent-color: #27AE60;
            --success-color: #27AE60;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, .08);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 1.2rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 8px;
            padding: 0.6rem 1rem;
            border: 1px solid #e0e6ed;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(39, 174, 96, 0.15);
            background-color: #fff;
        }

        textarea.form-control {
            min-height: 100px;
        }

        .btn {
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, .1);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-color), #219a52);
            border: none;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
            border: none;
        }

        .current-file {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-top: 12px;
            border: 1px dashed #dee2e6;
            transition: all 0.3s ease;
        }

        .current-file:hover {
            background-color: #fff;
            border-color: var(--accent-color);
        }

        .current-file a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .current-file a:hover {
            color: #219a52;
            text-decoration: underline;
        }

        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        .upload-area:hover {
            border-color: var(--accent-color);
            background: #fff;
        }

        .upload-icon {
            font-size: 2rem;
            color: var(--accent-color);
            margin-bottom: 1rem;
        }

        .card-body {
            padding: 2rem;
        }

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
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Surat Masuk</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-calendar3 me-1"></i>Tanggal Surat
                                    </label>
                                    <input type="date" name="tanggal" class="form-control"
                                        value="<?php echo $data['tanggal']; ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-calendar3 me-1"></i>Tanggal Masuk
                                    </label>
                                    <input type="date" name="tanggal_masuk" class="form-control"
                                        value="<?php echo $data['tanggal_masuk']; ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-tag me-1"></i>Jenis Surat
                                    </label>
                                    <input type="text" name="jenis_surat" class="form-control"
                                        value="<?php echo $data['jenis_surat']; ?>" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">
                                        <i class="bi bi-chat-square-text me-1"></i>Perihal
                                    </label>
                                    <textarea name="perihal" class="form-control" rows="3"
                                        required><?php echo $data['perihal']; ?></textarea>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">
                                        <i class="bi bi-building me-1"></i>Asal Surat
                                    </label>
                                    <input type="text" name="asal_surat" class="form-control"
                                        value="<?php echo $data['asal_surat']; ?>" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">
                                        <i class="bi bi-file-earmark-arrow-up me-1"></i>Upload Surat
                                    </label>
                                    <div class="upload-area">
                                        <i class="bi bi-cloud-arrow-up upload-icon"></i>
                                        <h5>Drag & Drop file atau klik untuk memilih</h5>
                                        <input type="file" name="file_surat" class="form-control"
                                            accept=".pdf,.doc,.docx">
                                    </div>
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Format yang diperbolehkan: PDF, DOC, DOCX. Biarkan kosong jika tidak ingin
                                        mengubah file.
                                    </div>
                                    <?php if ($data['file_surat']): ?>
                                        <div class="current-file">
                                            <i class="bi bi-file-earmark-text me-2"></i>
                                            File saat ini:
                                            <a href="uploads/<?php echo $data['file_surat']; ?>" target="_blank">
                                                <i class="bi bi-eye me-1"></i>Lihat File
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">
                                        <i class="bi bi-journal-text me-1"></i>Keterangan
                                    </label>
                                    <textarea name="keterangan" class="form-control"
                                        rows="3"><?php echo $data['keterangan']; ?></textarea>
                                </div>

                                <div class="col-12">
                                    <hr class="my-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="index.php" class="btn btn-secondary">
                                            <i class="bi bi-arrow-left me-1"></i>Kembali
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save me-1"></i>Update
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
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