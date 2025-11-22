<?php
// menu/components/navbar.php

if (!isset($site_config)) {
    $site_config = [
        'logo_path' => 'assets/images/logo-placeholder.png'
    ];
}

function renderNavbar($activePage = 'beranda') {
    global $site_config; 

    // --- LOGIKA PATH OTOMATIS ---
    $path_prefix = "";
    if (file_exists("assets/css/style.css")) {
        $path_prefix = ""; 
    } elseif (file_exists("../assets/css/style.css")) {
        $path_prefix = "../"; 
    } elseif (file_exists("../../assets/css/style.css")) {
        $path_prefix = "../../"; 
    }

    $logo_src = $path_prefix . ($site_config['logo_path'] ?? 'assets/images/logo-placeholder.png');
    ?>
    
    <header class="site-header">
        <div class="container">
            <div class="logo-area">
                <a href="<?= $path_prefix ?>beranda.php" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
                    <img src="<?= htmlspecialchars($logo_src) ?>" alt="Logo Laboratorium" style="height: 45px; width: auto;">
                    <span style="font-weight: 700; font-size: 18px; color: #fff; letter-spacing: 0.5px;">LAB MMT</span>
                </a>
            </div>

            <button class="menu-toggle" aria-label="Toggle navigation" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <nav class="main-navigation">
                <ul>
                    <li><a href="<?= $path_prefix ?>beranda.php" aria-current="<?= $activePage === 'beranda' ? 'page' : 'false' ?>">Beranda</a></li>
                    <li><a href="<?= $path_prefix ?>menu/profil.php" aria-current="<?= $activePage === 'profil' ? 'page' : 'false' ?>">Profil</a></li>
                    <li><a href="<?= $path_prefix ?>menu/berita.php" aria-current="<?= $activePage === 'berita' ? 'page' : 'false' ?>">Berita</a></li>
                    <li><a href="<?= $path_prefix ?>menu/proyek.php" aria-current="<?= $activePage === 'proyek' ? 'page' : 'false' ?>">Proyek</a></li>
                    <li><a href="<?= $path_prefix ?>menu/galeri.php" aria-current="<?= $activePage === 'galeri' ? 'page' : 'false' ?>">Galeri</a></li>
                    <li><a href="<?= $path_prefix ?>menu/kontak.php" aria-current="<?= $activePage === 'kontak' ? 'page' : 'false' ?>">Kontak</a></li>

                    <li class="nav-search">
                        <form action="<?= $path_prefix ?>menu/proyek.php" method="get" class="nav-search-form">
                            <input type="text" name="s" class="nav-search-input" placeholder="Cari...">
                            <button type="submit" class="nav-search-button"><i class="fas fa-search"></i></button>
                        </form>
                    </li>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li>
                            <a href="<?= $path_prefix ?>menu/user_profile.php" class="user-profile-nav">
                                <i class="fas fa-user-circle"></i> <?= htmlspecialchars(substr($_SESSION['user_name'], 0, 8)) ?>..
                            </a>
                        </li>
                        <li>
                            <a href="<?= $path_prefix ?>menu/process_logout.php" style="color: #ff9999; font-size: 14px;"><i class="fas fa-sign-out-alt"></i></a>
                        </li>
                    <?php else: ?>
                        <li>
                            <a href="<?= $path_prefix ?>menu/login.php" class="btn-login-nav">LOGIN</a>
                        </li>
                    <?php endif; ?>

                </ul>
            </nav>
        </div>
    </header>
    <?php
}
?>