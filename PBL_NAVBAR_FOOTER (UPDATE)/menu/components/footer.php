<?php
/**
 * menu/components/footer.php
 * Footer global - aman di semua level folder (pakai BASE_URL)
 */

if (!defined('BASE_URL')) {
    define('BASE_URL', '/PBL/'); // <- sesuaikan root project kamu
}

function renderFooter() {
?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            
            <!-- Kolom 1: Logo & Sosial Media -->
            <div class="footer-col">
                <img src="<?php echo BASE_URL; ?>assets/images/logo-footer-placeholder.png" 
                     alt="Logo Lab Polinema Footer" 
                     style="height: 60px; margin-bottom: 20px;">
                <p>
                    <strong>Laboratorium Mobile and Multimedia Tech</strong><br>
                    Politeknik Negeri Malang<br>
                    Malang, Indonesia
                </p>

                <!-- Media Sosial -->
                <div class="social-links-footer">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            
            <!-- Kolom 2: Tautan Cepat -->
            <div class="footer-col">
                <h4>Tautan Cepat</h4>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>beranda.php">Beranda</a></li>
                    <li><a href="<?php echo BASE_URL; ?>menu/profil.php">Profil</a></li>
                    <li><a href="<?php echo BASE_URL; ?>menu/berita.php">Berita & Kegiatan</a></li>
                    <li><a href="<?php echo BASE_URL; ?>menu/proyek.php">Proyek</a></li>
                    <li><a href="<?php echo BASE_URL; ?>menu/galeri.php">Galeri</a></li>
                    <li><a href="<?php echo BASE_URL; ?>menu/kontak.php">Kontak</a></li>
                </ul>
            </div>
            
            <!-- Kolom 3: Kontak -->
            <div class="footer-col">
                <h4>Kontak & Kebijakan</h4>
                <p>
                    Jl. Soekarno Hatta No.9, Jatimulyo,<br>
                    Kec. Lowokwaru, Kota Malang,<br>
                    Jawa Timur 65141
                </p>
                <p>
                    Email: <a href="mailto:info@polinema.ac.id">info@polinema.ac.id</a><br>
                    Telepon: (0341) 404 424
                </p>
                <ul class="policy-links">
                    <li><a href="#">Kebijakan Privasi</a></li>
                    <li><a href="#">Ketentuan Layanan</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-copyright">
            <p>&copy; 2025 Laboratorium Mobile and Multimedia Tech POLINEMA. Hak Cipta Dilindungi.</p>
        </div>
    </div>
</footer>

<!-- Tombol ke atas -->
<a href="#top" id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">&uarr;</a>

<!-- Font Awesome -->
<link rel="stylesheet" 
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    .social-links-footer a {
        color: inherit;
        text-decoration: none;
        margin-right: 12px;
        font-size: 18px;
        transition: opacity 0.3s;
    }
    .social-links-footer a:hover {
        opacity: 0.7;
    }
</style>
<?php
}
?>
