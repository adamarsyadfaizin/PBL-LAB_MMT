<?php
/**
 * menu/components/navbar.php
 * Reusable navbar + improved live-search UI (updated: visible text + magnifier icon)
 *
 * Notes:
 * - Keep DB include only for AJAX requests.
 * - Replace existing file with this one.
 */

/* ---------- BASE URL ---------- */
if (!defined('BASE_URL')) {
    define('BASE_URL', '/PBL/');
}

/* ---------- AJAX SEARCH HANDLER ---------- */
if (isset($_GET['ajax_search'])) {
    require_once __DIR__ . '/../../config/db.php';

    header('Content-Type: application/json; charset=utf-8');
    $q = trim((string)($_GET['q'] ?? ''));
    $results = [];

    if ($q !== '') {
        $pat = "%$q%";
        try {
            // Berita (limit 4)
            $sqlNews = "SELECT id, title, slug, cover_image FROM news
                        WHERE status = 'published' AND (title ILIKE ? OR summary ILIKE ?)
                        ORDER BY created_at DESC LIMIT 4";
            $stmt = $pdo->prepare($sqlNews);
            $stmt->execute([$pat, $pat]);
            while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $results[] = [
                    'type' => 'Berita',
                    'title' => $r['title'],
                    'image' => $r['cover_image'] ?: null,
                    'url' => BASE_URL . "menu/menu-detail-berita/detail-berita.php?slug=" . rawurlencode($r['slug'])
                ];
            }

            // Proyek (limit 4)
            $sqlProj = "SELECT id, title, slug, cover_image FROM projects
                        WHERE status = 'published' AND (title ILIKE ? OR summary ILIKE ?)
                        ORDER BY created_at DESC LIMIT 4";
            $stmt = $pdo->prepare($sqlProj);
            $stmt->execute([$pat, $pat]);
            while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $results[] = [
                    'type' => 'Proyek',
                    'title' => $r['title'],
                    'image' => $r['cover_image'] ?: null,
                    'url' => BASE_URL . "menu/menu-proyek-detail/detail-proyek.php?slug=" . rawurlencode($r['slug'])
                ];
            }

            // Galeri (limit 4)
            $sqlGal = "SELECT id, title, image_path FROM galleries
                       WHERE status = 'published' AND (title ILIKE ? OR description ILIKE ?)
                       ORDER BY created_at DESC LIMIT 4";
            $stmt = $pdo->prepare($sqlGal);
            $stmt->execute([$pat, $pat]);
            while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $results[] = [
                    'type' => 'Galeri',
                    'title' => $r['title'],
                    'image' => $r['image_path'] ?: null,
                    'url' => BASE_URL . "menu/menu-detail-galeri/galeri-detail.php?id=" . rawurlencode($r['id'])
                ];
            }

        } catch (PDOException $e) {
            echo json_encode(['error' => 'db_error', 'msg' => $e->getMessage()]);
            exit;
        }
    }

    echo json_encode(['q' => $q, 'results' => $results]);
    exit;
}

/* ---------- RENDER NAVBAR ---------- */
function renderNavbar($currentPage = '') {
    $ajaxEndpoint = rtrim(BASE_URL, "/") . "/menu/components/navbar.php?ajax_search=1";
    ?>
    <header class="site-header" id="siteHeader">
        <div class="container">
            <div class="logo-area">
                <a href="<?php echo BASE_URL; ?>beranda.php">
                    <img src="<?php echo BASE_URL; ?>assets/images/logo-placeholder.png" alt="Logo" style="height:50px; width:auto;">
                </a>
            </div>

            <nav class="main-navigation" id="primary-menu">
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>beranda.php" <?php echo ($currentPage == 'beranda') ? 'aria-current="page"' : ''; ?>>Beranda</a></li>
                    <li><a href="<?php echo BASE_URL; ?>menu/profil.php" <?php echo ($currentPage == 'profil') ? 'aria-current="page"' : ''; ?>>Profil</a></li>
                    <li><a href="<?php echo BASE_URL; ?>menu/berita.php" <?php echo ($currentPage == 'berita') ? 'aria-current="page"' : ''; ?>>Berita & Kegiatan</a></li>
                    <li><a href="<?php echo BASE_URL; ?>menu/proyek.php" <?php echo ($currentPage == 'proyek') ? 'aria-current="page"' : ''; ?>>Proyek</a></li>
                    <li><a href="<?php echo BASE_URL; ?>menu/galeri.php" <?php echo ($currentPage == 'galeri') ? 'aria-current="page"' : ''; ?>>Galeri</a></li>
                    <li><a href="<?php echo BASE_URL; ?>menu/kontak.php" <?php echo ($currentPage == 'kontak') ? 'aria-current="page"' : ''; ?>>Kontak</a></li>
                    <li><a href="<?php echo BASE_URL; ?>menu/login.php">Login</a></li>
                </ul>
            </nav>

            <!-- Search area with new style -->
            <div class="nav-search">
                <div class="nav-search-form" role="search" aria-label="Pencarian situs">
                    <input id="nav-search-input" class="nav-search-input" type="search" placeholder="Cari berita / proyek / galeri..." aria-label="Cari">
                    <button type="button" id="nav-search-button" class="nav-search-button" aria-label="Cari">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
                            <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
                <div id="nav-search-results" class="nav-search-results" role="listbox" aria-label="Hasil pencarian" aria-hidden="true"></div>
            </div>

            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="Buka menu navigasi">
                <span></span><span></span><span></span>
            </button>
        </div>

        <!-- Scoped styles -->
        <style>
        /* === NEW NAV SEARCH STYLES === */
        .nav-search {
            display: flex;
            align-items: center;
            margin-left: 16px;
            position: relative;
        }
        .nav-search-form {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 999px;
            overflow: hidden;
            height: 36px;
            margin-top: 4px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .nav-search-input {
            border: none;
            background: transparent;
            padding: 0 16px;
            font-size: 14px;
            font-family: 'Open Sans', sans-serif;
            color: var(--color-white);
            height: 100%;
            width: 160px;
            transition: width 0.3s ease;
        }
        .nav-search-input:focus {
            width: 200px;
            outline: none;
        }
        .nav-search-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
            opacity: 1;
        }
        .nav-search-button {
            border: none;
            background: transparent;
            color: rgba(255, 255, 255, 0.7);
            cursor: pointer;
            height: 100%;
            padding: 0 16px;
            font-size: 16px;
            transition: color 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .nav-search-button:hover {
            color: var(--color-white);
        }

        /* Dropdown hasil pencarian */
        .nav-search-results {
            position: absolute;
            left: 0;
            right: 0;
            top: calc(100% + 10px);
            background: rgba(15,23,42,0.98);
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.08);
            box-shadow: 0 14px 40px rgba(2,6,23,0.5);
            z-index: 1200;
            max-height: 340px;
            overflow-y: auto;
            display: none;
            padding: 6px;
            backdrop-filter: blur(8px);
        }

        /* Item hasil */
        .nav-search-results .result-item {
            display: flex;
            gap: 10px;
            align-items: center;
            padding: 10px;
            text-decoration: none;
            color: #e2e8f0;
            border-radius: 8px;
            transition: background 0.2s ease, transform 0.15s ease;
        }
        .nav-search-results .result-item:hover,
        .nav-search-results .result-item.active {
            background: rgba(96,165,250,0.15);
            transform: scale(1.02);
        }

        .nav-search-results .result-thumb {
            width: 60px;
            height: 44px;
            flex: 0 0 60px;
            border-radius: 6px;
            object-fit: cover;
            background: #475569;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }

        .nav-search-results .result-meta { font-size: 13px; line-height: 1.2; }
        .nav-search-results .result-type { font-weight: 700; font-size: 11px; color: #93c5fd; margin-bottom: 3px; text-transform: uppercase; }
        .nav-search-results .result-title { font-weight: 600; color: #f1f5f9; }

        .nav-search-results .result-empty {
            padding: 14px;
            color: #94a3b8;
            text-align: center;
            font-style: italic;
        }

        /* Responsif */
        @media (max-width: 900px) {
            .nav-search { margin-left: 12px; }
            .nav-search-input { width: 140px; }
            .nav-search-input:focus { width: 180px; }
        }
        @media (max-width: 768px) {
            .nav-search { margin-left: 8px; }
            .nav-search-input { width: 120px; font-size: 13px; padding: 0 12px; }
            .nav-search-input:focus { width: 160px; }
            .nav-search-button { padding: 0 12px; }
        }
        @media (max-width: 520px) {
            .nav-search { margin-left: 6px; }
            .nav-search-input { width: 100px; }
            .nav-search-input:focus { width: 140px; }
            .nav-search-results .result-thumb { width: 48px; height: 36px; }
        }

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

        /* ---- 3.1 Dropdown Menu (Desktop) ---- */
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
        </style>

        <script>
        (function(){
            const input = document.getElementById('nav-search-input');
            const box = document.getElementById('nav-search-results');
            const button = document.getElementById('nav-search-button');
            const ajaxUrl = '<?php echo $ajaxEndpoint; ?>';
            let timer = null, results = [], active = -1;

            function escapeHtml(s) {
                return (s||'').replace(/[&<>"'`=\/]/g, function(ch){
                    return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'&#47;','`':'&#96;','=':'&#61;'})[ch];
                });
            }

            function clearBox() {
                results = []; active = -1; box.innerHTML = ''; box.style.display = 'none'; box.setAttribute('aria-hidden','true');
            }

            function render(data) {
                results = data.results || [];
                if (!results.length) {
                    box.innerHTML = '<div class="result-empty">Tidak ada hasil</div>';
                    box.style.display = 'block';
                    box.setAttribute('aria-hidden','false');
                    return;
                }
                const html = results.map((it, idx) => {
                    const img = it.image ? '<img src="'+escapeHtml(it.image)+'" class="result-thumb" alt="">' : '<div class="result-thumb"></div>';
                    return '<a href="'+escapeHtml(it.url)+'" class="result-item" data-index="'+idx+'">' +
                           img +
                           '<div class="result-meta"><div class="result-type">'+escapeHtml(it.type)+'</div><div class="result-title">'+escapeHtml(it.title)+'</div></div>' +
                           '</a>';
                }).join('');
                box.innerHTML = html;
                box.style.display = 'block';
                box.setAttribute('aria-hidden','false');
            }

            function fetchResults(q) {
                if (!q || q.trim()==='') { clearBox(); return; }
                fetch(ajaxUrl + '&q=' + encodeURIComponent(q), { credentials: 'same-origin' })
                    .then(r => r.json())
                    .then(json => render(json))
                    .catch(() => clearBox());
            }

            input.addEventListener('input', function(e){
                const q = e.target.value;
                if (timer) clearTimeout(timer);
                timer = setTimeout(() => fetchResults(q), 220);
            });

            // keyboard navigation
            input.addEventListener('keydown', function(e){
                const items = box.querySelectorAll('.result-item');
                if (!items.length) return;
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    active = Math.min(active + 1, items.length - 1);
                    updateActive(items);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    active = Math.max(active - 1, 0);
                    updateActive(items);
                } else if (e.key === 'Enter') {
                    if (active >= 0 && items[active]) {
                        e.preventDefault();
                        window.location.href = items[active].getAttribute('href');
                    }
                } else if (e.key === 'Escape') {
                    clearBox();
                }
            });

            function updateActive(items) {
                items.forEach((it, i) => it.classList.toggle('active', i === active));
                if (items[active]) items[active].scrollIntoView({ block: 'nearest' });
            }

            box.addEventListener('click', function(e){
                const a = e.target.closest('.result-item');
                if (a) clearBox();
            });

            document.addEventListener('click', function(e){
                if (!e.target.closest('.nav-search')) clearBox();
            });

            // focus input when clicking button
            if (button) button.addEventListener('click', function(){ input.focus(); });

            input.addEventListener('focus', function(){
                if (box.innerHTML.trim() !== '') {
                    box.style.display = 'block';
                    box.setAttribute('aria-hidden','false');
                }
            });
        })();
        </script>
    </header>
    <?php
} // end renderNavbar
?>