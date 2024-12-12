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

// Ambil nama file sebelum menghapus data
$query = "SELECT file_surat FROM pos_masuk WHERE id = $id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

// Hapus file jika filenya ada
if (!empty($data['file_surat'])) {
    $file_path = "uploads/" . $data['file_surat'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}

// Hapus data dari database
$query = "DELETE FROM pos_masuk WHERE id = $id";
mysqli_query($conn, $query);
header("Location: index.php");
?> 