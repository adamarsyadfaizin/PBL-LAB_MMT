<?php
// PBL/menu/components/floating_profile.php

function renderFloatingProfile() {
    // Pastikan sesi dimulai
    if (!isset($_SESSION)) session_start();
    
    // Keluar jika user_id tidak diset
    if (!isset($_SESSION['user_id'])) {
        return;
    }
    
    // Asumsi: BASE_URL sudah didefinisikan di global scope (misal: /PBL/)
    if (!defined('BASE_URL')) {
        define('BASE_URL', '/PBL/'); 
    }

    $v = time();
    $user_id_display = htmlspecialchars($_SESSION['user_id'] ?? 'N/A');
    
    // Jalur ASET RELATIF dari BASE_URL:
    // Kita harus mengakses folder menu/components/ dari BASE_URL
    $asset_path = 'menu/components/';
    
    ?>

    <link rel="stylesheet" href="<?= BASE_URL . $asset_path ?>floating-profile.css?v=<?= $v ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <div class="floating-profile" id="floatingProfileBtn">
        <i class="fas fa-user-shield"></i> 
    </div>

    <div class="floating-menu" id="floatingMenu">
        <div class="menu-header">
            <i class="fas fa-fingerprint"></i>
            ID Pengguna: <?= $user_id_display ?>
        </div>
        <hr>
        <a href="profile.php">
            <i class="fas fa-id-badge"></i> Kelola Profil
        </a>
        <a href="logout.php">
            <i class="fas fa-power-off"></i> Keluar (Logout)
        </a>
    </div>

    <script defer src="<?= BASE_URL . $asset_path ?>floating-profile.js?v=<?= $v ?>"></script>

    <?php
}
?>