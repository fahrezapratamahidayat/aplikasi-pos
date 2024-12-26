<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false]));
}

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'pos_system';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die(json_encode(['success' => false]));
}

if (isset($_POST['id']) && isset($_POST['ttd'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $ttd = mysqli_real_escape_string($conn, $_POST['ttd']);
    
    $query = "UPDATE disposisi SET ttd_pejabat = ? WHERE surat_masuk_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $ttd, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
} 