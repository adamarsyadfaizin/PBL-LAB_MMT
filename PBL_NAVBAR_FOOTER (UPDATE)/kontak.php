<?php
if (!isset($_SESSION)) session_start();
// Include navbar component
require_once 'components/navbar.php';
// Include footer component
require_once 'components/footer.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak - Laboratorium Mobile and Multimedia Tech POLINEMA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/kontak/css/style-kontak.css">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

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

        /* Tombol email kuning konsisten dengan tombol lain */
        .btn-email {
            background: #ffc107;
            color: #000;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
            transition: opacity 0.3s;
        }
        .btn-email:hover {
            opacity: 0.8;
        }

        /* Peta styling */
        #map {
            width: 100%;
            height: 300px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(0,0,0,0.15);
        }
        .map-msg {
            font-size: 13px;
            color: #666;
            margin-top: 8px;
        }
    </style>
</head>
<body id="top">

    <?php
    // Render navbar dengan halaman aktif 'kontak'
    renderNavbar('kontak');
    ?>

    <main>

        <section class="hero">
            <div class="container">
                <h1>Hubungi Kami</h1>
            </div>
        </section>

        <div class="main-content-area">
            <div class="container">
                
                <div class="primary-content">
                    <h2>Kirim Pesan</h2>
                    <p>Punya pertanyaan atau ingin berkolaborasi? Silakan isi formulir di bawah ini dan kami akan segera menghubungi Anda.</p>
                    
                    <form class="contact-form" action="#" method="post">
                        <div class="form-group">
                            <label for="contact-name">Nama Lengkap</label>
                            <input type="text" id="contact-name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="contact-email">Alamat Email</label>
                            <input type="email" id="contact-email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="contact-subject">Subjek</label>
                            <input type="text" id="contact-subject" name="subject" required>
                        </div>
                        <div class="form-group">
                            <label for="contact-message">Pesan Anda</label>
                            <textarea id="contact-message" name="message" rows="6" required></textarea>
                        </div>
                        <button type="submit" class="btn">Kirim Pesan</button>
                    </form>
                </div>
                
                <aside class="sidebar">
                    
                    <div class="widget">
                        <h3 class="widget-title">Informasi Kontak</h3>
                        <div class="widget-contact-info">
                            <p>
                                <strong>Alamat:</strong>
                                Jl. Soekarno Hatta No.9, Jatimulyo, Kec. Lowokwaru, Kota Malang, Jawa Timur 65141
                            </p>
                            <p>
                                <strong>Email:</strong>
                                <a href="mailto:info@polinema.ac.id">info@polinema.ac.id</a>
                            </p>
                            <p>
                                <strong>Telepon:</strong>
                                (0341) 404 424
                            </p>
                        </div>
                    </div>

                    <div class="widget">
                        <h3 class="widget-title">Lokasi Kami</h3>

                        <!-- MAP -->
                        <div id="map"></div>
                        <div class="map-msg" id="mapMsg">Memuat peta…</div>

                    </div>

                    <div class="widget">
                        <h3 class="widget-title">Media Sosial</h3>
                        <div class="widget-social">
                            <div class="widget-social-links">
                                <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
                                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                                <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                                <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            </div>

                            <a href="mailto:adamkian09@gmail.com?subject=Halo%20Lab%20POLINEMA" class="btn-email">
                                Kirim Email ke Adam
                            </a>
                        </div>
                    </div>

                </aside>
                
            </div>
        </div>
        
    </main>

    <?php
    include 'components/floating_profile.php'; 
    renderFloatingProfile();
    renderFooter();
    ?>

    <!-- Include JavaScript untuk navbar -->
    <script src="assets/js/navbar.js"></script>
    <script src="assets/kontak/js/script-kontak.js"></script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        (function(){
            const address = "Jl. Soekarno Hatta No.9, Jatimulyo, Lowokwaru, Malang";

            const map = L.map('map', { scrollWheelZoom: false }).setView([-7.9666, 112.6326], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap',
                maxZoom: 19
            }).addTo(map);

            const mapMsg = document.getElementById('mapMsg');

            const nominatimUrl = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(address);

            fetch(nominatimUrl)
                .then(res => res.json())
                .then(data => {
                    if (data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lon = parseFloat(data[0].lon);

                        map.setView([lat, lon], 16);

                        const marker = L.marker([lat, lon]).addTo(map);
                        marker.bindPopup(`<strong>Laboratorium Mobile & Multimedia Tech POLINEMA</strong><br>${address}`).openPopup();

                        mapMsg.textContent = "";
                    } else {
                        mapMsg.textContent = "Lokasi tidak ditemukan — menampilkan area Malang.";
                    }
                })
                .catch(() => {
                    mapMsg.textContent = "Gagal memuat lokasi otomatis.";
                });

        })();
    </script>

</body>
</html>
