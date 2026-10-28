<?php 
// pesan.php - Kotak masuk pesan dari formulir kontak
include 'components/header.php';

// --- LOGIC PHP ---

// 1. Refresh halaman
if(isset($_GET['refresh'])) {
    header('Location: pesan.php');
    exit;
}

// 2. Tandai sudah dibaca
if(isset($_GET['mark_read'])) {
    if (!csrfValid($_GET['csrf'] ?? null)) {
        denyAccess('Token keamanan tidak valid. Muat ulang halaman.');
    }

    $stmt = $pdo->prepare("UPDATE feedback SET is_read = true WHERE id = ?");
    $stmt->execute([(int) $_GET['mark_read']]);
    header('Location: pesan.php');
    exit;
}

// 3. Hapus satu pesan
if(isset($_GET['delete'])) {
    if (!csrfValid($_GET['csrf'] ?? null)) {
        denyAccess('Token keamanan tidak valid. Muat ulang halaman.');
    }

    $stmt = $pdo->prepare("DELETE FROM feedback WHERE id = ?");
    $stmt->execute([(int) $_GET['delete']]);
    header('Location: pesan.php');
    exit;
}

// 4. Hapus SEMUA pesan
if(isset($_GET['delete_all'])) {
    if (!csrfValid($_GET['csrf'] ?? null)) {
        denyAccess('Token keamanan tidak valid. Muat ulang halaman.');
    }

    $pdo->exec("DELETE FROM feedback");
    autoRefreshAfterCRUD($pdo, 'feedback');
    header('Location: pesan.php');
    exit;
}

$csrf = csrfToken();
?>

<style>
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
    .modal-content { background-color: white; margin: 2% auto; padding: 0; border-radius: 8px; width: 90%; max-width: 850px; max-height: 90vh; overflow: hidden; }
    .modal-header { padding: 15px 20px; background: #007bff; color: white; display: flex; justify-content: space-between; align-items: center; }
    .modal-header h3 { margin: 0; font-size: 1.2em; }
    .close { color: white; font-size: 28px; font-weight: bold; cursor: pointer; background: none; border: none; }
    .modal-body { padding: 0; max-height: calc(90vh - 60px); overflow-y: auto; }
    .table-card { background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden; }
    .table-card table { width: 100%; border-collapse: collapse; }
    .table-card th { background: #f8f9fa; padding: 12px 15px; text-align: left; font-weight: bold; border-bottom: 2px solid #dee2e6; }
    .table-card td { padding: 12px 15px; border-bottom: 1px solid #dee2e6; }
    .table-card tbody tr:hover { background-color: #f8f9fa; }
    .unread { background-color: #f0fdf4 !important; font-weight: bold; }
    .view-detail { color: #007bff; text-decoration: none; cursor: pointer; }
    .view-detail:hover { text-decoration: underline; }
    .badge { background: #dc3545; color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px; }
</style>

<div class="header-flex">
    <h1>Pesan Masuk</h1>
    <div>
        <?php
        $unreadCount = $pdo->query("SELECT COUNT(*) FROM feedback WHERE is_read = false")->fetchColumn();
        if($unreadCount > 0): ?>
            <span class="badge"><?= (int) $unreadCount ?> belum dibaca</span>
        <?php endif; ?>
        
        <button onclick="refreshData()" class="btn btn-primary" style="margin-left: 10px;">Refresh</button>
        
        <a href="?delete_all=true&csrf=<?= $csrf ?>" 
           class="btn btn-danger" 
           style="margin-left: 10px;"
           onclick="return confirm('PERINGATAN: Apakah Anda yakin ingin menghapus SEMUA pesan?\n\nTindakan ini tidak dapat dibatalkan!')">
           Hapus Semua
        </a>
    </div>
</div>

<div id="detailModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detail Pesan</h3>
            <button class="close">&times;</button>
        </div>
        <div class="modal-body" id="modalBody"></div>
    </div>
</div>

<div class="table-card">
    <table>
        <thead>
            <tr>
                <th>Pengirim</th>
                <th>Email</th>
                <th>Subjek</th>
                <th>Pesan</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT *, 
                             EXTRACT(EPOCH FROM created_at) as created_epoch, 
                             LEFT(pesan, 50) as pesan_preview 
                      FROM feedback 
                      ORDER BY created_at DESC";
            
            $stmt = $pdo->query($query);
            $rows = $stmt->fetchAll();
            
            foreach ($rows as $row):
                $isUnread = !$row['is_read'];
                $truncatedMsg = $row['pesan_preview'] ?? substr($row['pesan'], 0, 50);
                if (mb_strlen($row['pesan']) > 50) $truncatedMsg .= '...';
            ?>
            <tr class="<?= $isUnread ? 'unread' : '' ?>">
                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['subjek']) ?></td>
                <td>
                    <a href="#" class="view-detail" data-id="<?= (int) $row['id'] ?>">
                        <?= htmlspecialchars($truncatedMsg) ?>
                    </a>
                </td>
                <td><?= date('d M Y',(int) $row['created_epoch']) ?></td>
                <td style="white-space: nowrap;">
                    <?php if($isUnread): ?>
                        <a href="?mark_read=<?= (int) $row['id'] ?>&csrf=<?= $csrf ?>" class="btn btn-sm btn-primary" title="Tandai sudah dibaca">Baca</a>
                    <?php endif; ?>
                    <a href="?delete=<?= (int) $row['id'] ?>&csrf=<?= $csrf ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus pesan ini?')" title="Hapus">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if (empty($rows)): ?>
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px;">
                    Tidak ada pesan masuk
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
const modal = document.getElementById('detailModal');
const closeBtn = document.querySelector('.close');
const modalBody = document.getElementById('modalBody');

// Buka modal
document.addEventListener('click', function(e) {
    if(e.target.closest('.view-detail')) {
        e.preventDefault();
        const link = e.target.closest('.view-detail');
        const id = link.getAttribute('data-id');
        
        modalBody.innerHTML = '<div style="padding: 20px; text-align: center;">Memuat...</div>';
        modal.style.display = 'block';
        
        fetch(`get_feedback_detail_mv.php?id=${id}`)
            .then(response => response.text())
            .then(data => { modalBody.innerHTML = data; })
            .catch(error => { modalBody.innerHTML = '<div style="padding: 20px; text-align: center; color: #dc3545;">Error</div>'; });
    }
});

// Tutup modal
closeBtn.addEventListener('click', () => { modal.style.display = 'none'; });
window.addEventListener('click', (e) => { if (e.target == modal) modal.style.display = 'none'; });

// Refresh
function refreshData() {
    window.location.href = '?refresh=true';
}

// ESC key
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') modal.style.display = 'none'; });
</script>

<?php include 'components/footer.php'; ?>
