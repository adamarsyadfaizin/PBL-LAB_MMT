<?php include 'components/header.php'; ?>

<?php
// Ambil statistik menggunakan function get_lab_stats()
try {
    // Call function get_lab_stats() untuk data tercepat
    $stmt = $pdo->query("SELECT * FROM get_lab_stats()");
    $stats = $stmt->fetch();
    
    $total_berita = $stats['total_news'];
    $total_proyek = $stats['total_projects'];
    $total_members = $stats['total_members'];
    $total_comments = $stats['total_comments'];
    $pesan_baru = $stats['total_unread_feedback'];
    
    // Ambil data tambahan dari materialized views
    $pdo->exec("REFRESH MATERIALIZED VIEW CONCURRENTLY mv_lab_dashboard_stats");
    $mv_stats = $pdo->query("SELECT * FROM mv_lab_dashboard_stats")->fetch();
    $total_kegiatan = $mv_stats['total_kegiatan'];
    $total_media = $mv_stats['total_media'];
    $total_videos = $mv_stats['total_videos'];
    
} catch (Exception $e) {
    // Fallback ke query biasa jika function gagal
    $total_berita = $pdo->query("SELECT COUNT(*) FROM news WHERE status = 'published'")->fetchColumn();
    $total_proyek = $pdo->query("SELECT COUNT(*) FROM projects WHERE status IN ('published', '1')")->fetchColumn();
    $pesan_baru = $pdo->query("SELECT COUNT(*) FROM feedback WHERE is_read = false")->fetchColumn();
    $total_kegiatan = $pdo->query("SELECT COUNT(*) FROM news WHERE type = 'kegiatan' AND status = 'published'")->fetchColumn();
    $total_members = $pdo->query("SELECT COUNT(*) FROM members")->fetchColumn();
    $total_media = $pdo->query("SELECT COUNT(*) FROM media_assets")->fetchColumn();
    $total_videos = $pdo->query("SELECT COUNT(*) FROM media_assets WHERE type = 'video'")->fetchColumn();
    $total_comments = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'approved'")->fetchColumn();
}
?>

<div class="header-flex">
    <h1>Dashboard Overview</h1>
    <div>Halo, <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></strong></div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <h3><?= $total_berita ?></h3>
            <p>Total Berita</p>
        </div>
        <div class="stat-icon"><i class="fas fa-newspaper"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3><?= $total_kegiatan ?></h3>
            <p>Kegiatan Lab</p>
        </div>
        <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3><?= $total_proyek ?></h3>
            <p>Proyek Mahasiswa</p>
        </div>
        <div class="stat-icon"><i class="fas fa-laptop-code"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3><?= $total_members ?></h3>
            <p>Total Anggota</p>
        </div>
        <div class="stat-icon"><i class="fas fa-users"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3><?= $total_media ?></h3>
            <p>Media Gallery</p>
        </div>
        <div class="stat-icon"><i class="fas fa-photo-video"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3><?= $total_videos ?></h3>
            <p>Video</p>
        </div>
        <div class="stat-icon"><i class="fas fa-video"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3><?= $total_comments ?></h3>
            <p>Komentar</p>
        </div>
        <div class="stat-icon"><i class="fas fa-comments"></i></div>
    </div>
    <div class="stat-card" style="border-left: 4px solid var(--danger);">
        <div class="stat-info">
            <h3><?= $pesan_baru ?></h3>
            <p>Pesan Baru</p>
        </div>
        <div class="stat-icon" style="color: var(--danger);"><i class="fas fa-envelope"></i></div>
    </div>
</div>

<div class="table-card">
    <h3 style="margin-bottom: 15px;">Pesan Terbaru</h3>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Subjek</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Query Limit di PostgreSQL
            $stmt = $pdo->query("SELECT * FROM feedback ORDER BY created_at DESC LIMIT 5");
            while($row = $stmt->fetch()):
            ?>
            <tr style="<?= $row['is_read'] ? '' : 'background-color: #f0fdf4;' ?>">
                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                <td><?= htmlspecialchars($row['subjek']) ?></td>
                <td><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
                <td><a href="pesan.php" class="btn btn-sm btn-primary">Lihat</a></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'components/footer.php'; ?>