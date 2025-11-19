<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // ✔ aman
}
/**
 * menu/components/navbar.php
 * Reusable navbar + improved live-search UI
 *
 * Notes:
 * - Keep DB include only for AJAX requests.
 * - Include navbar.css instead of internal <style>
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
            // Berita
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

            // Proyek
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

            // Galeri
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
    <!-- Link CSS eksternal -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>menu/components/navbar.css">

    <header class="site-header" id="siteHeader">
        <div class="container">
            <div class="logo-area">
                <a href="<?php echo BASE_URL; ?>beranda.php">
                    <img src="<?php echo BASE_URL; ?>assets/images/LAB MUTIMEDIA V2_TSP.png" alt="Logo" style="height:50px; width:auto;">
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
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- sudah login -->
                    <?php else: ?>
                        <li><a href="<?php echo BASE_URL; ?>menu/login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

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

        <!-- Script search AJAX -->
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

            input.addEventListener('keydown', function(e){
                const items = box.querySelectorAll('.result-item');
                if (!items.length) return;
                if (e.key === 'ArrowDown') { e.preventDefault(); active = Math.min(active + 1, items.length - 1); updateActive(items); }
                else if (e.key === 'ArrowUp') { e.preventDefault(); active = Math.max(active - 1, 0); updateActive(items); }
                else if (e.key === 'Enter') { if (active >= 0 && items[active]) { e.preventDefault(); window.location.href = items[active].getAttribute('href'); } }
                else if (e.key === 'Escape') { clearBox(); }
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
