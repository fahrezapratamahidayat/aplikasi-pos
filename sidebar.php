<?php
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="sidebar">
    <div class="sidebar-header">
        <img src="public/logo.JPG" alt="Logo" class="sidebar-logo">
        <div class="sidebar-brand">Sistem arsip surat</div>
    </div>
    
    <div class="sidebar-menu">
        <?php if ($current_page == 'user.php'): ?>
            <a href="user.php" class="nav-link active">
                <i class="bi bi-house-fill"></i>
                <span>Dashboard</span>
            </a>
        <?php else: ?>
            <a href="index.php" class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                <i class="bi bi-house-fill"></i>
                <span>Dashboard</span>
            </a>
        <?php endif; ?>

        <div class="menu-label mt-4">Main Menu</div>
        
        <a href="surat_masuk.php" class="nav-link <?php echo $current_page == 'surat_masuk.php' ? 'active' : ''; ?>">
            <i class="bi bi-inbox-fill"></i>
            <span>Surat Masuk</span>
        </a>
        
        <a href="surat_keluar.php" class="nav-link <?php echo $current_page == 'surat_keluar.php' ? 'active' : ''; ?>">
            <i class="bi bi-send-fill"></i>
            <span>Surat Keluar</span>
        </a>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="login.php" class="nav-link logout-link">
                <i class="bi bi-box-arrow-in-right"></i>
                <span>Login</span>
            </a>
        <?php else: ?>
            <a href="logout.php" class="nav-link logout-link">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        <?php endif; ?>
    </div>
</nav> 