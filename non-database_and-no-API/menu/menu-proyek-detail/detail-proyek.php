<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Judul Diubah -->
    <title>Detail Proyek: Deteksi Kemacetan - Sistem Informasi</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <style type="text/css">
        /* ---
           File: style.css
           Deskripsi: Stylesheet utama (SAMA DENGAN HALAMAN LAINNYA)
           + Tambahan style untuk Halaman Detail Proyek
        --- */

        /* ==== 1. Variabel Global & Reset ==== */
        :root {
            --color-primary: #003b8e;
            --color-accent: #ffc700;
            --color-accent-hover: #ffb700;
            --color-white: #ffffff;
            --color-bg-light: #f5f5f5;
            --color-text: #000000;
            --color-text-secondary: #666666;
            --color-text-darker: #444444;
            --color-border-light: #eeeeee;
            --color-dropdown-bg: #ffffff;
            --color-dropdown-border: #dddddd;
            --color-dropdown-hover: #f5f5f5;
            --color-mobile-submenu-bg: #002f80;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
            font-family: var(--font-body);
        }

        body {
            font-family: var(--font-body);
            font-size: 16px;
            color: var(--color-text);
            line-height: 1.6;
            background: var(--color-white);
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ==== 2. Aksesibilitas & Utilitas ==== */
        :focus-visible {
            outline: 3px solid rgba(255, 199, 0, 0.45);
            outline-offset: 2px;
            border-radius: 4px;
        }

        .container {
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 24px;
            padding-right: 24px;
        }

        img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        a {
            color: var(--color-primary);
            text-decoration: none;
            transition: color 0.28s ease;
        }
        a:hover {
            color: var(--color-accent-hover);
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
            font-weight: 700;
            line-height: 1.3;
        }

        .btn {
            display: inline-block;
            background: var(--color-accent);
            color: var(--color-text);
            padding: 10px 22px;
            border-radius: 6px;
            font-weight: 600;
            font-family: var(--font-body);
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: transform 0.18s ease, background-color 0.18s ease;
        }

        .btn:hover {
            background: var(--color-accent-hover);
            color: var(--color-text);
            transform: scale(1.03);
        }

        .btn-secondary {
            background: transparent;
            color: var(--color-primary);
            border: 2px solid var(--color-primary);
            padding: 8px 20px;
        }
        .btn-secondary:hover {
            background: var(--color-primary);
            color: var(--color-white);
            transform: scale(1.03);
        }
        
        .section-title {
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 40px;
            text-transform: uppercase;
        }


        /* ==== 3. Header & Navigasi (SAMA) ==== */
        .site-header {
            height: 60px;
            background: var(--color-primary);
            color: var(--color-white);
            position: sticky;
            top: 0;
            z-index: 999;
            width: 100%;
            transition: box-shadow 0.3s ease;
        }
        .site-header.header-scrolled { box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08); }
        .site-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100%;
        }
        .logo-area a { display: flex; align-items: center; color: var(--color-white); text-decoration: none; }
        .main-navigation ul { display: flex; list-style: none; gap: 24px; }
        .main-navigation li { margin: 0; position: relative; }
        .main-navigation a {
            color: var(--color-white);
            font-weight: 600;
            font-size: 15px;
            text-transform: uppercase;
            text-decoration: none;
            padding: 20px 0;
            position: relative;
            display: block;
        }
        .main-navigation a[aria-current="page"] { color: var(--color-accent); }
        .main-navigation a::after {
            content: '';
            display: block;
            width: 0;
            height: 3px;
            background: var(--color-accent);
            position: absolute;
            bottom: 10px;
            left: 0;
            transition: width 0.3s ease;
        }
        .main-navigation a:hover::after,
        .main-navigation a[aria-current="page"]::after {
            width: 100%;
        }

        /* MODIFIKASI: CSS untuk Search Bar (Sesuai Gambar) */
        .nav-search {
            display: flex;
            align-items: center;
            margin-left: 16px; /* Kasih jarak dari link Login */
        }
        .nav-search-form {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.1); /* Latar belakang transparan */
            border-radius: 999px; /* Sangat rounded */
            overflow: hidden;
            height: 36px;
            margin-top: 12px; /* Menyamakan posisi vertikal */
            border: 1px solid rgba(255, 255, 255, 0.2); /* Border tipis */
        }
        .nav-search-input {
            border: none;
            background: transparent;
            padding: 0 16px;
            font-size: 14px;
            font-family: var(--font-body);
            color: var(--color-white); /* Teks putih */
            height: 100%;
            width: 160px;
            transition: width 0.3s ease;
        }
        .nav-search-input:focus {
            width: 200px;
            outline: none;
        }
        .nav-search-input::placeholder { /* Style placeholder */
            color: rgba(255, 255, 255, 0.6);
            opacity: 1;
        }
        .nav-search-button {
            border: none;
            background: transparent;
            color: rgba(255, 255, 255, 0.7); /* Warna icon */
            cursor: pointer;
            height: 100%;
            padding: 0 16px;
            font-size: 16px;
            transition: color 0.2s ease;
        }
        .nav-search-button:hover {
            color: var(--color-white);
        }
        /* AKHIR MODIFIKASI CSS Search Bar */

        .main-navigation .has-dropdown > a { padding-right: 14px; }
        .main-navigation .has-dropdown > a::before {
            content: '\25BC';
            font-size: 10px;
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.7;
            transition: transform 0.2s ease;
        }
        .main-navigation .has-dropdown:hover > a::before { transform: translateY(-50%) rotate(180deg); }

        /* ---- 3.1 Dropdown Menu (Desktop) (SAMA) ---- */
        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            min-width: 230px;
            background: var(--color-dropdown-bg);
            border: 1px solid var(--color-dropdown-border);
            box-shadow: 0 5px 10px rgba(0,0,0,0.08);
            z-index: 1000;
            border-radius: 0 0 6px 6px;
            border-top: 3px solid var(--color-accent);
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: opacity 0.25s ease, visibility 0.25s ease, transform 0.25s ease;
        }
        @media (min-width: 769px) {
            .main-navigation .has-dropdown:hover > .dropdown-menu {
                display: block;
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
            }
        }
        .dropdown-menu li { width: 100%; gap: 0; }
        .dropdown-menu li a {
            color: var(--color-text);
            font-weight: 400;
            font-size: 14px;
            text-transform: none;
            padding: 12px 18px;
            white-space: nowrap;
            transition: background-color 0.2s ease, color 0.2s ease;
        }
        .dropdown-menu li a:hover { background: var(--color-dropdown-hover); color: var(--color-primary); }
        .dropdown-menu li a::after { display: none; }
        .dropdown-menu li a::before { display: none; }

        /* ---- 3.2 Navigasi Mobile (SAMA) ---- */
        .menu-toggle { display: none; background: none; border: none; color: var(--color-white); cursor: pointer; padding: 10px; z-index: 1001; width: 44px; height: 44px; }
        .menu-toggle span { display: block; width: 24px; height: 3px; background: var(--color-white); margin: 5px 0; transition: transform 0.3s ease, opacity 0.3s ease; }
        .menu-toggle[aria-expanded="true"] span:nth-child(1) { transform: translateY(8px) rotate(45deg); }
        .menu-toggle[aria-expanded="true"] span:nth-child(2) { opacity: 0; }
        .menu-toggle[aria-expanded="true"] span:nth-child(3) { transform: translateY(-8px) rotate(-45deg); }

        /* ==== 4. Hero Section (SAMA) ==== */
        .hero {
            height: 430px; 
            width: 100%;
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.5)), 
                        url('../../assets/images/hero.jpg') center center/cover no-repeat;
            display: flex;
            align-items: center; 
            position: relative;
            color: var(--color-white);
        }
        .hero .container { position: relative; }
        .hero h1 {
            font-size: 48px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
            max-width: 800px; 
        }
        
        /* ==== 5. Main Content Area (Layout 2 kolom) ==== */
        .main-content-area {
            padding-top: 80px;
            padding-bottom: 80px;
        }
        .main-content-area > .container {
            display: flex;
            flex-wrap: wrap; 
            gap: 48px; 
        }
        .primary-content {
            flex: 1; 
            min-width: 0; 
        }
        .sidebar {
            width: 360px; 
            flex-shrink: 0;
        }

        /* ==== 6. Style Konten (dari file lain, di-reuse) ==== */
        .widget { border-radius: 8px; overflow: hidden; border: 1px solid var(--color-border-light); background: var(--color-white); margin-bottom: 32px; }
        .widget-title { background: var(--color-primary); color: var(--color-white); padding: 18px; font-size: 20px; margin: 0; }
        .widget-news ul { list-style: none; }
        .widget-news li { border-bottom: 1px solid var(--color-border-light); }
        .widget-news li:last-child { border-bottom: none; }
        .widget-news li a { display: block; padding: 18px; text-decoration: none; position: relative; transition: background-color 0.2s ease; }
        .widget-news li a:hover { background-color: #f9f9f9; }
        .widget-news li h4 { font-size: 16px; font-weight: 700; color: var(--color-text); margin-bottom: 4px; }
        .widget-news li .date { font-size: 13px; color: var(--color-text-secondary); margin-bottom: 8px; display: block; }
        .widget-news li p { font-size: 14px; color: var(--color-text-darker); line-height: 1.5; }
        .widget-news li .arrow-icon {
            position: absolute; right: 18px; top: 50%; transform: translateY(-50%); font-size: 18px; font-weight: bold;
            color: var(--color-text); background: #f0f0f0; border-radius: 4px; width: 24px; height: 24px;
            display: flex; align-items: center; justify-content: center; transition: background-color 0.2s ease, color 0.2s ease;
        }
        .widget-news li a:hover .arrow-icon { background-color: var(--color-primary); color: var(--color-white); }
        .widget-categories ul { list-style: none; }
        .widget-categories li a {
            display: block;
            padding: 12px 18px;
            color: var(--color-text-darker);
            text-decoration: none;
            border-bottom: 1px solid var(--color-border-light);
            font-weight: 600;
            transition: background-color 0.2s ease;
        }
        .widget-categories li:last-child a { border-bottom: none; }
        .widget-categories li a:hover { background-color: #f9f9f9; color: var(--color-primary); }
        .widget-categories li a::after {
            content: '\203A';
            float: right;
            font-weight: bold;
            color: var(--color-text-secondary);
        }

        /* ==== 7. BARU: Style untuk Halaman Detail Proyek ==== */
        .project-video-embed {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
            height: 0;
            overflow: hidden;
            border-radius: 8px;
            background: #000;
            margin-bottom: 24px;
        }
        .project-video-embed iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
        /* Mockup jika tidak ada iframe */
        .project-video-embed .mockup-video {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            background-color: #333;
            color: white;
            font-size: 60px;
        }
        
        .primary-content .lead-paragraph {
            font-size: 1.15em;
            color: var(--color-text-darker);
            font-weight: 400; 
            margin-bottom: 24px;
            border-bottom: 2px solid var(--color-border-light);
            padding-bottom: 24px;
        }
        .primary-content h3 {
            font-size: 22px;
            margin-top: 30px;
            margin-bottom: 16px;
            color: var(--color-primary);
        }
        
        /* Badges Teknologi */
        .tech-badge-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 24px;
        }
        .tech-badge {
            background: var(--color-bg-light);
            border: 1px solid var(--color-border-light);
            color: var(--color-text-darker);
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: 600;
        }
        
        /* Tautan Proyek */
        .project-links-list {
            list-style: none;
            padding: 0;
            margin-bottom: 24px;
        }
        .project-links-list li {
            margin-bottom: 10px;
        }
        .project-links-list a {
            font-weight: 600;
            font-size: 16px;
            text-decoration: none;
        }
        .project-links-list a:hover {
            text-decoration: underline;
        }
        .project-links-list a::before {
            content: '🔗'; /* Emoji link */
            margin-right: 8px;
        }
        .project-links-list a.github::before {
            content: '💻'; /* Emoji laptop */
        }
        .project-links-list a.paper::before {
            content: '📄'; /* Emoji dokumen */
        }
        
        /* Rating & Feedback */
        .star-rating {
            font-size: 24px;
            color: var(--color-accent);
            margin-bottom: 8px;
        }
        .rating-summary {
            font-size: 14px;
            color: var(--color-text-secondary);
            margin-bottom: 20px;
        }
        
        .comment-form .form-group {
            margin-bottom: 16px;
        }
        .comment-form label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
        }
        .comment-form input[type="text"],
        .comment-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--color-border-light);
            border-radius: 6px;
            font-size: 16px;
            font-family: var(--font-body);
        }
        .comment-form textarea {
            min-height: 100px;
            resize: vertical;
        }
        .comment-form button {
            margin-top: 10px;
        }
        
        .comment-list .comment-item {
            border-bottom: 1px solid var(--color-border-light);
            padding: 16px 0;
        }
        .comment-list .comment-item:last-child {
            border-bottom: none;
        }
        .comment-list .comment-author {
            font-weight: 700;
            font-family: var(--font-heading);
            margin-bottom: 4px;
        }
        .comment-list .comment-date {
            font-size: 13px;
            color: var(--color-text-secondary);
            margin-bottom: 8px;
        }
        
        /* Sidebar: Anggota Tim */
        .widget-team-list {
            list-style: none;
            padding: 10px 18px 18px 18px;
        }
        .team-member {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }
        .team-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            background: var(--color-bg-light);
            border: 1px solid var(--color-border-light);
        }
        .team-info h4 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 0;
        }
        .team-info .role {
            font-size: 14px;
            color: var(--color-text-secondary);
        }
        .team-info .contact-link {
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
        }
        .team-info .contact-link:hover {
            text-decoration: underline;
        }

        /* Tombol Kembali */
        .back-link {
            margin-top: 40px;
            display: inline-block;
        }


        /* (Style dari Halaman Lain) */
        .additional-info, .info-grid, .widget-style-card, .project-item, .about-teaser, .facilities-teaser, .facility-grid, .facility-teaser-item {
            /* Style ini ada di CSS, tapi tidak dipakai di halaman ini */
        }

        /* ==== 9. Footer (MODIFIKASI) ==== */
        .site-footer {
            background: var(--color-primary);
            color: var(--color-white);
            padding-top: 60px;
            padding-bottom: 30px;
            font-size: 14px;
        }
        .footer-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 40px; margin-bottom: 40px; }
        .footer-col { color: #dddddd; }
        .footer-col h4 { color: var(--color-accent); margin-bottom: 16px; font-size: 18px; }
        .footer-col p { margin-bottom: 16px; line-height: 1.7; }
        .footer-col ul { list-style: none; }
        .footer-col li { margin-bottom: 8px; }
        .footer-col a { color: #dddddd; }
        .footer-col a:hover { color: var(--color-white); text-decoration: underline; }
        .footer-copyright {
            text-align: center; font-size: 12px; color: #dddddd;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 30px; margin-top: 20px;
        }

        /* MODIFIKASI: Footer Social Links */
        .social-links-footer {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        .social-links-footer a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: var(--color-white);
            border-radius: 50%;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.2s, border-color 0.2s;
        }
        .social-links-footer a:hover {
            background-color: var(--color-white);
            border-color: var(--color-white);
            color: var(--color-primary);
        }
        .policy-links {
            margin-top: 16px;
            padding: 0;
        }
        .policy-links li {
            margin-bottom: 5px;
        }
        .policy-links a {
            font-size: 13px;
            color: #bbbbbb;
        }
        .policy-links a:hover {
            color: var(--color-white);
        }

        /* ==== 10. Scroll to Top Button (SAMA) ==== */
        .scroll-top-btn {
            position: fixed; bottom: 20px; right: 20px;
            background: var(--color-accent); color: var(--color-text);
            width: 44px; height: 44px; border-radius: 50%;
            display: none; justify-content: center; align-items: center;
            font-size: 22px; font-weight: bold; text-decoration: none;
            z-index: 998; box-shadow: 0 2px 5px rgba(0,0,0,0.15);
            transition: background-color 0.2s ease, transform 0.2s ease, opacity 0.3s ease;
        }
        .scroll-top-btn.show { display: flex; opacity: 1; }
        .scroll-top-btn:hover { background: var(--color-accent-hover); color: var(--color-text); transform: scale(1.05); }

        /* ==== 11. Responsive Design ==== */
        @media (max-width: 992px) {
            .main-content-area > .container { flex-direction: column; gap: 60px; }
            .sidebar { width: 100%; }
        }

        @media (max-width: 768px) {
            body { font-size: 15px; }
            .container { padding-left: 16px; padding-right: 16px; }

            /* ---- 11.1 Navigasi Mobile (Overrides) (SAMA) ---- */
            .menu-toggle { display: block; }
            .main-navigation {
                display: none; position: absolute; top: 60px; left: 0; right: 0;
                width: 100%; background: var(--color-primary);
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                border-top: 1px solid rgba(255, 255, 255, 0.1);
            }
            .main-navigation.is-active { display: block; }
            .main-navigation ul { flex-direction: column; gap: 0; }
            .main-navigation li { border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
            .main-navigation li:last-child { border-bottom: none; }
            .main-navigation a { padding: 16px 24px; }
            .main-navigation a::after { display: none; }
            .main-navigation .has-dropdown > a::before { content: '\25B8'; transform: translateY(-50%) rotate(0deg); transition: transform 0.3s ease; }
            .main-navigation .has-dropdown.submenu-open > a::before { transform: translateY(-50%) rotate(90deg); }
            .dropdown-menu {
                display: none; position: static; width: 100%;
                background: var(--color-mobile-submenu-bg);
                border: none; box-shadow: none; border-radius: 0;
                opacity: 1; visibility: visible; transform: none; transition: none;
            }
            .main-navigation .has-dropdown.submenu-open > .dropdown-menu { display: block; }
            .dropdown-menu li { border-top: 1px solid rgba(255, 255, 255, 0.1); border-bottom: none; }
            .dropdown-menu li a { padding-left: 40px; color: #f0f0f0; font-size: 14px; }
            .dropdown-menu li a:hover { background: var(--color-primary); color: var(--color-white); }

            /* MODIFIKASI: CSS Search Bar (Mobile) */
            .main-navigation li.nav-search {
                padding: 16px 24px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }
            .nav-search-form {
                width: 100%;
                margin-top: 0;
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            .nav-search-input {
                width: 100%;
                flex: 1;
            }
            .nav-search-input:focus {
                width: 100%;
            }
            /* AKHIR MODIFIKASI CSS Search Bar (Mobile) */

            /* ---- 11.2 Konten Mobile ---- */
            .hero { height: 320px; } 
            .hero h1 { font-size: 30px; }
            
            .footer-grid { grid-template-columns: 1fr; gap: 30px; }
        }
    </style>
    
</head>
<body id="top">

    <!-- ==== HEADER (NAVIGASI SESUAI PERMINTAAN) ==== -->
    <header class="site-header" id="siteHeader">
        <div class="container">
            <div class="logo-area">
                <a href="../../beranda.php">
                    <img src="../../assets/images/logo-placeholder.png" alt="Logo Departemen" style="height: 50px; width: auto;">
                </a>
            </div>
            
            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="Buka menu navigasi">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <nav class="main-navigation" id="primary-menu">
                <ul>
                    <li><a href="../../beranda.php">Beranda</a></li>
                    
                    <li class="has-dropdown">
                        <a href="../profil.php" class="dropdown-toggle">Profil</a>
                        <ul class="dropdown-menu">
                            <li><a href="../profil.php#visi-misi">Visi & Misi</a></li>
                            <li><a href="../profil.php#sejarah">Sejarah</a></li>
                            <li><a href="../profil.php#struktur">Struktur</a></li>
                            <li><a href="../profil.php#tim">Tim Kami</a></li>
                        </ul>
                    </li>
                    
                    <li class="has-dropdown">
                        <a href="../berita.php" class="dropdown-toggle">Berita & Kegiatan</a>
                        <ul class="dropdown-menu">
                            <li><a href="../berita.php">Berita Terbaru</a></li>
                            <li><a href="../berita.php">Agenda Kegiatan</a></li>
                        </ul>
                    </li>
                    
                    <!-- Tombol Proyek AKTIF -->
                    <li><a href="../proyek.php" aria-current="page">Proyek</a></li>
                    
                    <!-- Tombol Galeri -->
                    <li><a href="../galeri.php">Galeri</a></li>
                    
                    <li><a href="../kontak.php">Kontak</a></li>
                    <li><a href="../../login.php">Login</a></li>

                    <!-- Search Bar -->
                    <li class="nav-search">
                        <form action="../../search.php" method="get" class="nav-search-form">
                            <input type="search" name="q" placeholder="Cari..." aria-label="Cari" class="nav-search-input">
                            <button type="submit" class="nav-search-button" aria-label="Cari">&#128269;</button>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    <!-- ==== AKHIR HEADER ==== -->

    <main>

        <!-- ==== HERO (Judul Proyek Spesifik) ==== -->
        <section class="hero">
            <div class="container">
                <h1>Sistem Deteksi Kemacetan Real-time</h1>
            </div>
        </section>

        <!-- ==== MAIN CONTENT (Layout 2 Kolom) ==== -->
        <div class="main-content-area">
            <div class="container">
                
                <!-- Kolom Konten Utama (Detail Proyek) -->
                <div class="primary-content">
                    
                    <!-- Media: Video Demo (Mockup) -->
                    <div class="project-video-embed">
                        <!-- <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="YouTube video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe> -->
                        <!-- Mockup Tampilan Saja -->
                        <div class="mockup-video">
                            <span>&#9658;</span> <!-- Icon Play -->
                        </div>
                    </div>

                    <!-- Ringkasan -->
                    <p class="lead-paragraph">
                        Proyek mahasiswa ini bertujuan membangun aplikasi mobile yang memanfaatkan data GPS crowdsourced dari pengguna untuk memetakan dan memprediksi titik kemacetan di area metropolitan, membantu pengguna menemukan rute tercepat.
                    </p>
                    
                    <!-- Deskripsi Lengkap -->
                    <h3>Deskripsi Proyek</h3>
                    <p>
                        Aplikasi ini dikembangkan sebagai solusi atas masalah kemacetan kronis di kota-kota besar. Dengan mengumpulkan data lokasi anonim dari pengguna secara real-time, sistem dapat menganalisis kecepatan rata-rata di berbagai ruas jalan.
                    </p>
                    <p>
                        Data yang terkumpul kemudian diproses menggunakan algoritma machine learning sederhana untuk memprediksi potensi kemacetan dalam 30 menit ke depan. Hasilnya ditampilkan dalam bentuk peta interaktif dengan visualisasi heatmap.
                    </p>

                    <!-- Teknologi (Badges) -->
                    <h3>Teknologi yang Digunakan</h3>
                    <div class="tech-badge-container">
                        <span class="tech-badge">React Native</span>
                        <span class="tech-badge">Firebase</span>
                        <span class="tech-badge">Google Maps API</span>
                        <span class="tech-badge">Node.js</span>
                        <span class="tech-badge">Figma</span>
                    </div>

                    <!-- Tautan -->
                    <h3>Tautan Proyek</h3>
                    <ul class="project-links-list">
                        <li><a href="#" class="demo">Lihat Demo Interaktif</a></li>
                        <li><a href="#" class="github">Repository GitHub</a></li>
                        <li><a href="#" class="paper">Publikasi Ilmiah (PDF)</a></li>
                    </ul>

                    <!-- Rating & Feedback -->
                    <h3>Rating & Umpan Balik</h3>
                    <div class="star-rating" title="Rating: 4.5 dari 5 bintang">★★★★☆</div>
                    <div class="rating-summary">(4.5/5 berdasarkan 28 ulasan)</div>
                    
                    <form class="comment-form" action="#" method="post">
                        <h4>Beri Komentar</h4>
                        <div class="form-group">
                            <label for="comment-name">Nama Anda</label>
                            <input type="text" id="comment-name" name="comment_name" required>
                        </div>
                        <div class="form-group">
                            <label for="comment-text">Komentar Anda</label>
                            <textarea id="comment-text" name="comment_text" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn">Kirim Komentar</button>
                    </form>
                    
                    <div class="comment-list">
                        <h3>Komentar (2)</h3>
                        <div class="comment-item">
                            <p class="comment-author">Andi Budianto</p>
                            <p class="comment-date">2 November 2025</p>
                            <p>Aplikasi yang sangat inovatif dan bermanfaat untuk penggunaan sehari-hari. Sukses selalu untuk tim pengembang!</p>
                        </div>
                        <div class="comment-item">
                            <p class="comment-author">Citra Lestari</p>
                            <p class="comment-date">1 November 2025</p>
                            <p>Tampilannya bersih dan mudah digunakan. Prediksinya cukup akurat untuk area Surabaya pusat.</p>
                        </div>
                    </div>

                    <!-- Tombol Kembali -->
                    <a href="../proyek.php" class="btn btn-secondary back-link">&larr; Kembali ke Katalog Proyek</a>

                </div>
                
                <!-- Kolom Sidebar (Anggota Tim & Proyek Terkait) -->
                <aside class="sidebar">
                    
                    <!-- Anggota Tim -->
                    <div class="widget">
                        <h3 class="widget-title">Anggota Tim</h3>
                        <ul class="widget-team-list">
                            <li class="team-member">
                                <img src="../../assets/images/avatar-1.jpg" alt="Avatar Anggota 1" class="team-avatar">
                                <div class="team-info">
                                    <h4>Ahmad Fadhil</h4>
                                    <span class="role">Project Manager</span>
                                    <a href="#" class="contact-link">LinkedIn</a>
                                </div>
                            </li>
                            <li class="team-member">
                                <img src="../../assets/images/avatar-2.jpg" alt="Avatar Anggota 2" class="team-avatar">
                                <div class="team-info">
                                    <h4>Siti Nurhaliza</h4>
                                    <span class="role">UI/UX Designer</span>
                                    <a href="#" class="contact-link">LinkedIn</a>
                                </div>
                            </li>
                            <li class="team-member">
                                <img src="../../assets/images/avatar-3.jpg" alt="Avatar Anggota 3" class="team-avatar">
                                <div class="team-info">
                                    <h4>Bagus Wijaya</h4>
                                    <span class="role">Lead Developer</span>
                                    <a href="#" class="contact-link">LinkedIn</a>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Proyek Terkait -->
                    <div class="widget widget-news">
                        <h3 class="widget-title">Proyek Terkait</h3>
                        <ul>
                            <li>
                                <a href="#">
                                    <h4>Dashboard Visualisasi Data COVID-19</h4>
                                    <span class="date">Kategori: Analitika Data</span>
                                    <p>Dashboard interaktif untuk memantau penyebaran kasus...</p>
                                    <span class="arrow-icon" aria-hidden="true">&rarr;</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <h4>Aplikasi E-Commerce Terintegrasi UMKM</h4>
                                    <span class="date">Kategori: Aplikasi Mobile</span>
                                    <p>Pengembangan platform e-commerce untuk UMKM lokal...</p>
                                    <span class="arrow-icon" aria-hidden="true">&rarr;</span>
                                </a>
                            </li>
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
                    <img src="../../assets/images/logo-footer-placeholder.png" alt="Logo Lab Polinema Footer" style="height: 60px; margin-bottom: 20px;">
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
                        <li><a href="../../beranda.php">Beranda</a></li>
                        <li><a href="../profil.php">Profil</a></li>
                        <li><a href="../berita.php">Berita & Kegiatan</a></li>
                        <li><a href="../proyek.php">Proyek</a></li>
                        <li><a href="../galeri.php">Galeri</a></li>
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

    <script>
        /* ---
           File: script.js
           Deskripsi: Interaksi vanilla JS (Hamburger, Sticky Header, Scroll-to-Top, Dropdown).
        --- */

        document.addEventListener("DOMContentLoaded", function() {

            /**
             * 1. Toggle Menu Hamburger (Mobile)
             */
            const menuToggle = document.querySelector('.menu-toggle');
            const navMenu = document.querySelector('#primary-menu');

            if (menuToggle && navMenu) {
                menuToggle.addEventListener('click', function() {
                    navMenu.classList.toggle('is-active');
                    const isExpanded = navMenu.classList.contains('is-active');
                    menuToggle.setAttribute('aria-expanded', isExpanded);
                });
            }

            /**
             * 2. Efek Shadow pada Sticky Header saat scroll
             */
            const header = document.querySelector('#siteHeader');
            if (header) {
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 10) {
                        header.classList.add('header-scrolled');
                    } else {
                        header.classList.remove('header-scrolled');
                    }
                });
            }

            /**
             * 3. Tombol Scroll-to-Top
             */
            const scrollTopBtn = document.querySelector('#scrollTopBtn');
            
            if (scrollTopBtn) {
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 300) {
                        scrollTopBtn.classList.add('show');
                    } else {
                        scrollTopBtn.classList.remove('show');
                    }
                });

                scrollTopBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }

            /**
             * 4. Dropdown Menu Toggle (HANYA UNTUK MOBILE)
             */
            const dropdownToggles = document.querySelectorAll('.main-navigation .dropdown-toggle');

            dropdownToggles.forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    if (window.innerWidth <= 768) {
                        e.preventDefault(); 
                        const parentLi = this.parentElement;
                        parentLi.classList.toggle('submenu-open');
                    }
                });
            });

        });
    </script>

</body>
</html>