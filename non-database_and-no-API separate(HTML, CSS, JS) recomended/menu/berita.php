<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita & Kegiatan - Departemen Sistem Informasi</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/berita/css/style-berita.css">
    
    <style>
        /* Override background hero image dengan path yang benar */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.5)), 
                        url('../assets/images/hero.jpg') center center/cover no-repeat;
            height: 300px;
            display: flex;
            align-items: center;
            position: relative;
            color: var(--color-white);
        }
        .hero .container {
            position: relative;
            z-index: 2;
        }
        .hero h1 {
            font-size: 36px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
        }
    </style>
</head>
<body id="top">

    <header class="site-header" id="siteHeader">
        <div class="container">
            <div class="logo-area">
                <a href="../beranda.php">
                    <img src="../assets/images/logo-placeholder.png" alt="Logo Departemen" style="height: 50px; width: auto;">
                </a>
            </div>
            
            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="Buka menu navigasi">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <nav class="main-navigation" id="primary-menu">
                <ul>
                    <li><a href="../beranda.php">Beranda</a></li>
                    
                    <li class="has-dropdown">
                        <a href="profil.php" class="dropdown-toggle">Profil</a>
                        <ul class="dropdown-menu">
                            <li><a href="profil.php#visi-misi">Visi & Misi</a></li>
                            <li><a href="profil.php#sejarah">Sejarah</a></li>
                            <li><a href="profil.php#manajemen">Manajemen</a></li>
                        </ul>
                    </li>
                    
                    <li class="has-dropdown">
                        <a href="berita.php" class="dropdown-toggle" aria-current="page">Berita & Kegiatan</a>
                        <ul class="dropdown-menu">
                            <li><a href="berita.php">Berita Terbaru</a></li>
                            <li><a href="berita.php">Agenda Kegiatan</a></li>
                        </ul>
                    </li>
                    
                    <li><a href="proyek.php">Proyek</a></li>
                    <li><a href="galeri.php">Galeri</a></li>
                    <li><a href="kontak.php">Kontak</a></li>
                    <li><a href="../login.php">Login</a></li>

                    <li class="nav-search">
                        <form action="../search.php" method="get" class="nav-search-form">
                            <input type="search" name="q" placeholder="Cari..." aria-label="Cari" class="nav-search-input">
                            <button type="submit" class="nav-search-button" aria-label="Cari">&#128269;</button>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main>

        <section class="hero">
            <div class="container">
                <h1>Berita & Kegiatan</h1>
            </div>
        </section>

        <div class="main-content-area">
            <div class="container">
                
                <div class="primary-content">

                    <section class="event-highlight">
                        <div class="event-highlight-img">
                            <img src="../assets/images/event-highlight.jpg" alt="Banner Seminar AI">
                        </div>
                        <div class="event-highlight-content">
                            <span class="event-tag">Kegiatan Terdekat</span>
                            <h2>Seminar Nasional Implementasi AI</h2>
                            <p class="event-date">25 November 2025 | 09:00 - 12:00 WIB</p>
                            <p>Membahas tren terbaru dan implementasi Artificial Intelligence di industri, dibawakan oleh praktisi ahli.</p>
                        </div>
                    </section>
                    
                    <form class="filter-bar" action="#" method="get">
                        <div class="filter-group">
                            <label for="filter-kategori">Kategori:</label>
                            <select id="filter-kategori" name="kategori">
                                <option value="semua">Semua</option>
                                <option value="berita">Berita</option>
                                <option value="kegiatan">Kegiatan</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="filter-tahun">Tahun:</label>
                            <select id="filter-tahun" name="tahun">
                                <option value="2025">2025</option>
                                <option value="2024">2024</option>
                                <option value="2023">2023</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-filter">Filter</button>
                    </form>

                    <article class="facility-item image-left">
                        <div class="facility-image">
                            <img src="../assets/images/berita-1.jpg" alt="Gambar Pelatihan Keamanan Siber">
                        </div>
                        <div class="facility-text">
                            <h2>Pelatihan Keamanan Siber Internal 2025</h2>
                            <span class="article-metadata">Penulis: Admin | 18 Okt 2025 | Kategori: Berita</span>
                            <p>Departemen menyelenggarakan pelatihan audit keamanan internal untuk staf dan mahasiswa guna meningkatkan kesadaran akan pentingnya keamanan data di lingkungan akademik.</p>
                            <a href="menu-detail-berita/detail-berita.php" class="btn">Lihat Detail</a>
                        </div>
                    </article>
                    
                    <article class="facility-item image-right">
                        <div class="facility-image">
                            <img src="../assets/images/berita-2.jpg" alt="Gambar Kolaborasi Riset">
                        </div>
                        <div class="facility-text">
                            <h2>Kolaborasi Riset Big Data dengan Industri</h2>
                            <span class="article-metadata">Penulis: Admin | 15 Okt 2025 | Kategori: Berita</span>
                            <p>Penandatanganan MoU baru untuk riset bersama di bidang analisis data prediktif telah dilaksanakan dengan beberapa mitra industri terkemuka di bidang telekomunikasi.</p>
                            <a href="menu-detail/detail-berita.php" class="btn">Lihat Detail</a>
                        </div>
                    </article>
                    
                    <article class="facility-item image-left">
                        <div class="facility-image">
                            <img src="../assets/images/berita-3.jpg" alt="Gambar Workshop Mobile">
                        </div>
                        <div class="facility-text">
                            <h2>Workshop Pengembangan Aplikasi Mobile</h2>
                            <span class="article-metadata">Penulis: Admin | 11 Okt 2025 | Kategori: Kegiatan</span>
                            <p>Mahasiswa antusias mengikuti workshop intensif pengembangan aplikasi cross-platform yang dibimbing langsung oleh developer profesional selama dua hari penuh.</p>
                            <a href="menu-detail/detail-berita.php" class="btn">Lihat Detail</a>
                        </div>
                    </article>
                    
                </div>
                
                <aside class="sidebar">
                    
                    <div class="widget widget-calendar">
                        <h3 class="widget-title">Kalender Kegiatan</h3>
                        <table class="calendar-table">
                            <caption>November 2025</caption>
                            <thead>
                                <tr>
                                    <th>Sn</th> <th>Sl</th> <th>Rb</th> <th>Km</th> <th>Jm</th> <th>Sb</th> <th>Mg</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="other-month">27</td><td class="other-month">28</td><td class="other-month">29</td><td class="other-month">30</td><td class="other-month">31</td><td>1</td><td>2</td>
                                </tr>
                                <tr>
                                    <td>3</td><td>4</td><td class="today">5</td><td>6</td><td>7</td><td>8</td><td>9</td>
                                </tr>
                                <tr>
                                    <td>10</td><td>11</td><td>12</td><td class="event"><a href="#" title="Kuliah Tamu">13</a></td><td>14</td><td>15</td><td>16</td>
                                </tr>
                                <tr>
                                    <td>17</td><td>18</td><td>19</td><td>20</td><td>21</td><td>22</td><td>23</td>
                                </tr>
                                <tr>
                                    <td>24</td><td class="event"><a href="#" title="Seminar AI">25</a></td><td>26</td><td>27</td><td>28</td><td>29</td><td>30</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="widget widget-publications">
                        <h3 class="widget-title">Publikasi Ilmiah</h3>
                        <ul>
                            <li>
                                <h4><a href="#">Model Prediktif untuk Retensi Pelanggan</a></h4>
                                <span class="pub-source">Jurnal Internasional Q1 (2025)</span>
                                <div class="pub-links">
                                    <a href="#">DOI</a>
                                    <a href="#">PDF</a>
                                </div>
                            </li>
                            <li>
                                <h4><a href="#">Analisis Framework Tata Kelola IT</a></h4>
                                <span class="pub-source">Prosiding IEEE (2025)</span>
                                <div class="pub-links">
                                    <a href="#">DOI</a>
                                    <a href="#">PDF</a>
                                </div>
                            </li>
                            <li>
                                <h4><a href="#">Deteksi Anomali Jaringan (Deep Learning)</a></h4>
                                <span class="pub-source">Jurnal Nasional Sinta 2 (2024)</span>
                                <div class="pub-links">
                                    <a href="#">PDF</a>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="widget widget-categories">
                        <h3 class="widget-title">Arsip</h3>
                        <ul>
                            <li><a href="#">Arsip 2025</a></li>
                            <li><a href="#">Arsip 2024</a></li>
                            <li><a href="#">Arsip 2023</a></li>
                            <li><a href="#">Arsip 2022</a></li>
                        </ul>
                    </div>

                </aside>
                
            </div>
        </div>
        
    </main>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                
                <div class="footer-col">
                    <img src="../assets/images/logo-footer-placeholder.png" alt="Logo Lab Polinema Footer" style="height: 60px; margin-bottom: 20px;">
                    <p>
                        <strong>Laboratorium Mobile and Multimedia Tech</strong><br>
                        Politeknik Negeri Malang<br>
                        Malang, Indonesia
                    </p>
                    <div class="social-links-footer">
                        <a href="#" aria-label="Facebook">F</a>
                        <a href="#" aria-label="Twitter">T</a>
                        <a href="#" aria-label="Instagram">I</a>
                        <a href="#" aria-label="YouTube">Y</a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h4>Tautan Cepat</h4>
                    <ul>
                        <li><a href="../beranda.php">Beranda</a></li>
                        <li><a href="profil.php">Profil</a></li>
                        <li><a href="berita.php">Berita & Kegiatan</a></li>
                        <li><a href="proyek.php">Proyek</a></li>
                        <li><a href="galeri.php">Galeri</a></li>
                    </ul>
                </div>
                
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

    <a href="#top" id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">
        &uarr;
    </a>

    <script src="assets/berita/js/script-berita.js"></script>

</body>
</html>