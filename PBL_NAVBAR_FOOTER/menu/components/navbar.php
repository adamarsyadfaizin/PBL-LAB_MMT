<?php
/**
 * File: navbar.php
 * Komponen navigasi yang reusable - bisa dipanggil di semua halaman
 */

// Ganti sesuai folder project kamu di localhost atau hosting
// Contoh: http://localhost/PBL/  → '/PBL/'
// Kalau langsung di domain utama → '/'
if (!defined('BASE_URL')) {
    define('BASE_URL', '/PBL/'); 
}

function renderNavbar($currentPage = '') {
?>
<header class="site-header" id="siteHeader">
    <div class="container">
        <!-- Logo -->
        <div class="logo-area">
            <a href="<?php echo BASE_URL; ?>beranda.php">
                <img src="<?php echo BASE_URL; ?>assets/images/logo-placeholder.png" 
                     alt="Logo Departemen" 
                     style="height: 50px; width: auto;">
            </a>
        </div>

        <!-- Navigasi -->
        <nav class="main-navigation" id="primary-menu">
            <ul>
                <li>
                    <a href="<?php echo BASE_URL; ?>beranda.php" 
                       <?php echo ($currentPage == 'beranda') ? 'aria-current="page"' : ''; ?>>
                       Beranda
                    </a>
                </li>

                <li class="has-dropdown">
                    <a href="<?php echo BASE_URL; ?>menu/profil.php" 
                       class="dropdown-toggle" 
                       <?php echo ($currentPage == 'profil') ? 'aria-current="page"' : ''; ?>>
                       Profil
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo BASE_URL; ?>menu/profil.php#visi-misi">Visi & Misi</a></li>
                        <li><a href="<?php echo BASE_URL; ?>menu/profil.php#sejarah">Sejarah</a></li>
                        <li><a href="<?php echo BASE_URL; ?>menu/profil.php#struktur">Struktur</a></li>
                        <li><a href="<?php echo BASE_URL; ?>menu/profil.php#tim">Tim Kami</a></li>
                    </ul>
                </li>

                <li class="has-dropdown">
                    <a href="<?php echo BASE_URL; ?>menu/berita.php" 
                       class="dropdown-toggle" 
                       <?php echo ($currentPage == 'berita') ? 'aria-current="page"' : ''; ?>>
                       Berita & Kegiatan
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo BASE_URL; ?>menu/berita.php">Berita Terbaru</a></li>
                        <li><a href="<?php echo BASE_URL; ?>menu/berita.php">Agenda Kegiatan</a></li>
                    </ul>
                </li>

                <li>
                    <a href="<?php echo BASE_URL; ?>menu/proyek.php" 
                       <?php echo ($currentPage == 'proyek') ? 'aria-current="page"' : ''; ?>>
                       Proyek
                    </a>
                </li>

                <li>
                    <a href="<?php echo BASE_URL; ?>menu/galeri.php" 
                       <?php echo ($currentPage == 'galeri') ? 'aria-current="page"' : ''; ?>>
                       Galeri
                    </a>
                </li>

                <li>
                    <a href="<?php echo BASE_URL; ?>menu/kontak.php" 
                       <?php echo ($currentPage == 'kontak') ? 'aria-current="page"' : ''; ?>>
                       Kontak
                    </a>
                </li>

                <li>
                    <a href="<?php echo BASE_URL; ?>menu/login.php" 
                       <?php echo ($currentPage == 'login') ? 'aria-current="page"' : ''; ?>>
                       Login
                    </a>
                </li>

                <!-- Search -->
                <li class="nav-search">
                    <form action="<?php echo BASE_URL; ?>search.php" method="get" class="nav-search-form">
                        <input type="search" name="q" placeholder="Cari..." aria-label="Cari" class="nav-search-input">
                        <button type="submit" class="nav-search-button" aria-label="Cari">&#128269;</button>
                    </form>
                </li>
            </ul>
        </nav>

        <!-- Tombol menu responsive -->
        <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="Buka menu navigasi">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>
<?php
}
?>
