<?php include 'components/header.php'; ?>

<?php
// Mengambil Statistik Menggunakan PostgreSQL Count
$total_berita = $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn();
$total_proyek = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
// Cek pesan yang belum dibaca (is_read = false)
$pesan_baru = $pdo->query("SELECT COUNT(*) FROM feedback WHERE is_read = false")->fetchColumn();
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
            <h3><?= $total_proyek ?></h3>
            <p>Proyek Mahasiswa</p>
        </div>
        <div class="stat-icon"><i class="fas fa-laptop-code"></i></div>
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